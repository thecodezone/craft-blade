<?php


namespace CodeZone\Blade\Directives;


use CodeZone\Blade\Blade;

class Redirect implements DirectiveInterface
{

    public function register(Blade $blade)
    {
        $blade->directive('redirect', function ($url) {
            return "<?php \Craft::\$app->response->redirect($url) ?>";
        });
    }
}