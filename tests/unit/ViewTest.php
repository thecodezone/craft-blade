<?php


namespace CodeZone\Blade\Tests\Unit;


use CodeZone\Blade\Tests\TestCase;

class ViewTest extends TestCase
{
    public function view()
    {
        return \Craft::$app->getView();
    }

    public function testItDoesntResolveMissing()
    {
        $this->assertFalse(
            $this->view()->resolveTemplate('missing')
        );

        $this->assertContains(
            '.blade', $this->view()->resolveTemplate('template')
        );
    }

    public function testItResolvesTwig()
    {
        $this->assertContains(
            '.twig', $this->view()->resolveTemplate('twig-template')
        );
    }

    public function testItRendersBlade()
    {
        $this->assertContains(
            'Hello World', $this->view()->renderTemplate('template', ['content' => 'Hello World'])
        );
    }

    public function testItRendersTwig()
    {
        $this->assertContains(
            'Hello World', $this->view()->renderTemplate('twig-template', ['content' => 'Hello World'])
        );
    }

    public function testItResolvesNested() {
        $this->assertNotFalse(
            $this->view()->resolveTemplate('nested/view')
        );
    }

}