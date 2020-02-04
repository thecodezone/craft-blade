<?php


namespace CodeZone\Blade\Functions;

use craft\helpers\Template;

class Paginate implements FunctionInterface
{
    public function call($arguments) {
        list($query) = $arguments;
        return Template::paginateCriteria($query);
    }
}