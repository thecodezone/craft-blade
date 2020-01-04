<?php


namespace CodeZone\Blade\Directives;


use CodeZone\Blade\Blade;
use CodeZone\Blade\Cacher;
use CodeZone\Blade\View;

class Header implements DirectiveInterface
{

    public function register(Blade $blade)
    {
        $blade->directive('header', function ($header) {
            return "<?php \Craft::\$app->response->headers->set(explode(': ', $header)[0], explode(': ', $header)[1]); ?>";
        });
    }
}