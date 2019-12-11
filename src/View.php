<?php

namespace CodeZone\Blade;

use Craft;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\TemplateEvent;
use craft\helpers\FileHelper;
use craft\helpers\Path;
use craft\helpers\StringHelper;
use craft\web\twig\Environment;
use Twig\Error\LoaderError as TwigLoaderError;
use Twig\Error\RuntimeError as TwigRuntimeError;
use Twig\Error\SyntaxError as TwigSyntaxError;

class View extends \yii\web\View
{
    // Constants
    // =========================================================================

    /**
     * @event RegisterTemplateRootsEvent The event that is triggered when registering CP template roots
     */
    const EVENT_REGISTER_CP_TEMPLATE_ROOTS = 'registerCpTemplateRoots';

    /**
     * @event RegisterTemplateRootsEvent The event that is triggered when registering site template roots
     */
    const EVENT_REGISTER_SITE_TEMPLATE_ROOTS = 'registerSiteTemplateRoots';

    /**
     * @event TemplateEvent The event that is triggered before a template gets rendered
     */
    const EVENT_BEFORE_RENDER_TEMPLATE = 'beforeRenderTemplate';

    /**
     * @event TemplateEvent The event that is triggered after a template gets rendered
     */
    const EVENT_AFTER_RENDER_TEMPLATE = 'afterRenderTemplate';

    /**
     * @event TemplateEvent The event that is triggered before a page template gets rendered
     */
    const EVENT_BEFORE_RENDER_PAGE_TEMPLATE = 'beforeRenderPageTemplate';

    /**
     * @event TemplateEvent The event that is triggered after a page template gets rendered
     */
    const EVENT_AFTER_RENDER_PAGE_TEMPLATE = 'afterRenderPageTemplate';

    /**
     * @const TEMPLATE_MODE_SITE
     */
    const TEMPLATE_MODE_SITE = 'site';


    // Properties
    // =========================================================================

    /**
     * The blade instance
     * @var
     */
    private $_blade;

    /**
     * @var array|null
     */
    private $_templateRoots;

    /**
     * @var string|null The root path to look for templates in
     */
    private $_templatesPath;

    /**
     * @var
     */
    private $_defaultTemplateExtensions;

    /**
     * @var
     */
    private $_indexTemplateFilenames;

    /**
     * @var
     */
    private $_templatePaths;

    private $_hooks = [];


    /**
     * @var
     */
    private $_renderingTemplate;

    /**
     * @var
     */
    private $_isRenderingPageTemplate = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $generalConfig = Craft::$app->getConfig()->getGeneral();
        $this->_templatesPath = rtrim(Craft::$app->getPath()->getSiteTemplatesPath(), '/\\');
        $this->_defaultTemplateExtensions = array_merge($generalConfig->defaultTemplateExtensions, ['blade.php']);
        $this->_indexTemplateFilenames = $generalConfig->indexTemplateFilenames;

