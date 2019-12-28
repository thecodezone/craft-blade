<?php


namespace CodeZone\Blade\Directives;


use CodeZone\Blade\Blade;
use CodeZone\Blade\Cacher;
use CodeZone\Blade\View;

class Cache implements DirectiveInterface
{
    public function register(Blade $blade, View $view)
    {

        $blade->directive('cache', function ($expression = []) {
            return "<?php if (! " . Cacher::class . "::setUp({$expression})) { ?>";
        });

        $blade->directive('endcache', function () {
            return "<?php } echo " . Cacher::class . "::tearDown(); ?>";
        });
    }
}