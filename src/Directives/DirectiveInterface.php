<?php


namespace CodeZone\Blade\Directives;


use CodeZone\Blade\Blade;
use CodeZone\Blade\View;

interface DirectiveInterface
{
    public function register(Blade $blade, View $view);
}