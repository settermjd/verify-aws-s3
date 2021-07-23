<?php

use DI\Container;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToWriteFile;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twilio\Rest\Client;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$container = new Container();
AppFactory::setContainer($container);

$container->set('known_participants', fn() => json_decode(
    file_get_contents(__DIR__ . '/../data/known_participants.json'),
    TRUE
));
$container->set('view', fn() => Twig::create(__DIR__ . '/../data/templates'));
$container->set('twilioClient', fn() => new Client($_ENV['TWILIO_ACCOUNT_SID'], $_ENV['TWILIO_AUTH_TOKEN']));
$container->set('allowedFileExtensions', ['jpg', 'jpeg', 'png']);
$container->set('config', [
    'allowedFileExtensions' => ['jpg', 'jpeg', 'png'],
    'uploadDir' => __DIR__ . '/../data/uploads/'
]);

$container->set('s3Client', function () {
    $client = new Aws\S3\S3Client([
        'credentials' => [
            'key'    => $_ENV['AMAZON_S3_ACCESS_KEY'],
            'secret' => $_ENV['AMAZON_S3_SECRET_KEY']
        ],
        'region' => 'eu-central-1',
        'version' => 'latest',
    ]);
    // The internal adapter
    $adapter = new League\Flysystem\AwsS3V3\AwsS3V3Adapter(
        $client,                                // S3Client
        'settermjd-lats-image-data',     // Bucket name
    );

    // The FilesystemOperator
    return new League\Flysystem\Filesystem($adapter);
});

$app = AppFactory::create();

// Add Twig-View Middleware
$app->add(TwigMiddleware::createFromContainer($app));

$app->map(['GET', 'POST'], '/', function (Request $request, Response $response, array $args) {
    $view = $this->get('view');
    $template = 'index.html.twig';

    if ($request->getMethod() === 'POST') {
        $username = $request->getParsedBody()['username'];
        if (array_key_exists($username, $this->get('known_participants'))) {
            $_SESSION['username'] = $username;

            $twilioClient = $this->get('twilioClient');
            $twilioClient
                ->verify
                ->v2
                ->services($_ENV['VERIFY_SERVICE_SID'])
                ->verifications
                ->create($this->get('known_participants')[$username], "sms");

            return $response
                ->withHeader('Location', '/verify')
                ->withStatus(302);
        }

        return $view->render($response, $template, ['error' => 'User not found. Please try again.']);
    }

    return $view->render($response, $template);

});

$app->map(['GET', 'POST'], '/verify', function (Request $request, Response $response, array $args) {
    $username = $_SESSION['username'];
    $phoneNumber = $this->get('known_participants')[$username];
    $template = 'verify.html.twig';
    $view = $this->get('view');

    if ($request->getMethod() === 'POST') {
        $verificationCode = $request->getParsedBody()['verification_code'];
        $twilioClient = $this->get('twilioClient');

        $verification = $twilioClient
            ->verify
            ->v2
            ->services($_ENV['VERIFY_SERVICE_SID'])
            ->verificationChecks
            ->create($verificationCode, ["to" => $phoneNumber]);

        return ($verification->status === 'approved')
            ? $response
                ->withHeader('Location', '/upload')
                ->withStatus(302)
            : $view->render($response, $template, ['error' => 'Invalid verification code. Please try again.']);
    }

    return $view->render($response, $template);
});

$app->map(['GET', 'POST'], '/upload', function (Request $request, Response $response, array $args) {
    $templateFile = 'upload.html.twig';
    $view = $this->get('view');

    if ($request->getMethod() === 'POST') {
        $file = $request->getUploadedFiles()['file'];

        $hasValidExtension = in_array(
            pathinfo($file->getClientFilename(), PATHINFO_EXTENSION),
            $this->get('allowedFileExtensions'),
            true
        );

        if (! $hasValidExtension) {
            return $view->render($response, $templateFile, ['error' => 'Please upload a Jpeg or a PNG file.']);
        }

        if ($file->getError() === UPLOAD_ERR_OK) {
            $file->moveTo($this->get('config')['uploadDir'] . $file->getClientFilename());
        }

        try {
            $this->get('s3Client')
                ->writeStream(
                    '/uploads/' . $file->getClientFilename(),
                    fopen($this->get('config')['uploadDir'] . $file->getClientFilename(), 'rb')
                );
            return $response
                ->withHeader('Location', '/success')
                ->withStatus(302);
        } catch (FilesystemException | UnableToWriteFile $e) {
            return $view->render($response, $templateFile, ['error' => $e->getMessage()]);
        }
    }

    return $view->render($response, $templateFile);
});

$app->get('/success', function (Request $request, Response $response, array $args) {
    return $this->get('view')->render($response, 'success.html.twig');
});

$app->run();
