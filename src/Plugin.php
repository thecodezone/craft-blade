<?php

namespace CodeZone\Blade;

use craft\helpers\App;

/**
 * Class Plugin
 * @package CodeZone\Blade
 */
class Plugin extends \craft\base\Plugin
{
    public function init()
    {
        $this->adjustConfig();

        parent::init();
    }

    /**
     * Add blade extension to default template extensions to the config.
     */
    protected function adjustConfig()
    {
        if ($this->viewRegistered()) {
            $generalConfig = \Craft::$app->getConfig()->getGeneral();
            $extensions = $generalConfig->defaultTemplateExtensions;
            $generalConfig->defaultTemplateExtensions = array_unique(
                array_merge($extensions, ['blade.php'])
            );
        }
    }

    /**
     * The developer may not have loaded the view.
     */
    public function viewRegistered()
    {
        return get_class(\Craft::$app->getView()) === View::class;
    }

    /**
     * Returns the `view` component config.
     * Falls back to Twig based views for the CP.
     */
    public static function viewConfig(): array
    {
        $request = \Craft::$app->getRequest();

        if ($request->getIsCpRequest()) {
            return App::viewConfig();
        }

        $config = [
            'class' => View::class,
        ];

        return $config;
    }
}