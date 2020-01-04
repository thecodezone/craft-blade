<?php


namespace CodeZone\Blade\Tests\unit;


use CodeZone\Blade\Tests\TestCase;
use CodeZone\Blade\View;

class GlobalsTest extends TestCase
{
    /**
     * Cherry pick some random globals to test.
     */
    public function testCurrentSite()
    {
        $html = \Craft::$app->getView()->renderTemplate('globals/currentSite');
        $this->assertStringContainsString(\Craft::$app->getSites()->currentSite->name, $html);
    }

    public function testDevMode()
    {
        $html = \Craft::$app->getView()->renderTemplate('globals/devMode');
        $this->assertStringContainsString('Craft is running in dev mode.', $html);
    }

    public function testToday()
    {
        $html = \Craft::$app->getView()->renderTemplate('globals/today');
        $this->assertStringContainsString('Today is ' . (new \DateTime())->format('M j, Y'), $html);
    }
}