<?php

use Aws\S3\S3Client;
use DI\Container;
use League\Flysystem\Filesystem;
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

$container->set('view', function() {
    return Twig::create(__DIR__ . '/../data/templates', ['cache' => __DIR__ . '/../data/cache']);
});

$container->set('known_participants', function() {
    return json_decode(file_get_contents(__DIR__ . '/../data/known_participants.json'), TRUE);
});

$container->set('twilioClient', function() {
    return new Client($_ENV['TWILIO_ACCOUNT_SID'], $_ENV['TWILIO_AUTH_TOKEN']);
});

$container->set('allowedFileExtensions', ['jpg', 'jpeg', 'png']);

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

class verifier
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function sendVerificationPasscode(string $username, string $phoneNumber): void
    {
        $this->client
            ->verify
            ->v2
            ->services($_ENV['VERIFY_SERVICE_SID'])
            ->verifications
            ->create($phoneNumber, "sms");
    }

    public function checkVerificationPasscode(string $phoneNumber, string $token): bool
    {
        $verification = $this->client
            ->verify
            ->v2
            ->services($_ENV['VERIFY_SERVICE_SID'])
            ->verificationChecks
            ->create($token, ["to" => $phoneNumber]);

        return $verification->status === 'approved';
    }
}

$app->map(['GET', 'POST'], '/', function (Request $request, Response $response, array $args) {
    if ($request->getMethod() === 'POST') {
        // set username in session
        $username = $request->getParsedBody()['username'];
        if (array_key_exists($username, $this->get('known_participants'))) {
            $_SESSION['username'] = $username;
            (new verifier($this->get('twilioClient')))
                ->sendVerificationPasscode($username, $this->get('known_participants')[$username]);
            return $response
                ->withHeader('Location', '/verify')
                ->withStatus(302);
        }

        $error = "User not found. Please try again.";
        return $this
            ->get('view')
            ->render($response, 'index.html.twig', ['error' => $error]);
    }

    return $this
        ->get('view')
        ->render($response, 'index.html.twig');

})->setName('default');

$app->map(['GET', 'POST'], '/verify', function (Request $request, Response $response, array $args) {
    $username = $_SESSION['username'];
    $phoneNumber = $this->get('known_participants')[$username];
    $error = '';

    if ($request->getMethod() === 'POST') {
        $verificationCode = $request->getParsedBody()['verificationcode'];
        $validToken = (new verifier($this->get('twilioClient')))->checkVerificationPasscode($phoneNumber, $verificationCode);
        return ($validToken)
            ? $this
                ->get('view')
                ->render($response, 'upload.html.twig', ['username' => $username])
            : $this
                ->get('view')
                ->render($response, 'verify.html.twig', ['error' => 'Invalid verification code. Please try again.']);
    }
    return $this
        ->get('view')
        ->render($response, 'verify.html.twig');
});

$app->map(['GET', 'POST'], '/upload', function (Request $request, Response $response, array $args) {
    if ($request->getMethod() === 'POST') {
        /** @var \Psr\Http\Message\UploadedFileInterface $file */
        $file = $request->getUploadedFiles()['file'];

        $hasValidExtension = in_array(
            pathinfo($file->getClientFilename(), PATHINFO_EXTENSION),
            $this->get('allowedFileExtensions'),
            true
        );

        if (! $hasValidExtension) {
            return $this
                ->get('view')
                ->render(
                    $response,
                    'upload.html.twig',
                    [
                        'error' => sprintf(
                            'Please upload a file in one of the following formats: %s.',
                            implode(', ', $this->get('allowedFileExtensions'))
                        )
                    ]
                );
        }

        if ($file->getError() === UPLOAD_ERR_OK) {
            $file->moveTo(__DIR__ . '/../data/uploads/' . $file->getClientFilename());
        }

        /** @var Filesystem $s3Client */
        $s3Client = $this->get('s3Client');
        try {
            $s3Client->writeStream(
                '/uploads/' . $file->getClientFilename(),
                fopen(__DIR__ . '/../data/uploads/' . $file->getClientFilename(), 'rb')
            );
            return $this
                ->get('view')
                ->render($response, 'success.html.twig');
        } catch (FilesystemException | UnableToWriteFile $e) {
            return $this
                ->get('view')
                ->render($response, 'upload.html.twig', ['error' => $e->getMessage()]);
        }
    }

    return $this
        ->get('view')
        ->render($response, 'upload.html.twig');
});

$app->run();
