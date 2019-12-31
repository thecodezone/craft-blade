<?php

namespace CodeZone\Blade\Tests\Unit;

use Codeception\Configuration;
use Codeception\Lib\Di;
use Codeception\Scenario;
use Codeception\Test\Unit;

use CodeZone\Blade\Plugin;
use CodeZone\Blade\Tests\TestCase;
use CodeZone\Blade\View;
use UnitTester;
use Craft;

class PluginTest extends TestCase
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testItInstalls()
    {
        $plugin = Craft::$app->getPlugins()->getPlugin('blade');
        $this->assertInstanceOf(Plugin::class, $plugin);
    }

    public function testItAllowsBladeFiles()
    {
        $generalConfig = Craft::$app->getConfig()->getGeneral('blade');
        $this->assertEquals('blade.php', array_pop($generalConfig->defaultTemplateExtensions));
    }

    public function testItOverridesTheView()
    {
        $view = Craft::$app->getView();
        $this->assertEquals(View::class, get_class($view));
    }
}