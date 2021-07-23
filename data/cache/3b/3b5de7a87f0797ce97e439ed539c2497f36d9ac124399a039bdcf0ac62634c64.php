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

/* index.html.twig */
class __TwigTemplate_394759cd4709f7fe77eea3bd4a31ef3ad44075a130f3f15d797dc3b1d9b75086 extends Template
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
<html>
<head>
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <h1>Login</h1>
    ";
        // line 7
        if (($context["error"] ?? null)) {
            // line 8
            echo "        <p class=error>
            <strong>Error:</strong> ";
            // line 9
            echo twig_escape_filter($this->env, ($context["error"] ?? null), "html", null, true);
            echo "
        </p>
    ";
        }
        // line 12
        echo "</head>
<body>
<form method=\"POST\">
    <div class=\"field\">
        <label for=\"username\" class=\"label\">Username</label>
        <input class=\"input\" type=\"text\" id=\"username\" name=\"username\" placeholder=\"Username\">
    </div>
    <div class=\"field\">
        <p class=\"control\">
            <button type=\"submit\" class=\"button is-success\">
                Request verification code
            </button>
        </p>
    </div>
</form>
</body>
</html>";
    }

    public function getTemplateName()
    {
        return "index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  56 => 12,  50 => 9,  47 => 8,  45 => 7,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "index.html.twig", "/Users/settermjd/Workspace/php/SlimPHP/verify-aws-s3/data/templates/index.html.twig");
    }
}
