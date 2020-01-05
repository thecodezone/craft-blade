<?php


namespace CodeZone\Blade\Directives;


use CodeZone\Blade\Blade;

class RequirePermission implements DirectiveInterface
{

    public function register(Blade $blade)
    {
        $blade->directive('requirePermission', function ($permission) {
            return "<?php \Craft::\$app->controller->requirePermission($permission); ?>";
        });
    }
}