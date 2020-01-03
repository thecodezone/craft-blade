<?php


namespace CodeZone\Blade;


use Twig\TwigFunction;

/**
 * Proxy calls to a twig function
 *
 * Class FunctionProxy
 * @package CodeZone\Blade
 */
class FunctionProxy
{
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws MissingBladeFunctionException
     */
    public static function __callStatic($name, $arguments) {
        if (method_exists(FunctionProxy::class, $name)) {
            return forward_static_call_array([
                $name
            ], $arguments);
        }

        return self::call($name, $arguments);
    }

    /**
     * Call a twig function by string name and prarms array.
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws MissingBladeFunctionException
     */
    public static function call($name, $arguments = []) {
        $function = collect(\Craft::$app->view->getTwig()->getFunctions())->first(function($function) use ($name) {
            return $function->getName() === $name;
        });
        if (!$function) {
            throw new MissingBladeFunctionException('The ' . $name . ' function is not registered.');
        }
        return self::callFunction($function, $arguments);
    }

    /**
     * Call a twig function
     * @param TwigFunction $function
     * @param $arguments
     * @return mixed
     */
    protected static function callFunction(TwigFunction $function, $arguments)
    {
        return call_user_func_array($function->getCallable(), $arguments);
    }
}