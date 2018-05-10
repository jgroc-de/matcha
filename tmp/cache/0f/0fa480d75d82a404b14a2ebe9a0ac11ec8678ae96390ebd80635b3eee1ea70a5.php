<?php

/* sign.html.twig */
class __TwigTemplate_7053fc72c4c605455e282e4723f8fd6f04b9e0636082d5dd468601ca9da65fd5 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>

<html>
    <head>
    </head>
    <body>
        <header>
            <h1>Wilkommen/Welcome/Bienvenue</h1>
        </header>
        <section>
            <h2>content</h2>
        </section>
        <footer>
            <hr>
            <em>© jgroc-de 2018</em>
        </footer>

    </body>
</html>
";
    }

    public function getTemplateName()
    {
        return "sign.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "sign.html.twig", "/home/gg/www/test/app/template/views/sign.html.twig");
    }
}
