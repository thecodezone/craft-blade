<?php


namespace CodeZone\Blade\Directives;


use CodeZone\Blade\Blade;
use CodeZone\Blade\Cacher;
use CodeZone\Blade\View;

class Dd implements DirectiveInterface
{

    public function register(Blade $blade)
    {
        $blade->directive('dd', function ($expression) {
            return "<?php dd({$expression}); ?>";
        });
    }
}