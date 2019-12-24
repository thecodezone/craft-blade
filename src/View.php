<?php

namespace CodeZone\Blade;

use Craft;
use Twig\Error\LoaderError as TwigLoaderError;
use Twig\Error\RuntimeError as TwigRuntimeError;
use Twig\Error\SyntaxError as TwigSyntaxError;

class View extends \craft\web\View
{

    // Properties
    // =========================================================================

    /**
     * The blade instance
     * @var
     */
    private $_blade;

    /**
     * Get the blade instance
     *
     * @return Blade
     * @throws \yii\base\Exception
     */
    public function getBlade(): Blade
    {
        if (!$this->_blade) {
            $this->createBlade();
        }

        return $this->_blade;
    }


    /**
     * Create a new blade instance.
     *
     * @return Blade
     * @throws \yii\base\Exception
     */
    public function createBlade() {
        return $this->_blade = new Blade(
            [rtrim(Craft::$app->getPath()->getSiteTemplatesPath(), '/\\')],
            Craft::$app->getPath()->getCompiledTemplatesPath()
        );
    }

    /**
     * Renders a Twig template.
     *
     * @param string $template The name of the template to load
     * @param array $variables The variables that should be available to the template
     * @param string|null $templateMode The template mode to use
     * @return string the rendering result
     * @throws TwigLoaderError
     * @throws TwigRuntimeError
     * @throws TwigSyntaxError
     * @throws \Throwable
     */
    public function renderTemplate(string $template, array $variables = [], string $templateMode = null): string
    {
        $templateFilePath = $this->resolveTemplate($template);
        if (str_contains($templateFilePath, 'blade.php')) {
            return $this->renderBladeTemplate($template, $variables, $templateMode);
        } else {
            return parent::renderTemplate($template, $variables, $templateMode);
        }
    }

    /**
     * @param string $template
     * @param array $variables
     * @return string
     * @throws \Throwable
     */
    public function renderBladeTemplate(string $template, array $variables = [], $templateMode)
    {
        if ($templateMode === null) {
            $templateMode = $this->getTemplateMode();
        }

        if (!$this->beforeRenderTemplate($template, $variables, $templateMode)) {
            return '';
        }

        Craft::debug("Rendering blade template: $template", __METHOD__);

        $oldTemplateMode = $this->getTemplateMode();
        $this->setTemplateMode($templateMode);

        $e = null;
        try {
            $output = $this->getBlade()->render($template, $variables);
        } catch (\Throwable $e) {
            // throw it later
        }

        $this->setTemplateMode($oldTemplateMode);

        if ($e !== null) {
            throw YII_DEBUG ? $e : new \RuntimeException('An error occurred when rendering a template.', 0, $e);
        }

        $this->afterRenderTemplate($template, $variables, $templateMode, $output);

        return $output;
    }
}
