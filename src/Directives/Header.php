<?php


namespace CodeZone\Blade\Directives;


use CodeZone\Blade\Blade;
use CodeZone\Blade\Cacher;
use CodeZone\Blade\View;

class Header implements DirectiveInterface
{

    public function register(Blade $blade, View $view)
    {
        $blade->directive('header', function ($header) {
            return "<?php header({$header}); ?>";
        });
    }
}