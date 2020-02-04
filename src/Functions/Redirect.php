<?php


namespace CodeZone\Blade\Functions;


class Redirect implements FunctionInterface
{
    public function call($arguments) {
        list($url) = $arguments;
        return \Craft::$app->getResponse()->redirect($url);
    }
}