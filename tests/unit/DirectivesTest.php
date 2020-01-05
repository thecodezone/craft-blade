<?php


namespace CodeZone\Blade\Tests\unit;


use CodeZone\Blade\Tests\TestCase;

class DirectivesTest extends TestCase
{
    public function testCache()
    {
        $content = 'cached content';
        $key = 'test';
        $html = \Craft::$app->getView()->renderTemplate('directives/cache', compact('content', 'key'));
        $cached = \Craft::$app->templateCaches->getTemplateCache($key, true);
        $this->assertStringContainsString($content, $cached);
        $this->assertStringContainsString($content, $html);
    }

    public function testCss()
    {
        $css = '.el{color: black}';
        $html = \Craft::$app->getView()->renderTemplate('directives/css', compact('css'));
        $this->assertStringContainsString($css, $html);
    }

    public function testHeader()
    {
        $header = "Key: Value";
        \Craft::$app->getView()->renderTemplate('directives/header', compact('header'));
        $this->assertEquals('Value', \Craft::$app->response->getHeaders()->get('Key'));
    }

    public function testJs()
    {
        $js = "var hello = 1";
        $html = \Craft::$app->getView()->renderTemplate('directives/js', compact('js'));
        $this->assertStringContainsString($js, $html);
    }

    public function testHook()
    {
        $name = 'the-hook';
        $i = 1;
        \Craft::$app->view->hook($name, function(array &$context) {
            $context['i']++;
            return 'result';
        });
        $html = \Craft::$app->view->renderTemplate('directives/hook', compact('name', 'i'));
        $this->assertStringContainsString('number 2', $html);
        $this->assertStringContainsString('result', $html);
    }

    public function testRedirect()
    {
        $this->assertFalse(\Craft::$app->getResponse()->getIsRedirection());
        \Craft::$app->getView()->renderTemplate('directives/redirect');
        $this->assertTrue(\Craft::$app->getResponse()->getIsRedirection());
    }
}