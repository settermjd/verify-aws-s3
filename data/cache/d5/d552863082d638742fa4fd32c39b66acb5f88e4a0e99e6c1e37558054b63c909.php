<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* success.html.twig */
class __TwigTemplate_d72b08537a6a65ea9e0d7f59ff0d7c52b5cda1be7c90a9912f1a958fabb965e6 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <title>Successful Upload!</title>
</head>
<body>
<div class=\"container\">
    <h1 class=\"title\">
        Your image has successfully been added to the S3 bucket.
    </h1>
</div>
</body>
</html>";
    }

    public function getTemplateName()
    {
        return "success.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "success.html.twig", "/Users/settermjd/Workspace/php/SlimPHP/verify-aws-s3/data/templates/success.html.twig");
    }
}
