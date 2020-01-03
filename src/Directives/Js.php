<?php


namespace CodeZone\Blade\Directives;


use CodeZone\Blade\Blade;
use CodeZone\Blade\Cacher;
use CodeZone\Blade\View;

class Js implements DirectiveInterface
{
    /**
     * Positions to position numbers
     * @var array
     */
    protected $positionMap = [
        'head' => View::POS_HEAD,
        'beginBody' => View::POS_BEGIN,
        'endBody' => View::POS_END
    ];

    public function resolvePosition($at)
    {
        if (!array_key_exists($at, $this->positionMap)) {
            $at = 'endBody';
        }
        return $this->positionMap[$at];
    }

    public function register(Blade $blade)
    {
        $blade->directive('js', function ($at) {
            $position = $this->resolvePosition($at);
            return "<?php \$jsPosition = $position; ob_start(); ?>";
        });

        $blade->directive('endjs', function() use (&$position) {
           return "<?php \Craft::\$app->view->registerJs(ob_get_clean(), \$jsPosition); ?>";
        });
    }
}