        // Register the cp.elements.element hook
        $this->hook('cp.elements.element', [$this, '_getCpElementHtml']);
    }

    /**
     * Get the blade instance
     *
     * @return Blade
     */
    public function getBlade(): Blade
    {
        if (!$this->_blade) {
            $this->createBlade();
        }

        return $this->_blade;
    }

    /**
     * Returns the base path that templates should be found in.
     *
     * @return string
     */
    public function getTemplatesPath(): string
    {
        return rtrim(Craft::$app->getPath()->getSiteTemplatesPath(), '/\\');
    }

    /**
     * Create a new blade instance.
     *
     * @return Blade
     */
    public function createBlade() {
        return $this->_blade = new Blade(
            [$this->getTemplatesPath()],
            Craft::$app->getPath()->getCompiledTemplatesPath()
        );
    }

    /**
     * Renders a Twig template.
     *
     * @param string $template The name of the template to load
     * @param array $variables The variables that should be available to the template
     * @return string the rendering result
     * @throws TwigLoaderError
     * @throws TwigRuntimeError
     * @throws TwigSyntaxError
     */
    public function renderTemplate(string $template, array $variables = []): string
    {
        if (!$this->beforeRenderTemplate($template, $variables)) {
            return '';
        }

        Craft::debug("Rendering template: $template", __METHOD__);

        // Render and return
        $renderingTemplate = $this->_renderingTemplate;
        $this->_renderingTemplate = $template;
        try {
            if ($template == '') {
                $template = 'index';
            }
            $output = $this->getBlade()->render($template, $variables);
        } catch (\RuntimeException $e) {
            if (!YII_DEBUG) {
                // Throw a generic exception instead
                throw new \RuntimeException('An error occurred when rendering a template.', 0, $e);
            }
            throw $e;
        }

        $this->_renderingTemplate = $renderingTemplate;

        $this->afterRenderTemplate($template, $variables, $output);

        return $output;
    }

    /**
     * Queues up a method to be called by a given template hook.
     *
     * For example, if you place this in your plugin’s [[BasePlugin::init()|init()]] method:
     *
     * ```php
     * Craft::$app->view->hook('myAwesomeHook', function(&$context) {
     *     $context['foo'] = 'bar';
     *     return 'Hey!';
     * });
     * ```
     *
     * you would then be able to add this to any template:
     *
     * ```twig
     * {% hook "myAwesomeHook" %}
     * ```
     *
     * When the hook tag gets invoked, your template hook function will get called. The $context argument will be the
     * current Twig context array, which you’re free to manipulate. Any changes you make to it will be available to the
     * template following the tag. Whatever your template hook function returns will be output in place of the tag in
     * the template as well.
     *
     * @param string $hook The hook name.
     * @param callback $method The callback function.
     */
    public function hook(string $hook, $method)
    {
        $this->_hooks[$hook][] = $method;
    }

    /**
     * Returns whether a template exists.
     *
     * Internally, this will just call [[resolveTemplate()]] with the given template name, and return whether that
     * method found anything.
     *
     * @param string $name The name of the template.
     * @return bool Whether the template exists.
     */
    public function doesTemplateExist(string $name): bool
    {
        try {
            return ($this->resolveTemplate($name) !== false);
        } catch (TwigLoaderError $e) {
            // _validateTemplateName() han an issue with it
            return false;
        }
    }

    /**
     * Finds a template on the file system and returns its path.
     *
     * @param string $name The name of the template.
     * @return string|false The path to the template if it exists, or `false`.
     */
    public function resolveTemplate(string $name)
    {
        // Normalize the template name
        $name = trim(preg_replace('#/{2,}#', '/', str_replace('\\', '/', StringHelper::convertToUtf8($name))), '/');

        $key = $this->_templatesPath . ':' . $name;

        // Is this template path already cached?
        if (isset($this->_templatePaths[$key])) {
            return $this->_templatePaths[$key];
        }

        // Validate the template name
        $this->_validateTemplateName($name);

        // Look for the template in the main templates folder
        $basePaths = [];

        // Should we be looking for a localized version of the template?
        if (Craft::$app->getIsInstalled()) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $sitePath = $this->_templatesPath . DIRECTORY_SEPARATOR . Craft::$app->getSites()->getCurrentSite()->handle;
            if (is_dir($sitePath)) {
                $basePaths[] = $sitePath;
            }
        }

        $basePaths[] = $this->_templatesPath;

        foreach ($basePaths as $basePath) {
            if (($path = $this->_resolveTemplate($basePath, $name)) !== null) {
                return $this->_templatePaths[$key] = $path;
            }
        }

        unset($basePaths);

        $roots = $this->_getTemplateRoots();

        if (!empty($roots)) {
            foreach ($roots as $templateRoot => $basePaths) {
                /** @var string[] $basePaths */
                $templateRootLen = strlen($templateRoot);
                if (strncasecmp($templateRoot . '/', $name . '/', $templateRootLen + 1) === 0) {
                    $subName = strlen($name) === $templateRootLen ? '' : substr($name, $templateRootLen + 1);
                    foreach ($basePaths as $basePath) {
                        if (($path = $this->_resolveTemplate($basePath, $subName)) !== null) {
                            return $this->_templatePaths[$key] = $path;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Renders a Twig template that represents an entire web page.
     *
     * @param string $template The name of the template to load
     * @param array $variables The variables that should be available to the template
     * @return string the rendering result
     * @throws TwigLoaderError
     * @throws TwigRuntimeError
     * @throws TwigSyntaxError
     */
    public function renderPageTemplate(string $template, array $variables = []): string
    {
        if (!$this->beforeRenderPageTemplate($template, $variables)) {
            return '';
        }

        ob_start();
        ob_implicit_flush(false);

        $isRenderingPageTemplate = $this->_isRenderingPageTemplate;
        $this->_isRenderingPageTemplate = true;

        $this->beginPage();
        echo $this->renderTemplate($template, $variables);
        $this->endPage();

        $this->_isRenderingPageTemplate = $isRenderingPageTemplate;

        $output = ob_get_clean();

        $this->afterRenderPageTemplate($template, $variables, $output);

        return $output;
    }

    // Events
    // -------------------------------------------------------------------------

    /**
     * Performs actions before a template is rendered.
     *
     * @param mixed $template The name of the template to render
     * @param array &$variables The variables that should be available to the template
     * @return bool Whether the template should be rendered
     */
    public function beforeRenderTemplate(string $template, array &$variables): bool
    {
        // Fire a 'beforeRenderTemplate' event
        $event = new TemplateEvent([
            'template' => $template,
            'variables' => $variables,
        ]);
        $this->trigger(self::EVENT_BEFORE_RENDER_TEMPLATE, $event);
        $variables = $event->variables;
        return $event->isValid;
    }

    /**
     * Performs actions after a template is rendered.
     *
     * @param mixed $template The name of the template that was rendered
     * @param array $variables The variables that were available to the template
     * @param string $output The template’s rendering result
     */
    public function afterRenderTemplate(string $template, array $variables, string &$output)
    {
        // Fire an 'afterRenderTemplate' event
        if ($this->hasEventHandlers(self::EVENT_AFTER_RENDER_TEMPLATE)) {
            $event = new TemplateEvent([
                'template' => $template,
                'variables' => $variables,
                'output' => $output,
            ]);
            $this->trigger(self::EVENT_AFTER_RENDER_TEMPLATE, $event);
            $output = $event->output;
        }
    }

    /**
     * Performs actions before a page template is rendered.
     *
     * @param mixed $template The name of the template to render
     * @param array &$variables The variables that should be available to the template
     * @return bool Whether the template should be rendered
     */
    public function beforeRenderPageTemplate(string $template, array &$variables): bool
    {
        // Fire a 'beforeRenderPageTemplate' event
        $event = new TemplateEvent([
            'template' => $template,
            'variables' => &$variables,
        ]);
        $this->trigger(self::EVENT_BEFORE_RENDER_PAGE_TEMPLATE, $event);
        $variables = $event->variables;
        return $event->isValid;
    }

    /**
     * Performs actions after a page template is rendered.
     *
     * @param mixed $template The name of the template that was rendered
     * @param array $variables The variables that were available to the template
     * @param string $output The template’s rendering result
     */
    public function afterRenderPageTemplate(string $template, array $variables, string &$output)
    {
        // Fire an 'afterRenderPageTemplate' event
        if ($this->hasEventHandlers(self::EVENT_AFTER_RENDER_PAGE_TEMPLATE)) {
            $event = new TemplateEvent([
                'template' => $template,
                'variables' => $variables,
                'output' => $output,
            ]);
            $this->trigger(self::EVENT_AFTER_RENDER_PAGE_TEMPLATE, $event);
            $output = $event->output;
        }
    }

    /**
     * Searches for a template files, and returns the first match if there is one.
     *
     * @param string $basePath The base path to be looking in.
     * @param string $name The name of the template to be looking for.
     * @return string|null The matching file path, or `null`.
     */
    private function _resolveTemplate(string $basePath, string $name)
    {
        // Normalize the path and name
        $basePath = FileHelper::normalizePath($basePath);
        $name = trim(FileHelper::normalizePath($name), '/');

        // $name could be an empty string (e.g. to load the homepage template)
        if ($name !== '') {
            // Maybe $name is already the full file path
            $testPath = $basePath . DIRECTORY_SEPARATOR . $name;

            if (is_file($testPath)) {
                return $testPath;
            }

            foreach ($this->_defaultTemplateExtensions as $extension) {
                $testPath = $basePath . DIRECTORY_SEPARATOR . $name . '.' . $extension;

                if (is_file($testPath)) {
                    return $testPath;
                }
            }
        }

        foreach ($this->_indexTemplateFilenames as $filename) {
            foreach ($this->_defaultTemplateExtensions as $extension) {
                $testPath = $basePath . ($name !== '' ? DIRECTORY_SEPARATOR . $name : '') . DIRECTORY_SEPARATOR . $filename . '.' . $extension;

                if (is_file($testPath)) {
                    return $testPath;
                }
            }
        }

        return null;
    }

    /**
     * Ensures that a template name isn't null, and that it doesn't lead outside the template folder.
     *
     * @param string $name
     * @throws BladeLoadError
     */
    private function _validateTemplateName(string $name)
    {
        if (StringHelper::contains($name, "\0")) {
            throw new BladeLoadError(Craft::t('app', 'A template name cannot contain NUL bytes.'));
        }

        if (Path::ensurePathIsContained($name) === false) {
            Craft::error('Someone tried to load a template outside the templates folder: ' . $name);
            throw new BladeLoadError(Craft::t('app', 'Looks like you are trying to load a template outside the template folder.'));
        }
    }

    /**
     * Returns any registered template roots.
     *
     * @param string $which 'cp' or 'site'
     * @return array
     */
    private function _getTemplateRoots(): array
    {
        if (isset($this->_templateRoots[self::TEMPLATE_MODE_SITE])) {
            return $this->_templateRoots[self::TEMPLATE_MODE_SITE];
        }

        $name = self::EVENT_REGISTER_SITE_TEMPLATE_ROOTS;
        $event = new RegisterTemplateRootsEvent();
        $this->trigger($name, $event);

        $roots = [];

        foreach ($event->roots as $templatePath => $dir) {
            $templatePath = strtolower(trim($templatePath, '/'));
            $roots[$templatePath][] = $dir;
        }

        // Longest (most specific) first
        krsort($roots, SORT_STRING);

        return $this->_templateRoots[self::TEMPLATE_MODE_SITE] = $roots;
    }
}
