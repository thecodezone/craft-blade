<?php

use \CodeZone\Blade\FunctionProxy;

/**
 * Unnamespaced functions proxy.
 * Class Func
 */
class Func
{
    public static function __callStatic($name, $arguments)
    {
        return FunctionProxy::call($name, $arguments);
    }
}