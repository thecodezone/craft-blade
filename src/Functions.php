<?php


namespace CodeZone\Blade;

class Functions
{

    protected $extension;

    /**
     * Pass calls along to twig filters.
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws MissingBladeFunctionException
     */
    public function __call($name, $arguments) {
        if (method_exists($this, $name)) {
            return call_user_func_array([
                $this, $name
            ], $arguments);
        }

        $filter = $this->_find($name);
        if (!$filter) {
            throw new MissingBladeFunctionException('The ' . $name . ' function is not registered.');
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
        return collect(\Craft::$app->view->getTwig()->getFunctions())->first(function($function) use ($name) {
            return $function->getName() === $name;
        });
    }
}