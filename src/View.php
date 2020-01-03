<?php

namespace CodeZone\Blade;

use Craft;
use Twig\Error\LoaderError as TwigLoaderError;
use Twig\Error\RuntimeError as TwigRuntimeError;
use Twig\Error\SyntaxError as TwigSyntaxError;

/**
 * Extend Craft View class to allow blade views along with twig.
 *
 * Class View
 * @package CodeZone\Blade
 */
class View extends \craft\web\View
{

    // Properties
    // =========================================================================

    /**
     * The blade instance
     * @var
     */
    private $_blade;

    // Public Methods
    // =========================================================================

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
    public function createBlade(): Blade
    {
        $this->_blade = new Blade(
            [rtrim(Craft::$app->getPath()->getSiteTemplatesPath(), '/\\')],
            Craft::$app->getPath()->getCompiledTemplatesPath()
        );

        //Register the globals
        $this->_blade->composer('*', GlobalsComposer::class);

        //Register the directives
        foreach (Plugin::getInstance()->getSettings()->directives as $className) {
            (new $className)->register($this->_blade);
        }

        return $this->_blade;
    }

    /**
     * Is a template string a blade template?
     * @param string $template
     * @return bool
     */
    public function isBladeTemplate(string $template): bool
    {
        $templateFilePath = $this->resolveTemplate($template);
        return str_contains($templateFilePath, 'blade.php');
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
        if($this->isBladeTemplate($template)) {
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
    public function renderBladeTemplate(string $template, array $variables = [], $templateMode): string
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

        $viewPath = str_replace('.blade.php', '', $template);
        $viewPath = str_replace('/', '.', $viewPath);

        try {
            $output = $this->getBlade()->render($viewPath, $variables);

            //Merge lazy functions
            $output = str_replace('<![CDATA[YII-BLOCK-HEAD]]>', $this->getHeadHtml(), $output);
            $output = str_replace('<![CDATA[YII-BLOCK-BODY-BEGIN]]>',$this->renderBodyBeginHtml(), $output);
            $output = str_replace('<![CDATA[YII-BLOCK-BODY-END]]>', $this->getBodyHtml(), $output);
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
