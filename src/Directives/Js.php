<?php


namespace CodeZone\Blade\Directives;


use CodeZone\Blade\Blade;
use CodeZone\Blade\Cacher;
use CodeZone\Blade\View;

class Js implements DirectiveInterface
{

    public function register(Blade $blade)
    {
        $blade->directive('js', function ($expression) {
            return "<?php ob_start(); ?>";
        });

        $blade->directive('endjs', function($expression) {
           return "<?php \Craft::\$app->view->registerJs(ob_get_clean()); ?>";
        });
    }
}