<?php


namespace CodeZone\Blade\Directives;


use CodeZone\Blade\Blade;
use CodeZone\Blade\Cacher;
use CodeZone\Blade\View;

class BodyHtml implements DirectiveInterface
{

    public function register(Blade $blade, View $view)
    {
        $blade->directive('cache', function ($expression = []) {
            return "<?php echo  ?>";
        });

        $blade->directive('endcache', function () {
            return "<?php } echo " . Cacher::class . "::tearDown(); ?>";
        });
    }
}