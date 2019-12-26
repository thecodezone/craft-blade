<?php


namespace CodeZone\Blade;


use craft\web\twig\Extension;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Functions
{

    protected $extension;

    public function __construct()
    {
        $this->extension = new Extension(new \craft\web\View, new Environment(new FilesystemLoader));
    }

    /**
     * Pass calls along to twig filters.
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws MissingBladeFilterException
     */
    public function __call($name, $arguments) {
        if (method_exists($this, $name)) {
            return call_user_func_array([
                $this, $name
            ], $arguments);
        }

        $filter = $this->_find($name);
        if (!$filter) {
            throw new MissingBladeFilterException('The ' . $name . ' function is not registered.');
        }
        return call_user_func_array($filter->getCallable(), $arguments);
    }

    /**
     * Find a twig filter by name.
     * @param $name
     * @return mixed
     */
    protected function _find($name)
    {
        return collect($this->extension->getFunctions())->first(function($function) use ($name) {
            return $function->getName() === $name;
        });
    }
}