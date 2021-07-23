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

/* upload.html.twig */
class __TwigTemplate_9d75028c64dd9fe772e34777f5e404fc7f0fd23e826b8509e190326cdc0a4540 extends Template
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
    <title>Upload an Image to the AWS S3 Bucket</title>
</head>
<body>
<h1 class=\"title\">
    Please upload an image in .jpg/.jpeg, or .png format
</h1>
";
        // line 12
        if (($context["error"] ?? null)) {
            echo "<p class=error><strong>Error:</strong> ";
            echo twig_escape_filter($this->env, ($context["error"] ?? null), "html", null, true);
        }
        // line 13
        echo "<form action=\"/upload\" method=\"POST\" enctype=\"multipart/form-data\">
    <label for=\"name\">File</label>
    <input id=\"name\" type=\"file\" name=\"file\" />
    <br>
    <br>
    <input type=submit value=Upload>
</form>
</body>
</html>";
    }

    public function getTemplateName()
    {
        return "upload.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  55 => 13,  50 => 12,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "upload.html.twig", "/Users/settermjd/Workspace/php/SlimPHP/verify-aws-s3/data/templates/upload.html.twig");
    }
}
