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

/* verify.html.twig */
class __TwigTemplate_fc4f807f67db5114a58af5631378014275e865a6680a5fdc8810e96ba0cd6d55 extends Template
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
    <title>Verify Your Account</title>
</head>
<body>
<h1 class=\"title\">
    Please verify your account ";
        // line 10
        echo twig_escape_filter($this->env, ($context["username"] ?? null), "html", null, true);
        echo "
</h1>
";
        // line 12
        if (($context["error"] ?? null)) {
            // line 13
            echo "<p class=error><strong>Error:</strong> ";
            echo twig_escape_filter($this->env, ($context["error"] ?? null), "html", null, true);
            echo "
";
        }
        // line 15
        echo "<form method=\"POST\">
    <div class=\"field\">
        <label for=\"verificationcode\" class=\"label\">Enter the code sent to your phone number.</label>
        <input class=\"input\" type=\"password\" id=\"verificationcode\" name=\"verificationcode\" placeholder=\"verificationcode\">
    </div>
    <div class=\"field\">
        <p class=\"control\">
            <button type=\"submit\" class=\"is-success\" value=\"submitcode\">
                Submit Verification Code
            </button>
        </p>
    </div>
</form>
</body>
</html>
";
    }

    public function getTemplateName()
    {
        return "verify.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  61 => 15,  55 => 13,  53 => 12,  48 => 10,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "verify.html.twig", "/Users/settermjd/Workspace/php/SlimPHP/verify-aws-s3/data/templates/verify.html.twig");
    }
}
