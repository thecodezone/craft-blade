<?php
namespace CodeZone\Blade\Tests;

use Codeception\Test\Unit;
use CodeZone\Blade\View;

class TestCase extends Unit
{
    protected function _setUp()
    {
        $view = \Craft::$app->view;
        \Craft::$app->getPlugins()->installPlugin('blade');
        $view->setTemplateMode($view::TEMPLATE_MODE_SITE);
        return parent::_setUp();
    }
}