<?php


namespace CodeZone\Blade\Tests\Unit;


use CodeZone\Blade\Tests\TestCase;

class ViewTest extends TestCase
{
    public function view()
    {
        return \Craft::$app->getView();
    }

    public function testItResolvesFiles()
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

    public function testInjectsSlots()
    {
        $this->view()->registerJs('var head = true;', $this->view()::POS_HEAD);
        $this->view()->registerJs('var begin = true;', $this->view()::POS_HEAD);
        $this->view()->registerJs('var end = true;', $this->view()::POS_HEAD);

        $html = $this->view()->renderTemplate('template');

        $this->assertContains('var head = true;', $html);
        $this->assertContains('var begin = true;', $html);
        $this->assertContains('var end = true;', $html);
    }
}