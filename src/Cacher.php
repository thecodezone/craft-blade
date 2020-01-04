<?php

namespace CodeZone\Blade;

use craft\helpers\StringHelper;

/**
 * Class Cacher
 * @package CodeZone\Blade\Cache
 */
class Cacher
{
    private static $_cacheCount = 1;
    private static $_cacheUniqueCount = 0;
    private static $_cacheConfig = [];
    private static $_cacheBody = [];
    private static $_cacheKey = [];
    private static $_ignoreCache = [];

    /**
     * The cache instance.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Handle the @cache setup.
     *
     * @param mixed       $model
     * @param string|null $key
     */
    public static function setup($config = [])
    {
        $n = self::$_cacheCount++;
        $i = self::$_cacheUniqueCount++;
        self::$_cacheConfig[$n] = $config;
        $if = isset($config['if']) ? $config['if'] : true;
        $unless = isset($config['unless']) ? $config['unless'] : false;
        $key = isset($config['key']) ? $config['key'] : null;
        $global = isset($config['global']) ? $config['global'] : false;

        $cacheService =  \Craft::$app->getTemplateCaches();
        $request = \Craft::$app->getRequest();
        self::$_ignoreCache[$n] =
            ($request->getIsLivePreview() || $request->getToken())
            || !$if
            || $unless;

        if (!self::$_ignoreCache[$n]) {
            self::$_cacheKey[$n] = $key ? $key : md5($request->getFullPath() . $i);
            self::$_cacheBody[$n] = $cacheService->getTemplateCache(self::$_cacheKey[$n], $global);
        } else {
            self::$_cacheBody[$n] = null;
        }

        if (self::$_cacheBody[$n] === null) {
            if (!self::$_ignoreCache[$n]) {
                $cacheService->startTemplateCache(self::$_cacheKey[$n]);
            }
            ob_start();
            return false;
        }

        return true;
    }

    /**
     * Handle the @endcache teardown.
     */
    public static function teardown()
    {
        self::$_cacheCount--;
        $n = self::$_cacheCount;
        $config = self::$_cacheConfig[$n];
        $global = isset($config['global']) ? $config['global'] : false;
        $cacheService =  \Craft::$app->getTemplateCaches();
        $expiration = isset($config['expiration']) ? new \DateTime($config['expiration']) : null;
        $durationNum = isset($config['durationNum']) ? $config['durationNum'] : null;
        $durationUnit = isset($config['durationUnit']) ? $config['durationUnit'] : null;
        $duration = null;

        if (self::$_cacheBody[$n] === null) {
            self::$_cacheBody[$n] = ob_get_clean();
            if (!self::$_ignoreCache[$n]) {
                if ($durationNum) {
                    // So silly that PHP doesn't support "+1 week" http://www.php.net/manual/en/datetime.formats.relative.php

                    if ($durationUnit === 'week') {
                        if ($durationNum == 1) {
                            $durationNum = 7;
                            $durationUnit = 'days';
                        } else {
                            $durationUnit = 'weeks';
                        }
                    }

                    $duration = $durationNum . ' ' . $durationUnit;
                }
                $cacheService->endTemplateCache(self::$_cacheKey[$n], $global, $duration, $expiration, self::$_cacheBody[$n]);
            }
        }


        return self::$_cacheBody[$n];
    }
}