<?php


namespace CodeZone\Blade\Directives;


use CodeZone\Blade\Blade;
use CodeZone\Blade\Cacher;
use CodeZone\Blade\View;

class ExitDirective implements DirectiveInterface
{

    public function register(Blade $blade)
    {
        $blade->directive('header', function ($header) {
            return "<?php header({$header}); ?>";
        });
    }
}