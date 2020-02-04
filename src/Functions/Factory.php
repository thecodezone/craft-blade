<?php


namespace CodeZone\Blade\Functions;


class Factory
{
    protected static function classnameFromName($name): string
    {
        return "CodeZone\\Blade\\Functions\\" . ucfirst($name);
    }

    public static function exists($name): bool
    {
        return (class_exists(static::classnameFromName($name)));
    }

    public static function make($name): FunctionInterface
    {
        $classname = static::classnameFromName($name);
        return new $classname;
    }
}