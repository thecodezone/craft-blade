<?php

namespace CodeZone\Blade;

use craft\helpers\App;

class Plugin extends \craft\base\Plugin
{
    const EDITION_LITE = 'lite';
    const EDITION_PRO = 'pro';


    public static function editions(): array
    {
        return [
            self::EDITION_LITE,
            self::EDITION_PRO,
        ];
    }


    public function init()
    {
        $this->adjustConfig();

        parent::init();
    }

    /**
     * Add blade extension to default template extensions to the config.
     */
    public function adjustConfig()
    {
        $generalConfig = \Craft::$app->getConfig()->getGeneral();
        $extensions = $generalConfig->defaultTemplateExtensions;
        $generalConfig->defaultTemplateExtensions = array_unique(
            array_merge($extensions, ['blade.php'])
        );
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