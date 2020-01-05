<?php


namespace CodeZone\Blade\Directives;


use CodeZone\Blade\Blade;

class RequireLogin implements DirectiveInterface
{

    public function register(Blade $blade)
    {
        $blade->directive('requireLogin', function ($expression) {
            return "<?php \Craft::\$app->controller->requireLogin(); ?>";
        });
    }
}