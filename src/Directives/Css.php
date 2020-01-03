<?php


namespace CodeZone\Blade\Directives;


use CodeZone\Blade\Blade;
use CodeZone\Blade\Cacher;
use CodeZone\Blade\View;

class Css implements DirectiveInterface
{

    public function register(Blade $blade)
    {
        $blade->directive('css', function ($expression) {
            return "<?php ob_start(); ?>";
        });

        $blade->directive('endcss', function($expression) {
           return "<?php \Craft::\$app->view->registerCss(ob_get_clean()); ?>";
        });
    }
}