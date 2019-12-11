<?php


namespace CodeZone\Blade;

use craft\base\Plugin as Base;
use craft\helpers\App;

class Pugin extends Base
{
    public function init()
    {
        parent::init();
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

        $request = \Craft::$app->getRequest();

        if ($request->getIsCpRequest()) {
            $headers = $request->getHeaders();
            $config['registeredAssetBundles'] = explode(',', $headers->get('X-Registered-Asset-Bundles', ''));
            $config['registeredJsFiles'] = explode(',', $headers->get('X-Registered-Js-Files', ''));
        }

        return $config;
    }
}