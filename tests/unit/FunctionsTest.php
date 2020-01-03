<?php


namespace CodeZone\Blade\Tests\unit;


use CodeZone\Blade\Tests\TestCase;
use yii\helpers\BaseHtml;

/**
 * Class FunctionsTest
 * @package CodeZone\Blade\Tests\unit
 */
class FunctionsTest extends TestCase
{
    /**
     * Cherry pick a few functions to test.
     * @return \craft\web\View
     */
    public function view()
    {
        return \Craft::$app->getView();
    }

    public function testAliasFunction()
    {
        $value = '@webroot/images/logo.png';
        $this->assertContains(\Craft::getAlias($value), $this->view()->renderTemplate('functions/alias', compact('value')));
    }

    public function testActionInputFunction()
    {
        $path = 'controller/action/route';
        $this->assertContains('<input type="hidden" name="action" value="controller/action/route">', $this->view()->renderTemplate('functions/actionInput', compact('path')));
    }

    public function testAttrFunction()
    {
        $value = ['hello' => 'world'];
        $this->assertContains(trim(BaseHtml::renderTagAttributes($value)), $this->view()->renderTemplate('functions/attr', compact('value')));
    }

    public function testCsrfInputFunction()
    {
        $this->assertContains('CRAFT_CSRF_TOKEN', $this->view()->renderTemplate('functions/csrfInput'));
    }

    /**
     * Test lazy functions
     */
    public function testItAddsEndBody()
    {
        $js = "animal = 'cat'";
        $html = $this->view()->renderTemplate('functions/endBody', compact('js'));
        $this->assertContains($js, $html);
    }

    public function testItAddsHead()
    {
        $js = "animal = 'cat'";
        $html = $this->view()->renderTemplate('functions/head', compact('js'));
        $this->assertContains($js, $html);
    }
}