<?php


namespace CodeZone\Blade\Tests\unit;


use CodeZone\Blade\Blade;
use CodeZone\Blade\Tests\TestCase;
use CodeZone\Blade\View;

class BladeTest extends TestCase
{
    public function blade()
    {
        return \Craft::$app->getView()->getBlade();
    }

    public function testItCanRender()
    {
        $this->assertContains(
            'Hello World', $this->blade()->render('template', ['content' => 'Hello World'])
        );
    }

    public function testItTestsExistance()
    {
        $this->assertFalse($this->blade()->exists('missing'));
        $this->assertTrue($this->blade()->exists('template'));
    }

    public function testItCanGetByFile()
    {
        $this->assertInstanceOf(
            \Illuminate\View\View::class,
            $this->blade()->file(\Craft::$app->getView()->resolveTemplate('template')
            )
        );
    }
}