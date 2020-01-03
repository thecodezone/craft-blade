<?php


namespace CodeZone\Blade\Directives;


use CodeZone\Blade\Blade;
use CodeZone\Blade\View;

class Hook implements DirectiveInterface
{

    public function register(Blade $blade)
    {
        $blade->directive('hook', function ($name) {
            return "<?php 
                  \$_view_context = get_defined_vars(); 
                  echo \Craft::\$app->getView()->invokeHook({$name}, \$_view_context);
                  foreach(\$_view_context as \$key => \$value) {
                    \$\$key = \$value;
                  }
                  unset(\$_view_context);
              ?>";
        });
    }
}