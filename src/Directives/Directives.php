<?php


namespace CodeZone\Blade\Directives;


use CodeZone\Blade\Blade;
use CodeZone\Blade\View;

/**
 * Setup blade directives.
 *
 * Class Directives
 * @package CodeZone\Blade\Directives
 */
class Directives
{
    private $_blade;
    private $_view;

    public function __construct(Blade $blade, View $view)
    {
        $this->_blade = $blade;
        $this->_view = $view;
    }

    /**
     * Registered directives.
     * @var array
     */
    private $_directives = [
        Cache::class,
        Dd::class,
        ExitDirective::class,
        Header::class,
        Hook::class
    ];

    /**
     * Get all the directives.
     * @return array
     */
    public function getDirectives()
    {
        return $this->_directives;
    }

    /**
     * Register all directives.
     */
    public function register()
    {
        foreach ($this->getDirectives() as $className) {
            $this->registerDirective($className);
        }
    }

    /**
     * Register a directive by classname.
     * @param $classname
     */
    public function registerDirective($classname)
    {
        self::factory($classname)->register($this->_blade, $this->_view);
    }

    /**
     * Directive factory.
     * @param $className
     * @return mixed
     */
    static public function factory($className)
    {
        return new $className;
    }
}