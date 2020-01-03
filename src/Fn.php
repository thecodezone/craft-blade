<?php

use \CodeZone\Blade\FunctionProxy;

/**
 * Unnamespaced functions proxy.
 * Class Fn
 */
class Fn
{
    public static function __callStatic($name, $arguments)
    {
        return FunctionProxy::call($name, $arguments);
    }
}