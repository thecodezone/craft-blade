<?php

namespace CodeZone\Blade;

use Illuminate\View\View;

class GlobalsComposer
{
    /**
     * Bind all twig globals to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with(
            \Craft::$app->view->getTwig()->getGlobals()
        );
    }
}