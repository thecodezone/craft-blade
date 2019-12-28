<?php


namespace CodeZone\Blade;

class Filters
{

    protected $extension;

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
            throw new MissingBladeFilterException('The ' . $name . ' filter is not registered.');
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
        return collect(\Craft::$app->view->getTwig()->getFilters())->first(function($filter) use ($name) {
            return $filter->getName() === $name;
        });
    }
}