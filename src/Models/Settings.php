<?php


namespace CodeZone\Blade\Models;

use CodeZone\Blade\Directives\Cache;
use CodeZone\Blade\Directives\Css;
use CodeZone\Blade\Directives\Dd;
use CodeZone\Blade\Directives\ExitDirective;
use CodeZone\Blade\Directives\Header;
use CodeZone\Blade\Directives\Hook;
use CodeZone\Blade\Directives\Js;
use craft\base\Model;

class Settings extends Model
{
    public $directives = [
        Cache::class,
        Css::class,
        Dd::class,
        ExitDirective::class,
        Header::class,
        Hook::class,
        Js::class
    ];

    public function rules()
    {
        return [
            [['directives'], 'required'],
            [['directives'], 'array'],
        ];
    }
}