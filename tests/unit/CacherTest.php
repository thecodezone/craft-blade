<?php


namespace CodeZone\Blade\Tests\unit;


use Carbon\Carbon;
use CodeZone\Blade\Cacher;
use CodeZone\Blade\Tests\TestCase;

class CacherTest extends TestCase
{
    public function _before()
    {
        \Craft::$app->getTemplateCaches()->deleteAllCaches();
    }

    public function testItCaches()
    {
        $key = "test";
        $content = 'test content';
        Cacher::setup([
            'key' => $key,
            'global' => true
        ]);
        echo $content;
        Cacher::teardown();
        $cached = \Craft::$app->templateCaches->getTemplateCache($key, true);
        $this->assertEquals($content, $cached);
    }

    public function testItCachesNestedCalls()
    {

        $outerKey = "outer";
        $innerKey = 'inner';
        $outerContent = "outer content";
        $innerContent = " inner content";
        echo Cacher::setup([
            'key' => $outerKey,
            'global' => true
        ]);
            echo $outerContent;
            echo Cacher::setup([
                'key' => $innerKey,
                'global' => true
            ]);
                echo $innerContent;
            echo Cacher::teardown();
        echo Cacher::teardown();
        $outerCached = \Craft::$app->templateCaches->getTemplateCache($outerKey, true);
        $innerCached = \Craft::$app->templateCaches->getTemplateCache($innerKey, true);
        $this->assertEquals($outerContent . $innerContent, $outerCached);
        $this->assertEquals($innerContent, $innerCached);
    }

    public function testItCachesTrueConditions()
    {
        $key = "test";
        $content = 'test content';
        Cacher::setup([
            'key' => $key,
            'if' => true,
            'global' => true
        ]);
        echo $content;
        Cacher::teardown();
        $cached = \Craft::$app->templateCaches->getTemplateCache($key, true);
        $this->assertEquals($content, $cached);
    }

    public function testIgnoresIfUnlessTrue()
    {
        $key = "test";
        $content = 'test content';
        Cacher::setup([
            'key' => $key,
            'unless' => true,
            'global' => true
        ]);
        echo $content;
        Cacher::teardown();
        $cached = \Craft::$app->templateCaches->getTemplateCache($key, true);
        $this->assertNull($cached);
    }

    public function testCacheIfUnlessFalse()
    {
        $key = "test";
        $content = 'test content';
        Cacher::setup([
            'key' => $key,
            'unless' => false,
            'global' => true
        ]);
        echo $content;
        Cacher::teardown();
        $cached = \Craft::$app->templateCaches->getTemplateCache($key, true);
        $this->assertEquals($content, $cached);
    }

    public function testItExpires()
    {
        $key = "test";
        $content = 'test content';
        Cacher::setup([
            'key' => $key,
            'global' => true,
            'expiration' => 'yesterday'
        ]);
        echo $content;
        Cacher::teardown();
        $cached = \Craft::$app->templateCaches->getTemplateCache($key, true);
        $this->assertNull($cached);
    }

    public function testItExpiresFromDatetime()
    {
        $key = "test";
        $content = 'test content';
        Cacher::setup([
            'key' => $key,
            'global' => true,
            'expiration' => Carbon::now()->subDay(1)
        ]);
        echo $content;
        Cacher::teardown();
        $cached = \Craft::$app->templateCaches->getTemplateCache($key, true);
        $this->assertNull($cached);
    }

    public function testItExpiresInFuture()
    {
        $key = "test";
        $content = 'test content';
        Cacher::setup([
            'key' => $key,
            'global' => true,
            'durationNum' => 1,
            'durationUnit' => 'weeks'
        ]);
        echo $content;
        Cacher::teardown();
        $cached = \Craft::$app->templateCaches->getTemplateCache($key, true);
        $this->assertEquals($content, $cached);
    }

    public function testItExpiresViaDuration()
    {
        $key = "test";
        $content = 'test content';
        Cacher::setup([
            'key' => $key,
            'global' => true,
            'durationNum' => -1,
            'durationUnit' => 'weeks'
        ]);
        echo $content;
        Cacher::teardown();
        $cached = \Craft::$app->templateCaches->getTemplateCache($key, true);
        $this->assertNull($cached);
    }
}