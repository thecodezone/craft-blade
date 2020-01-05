<?php


namespace CodeZone\Blade\Models;

use CodeZone\Blade\Directives\Cache;
use CodeZone\Blade\Directives\Css;
use CodeZone\Blade\Directives\Dd;
use CodeZone\Blade\Directives\ExitDirective;
use CodeZone\Blade\Directives\Header;
use CodeZone\Blade\Directives\Hook;
use CodeZone\Blade\Directives\Js;
use CodeZone\Blade\Directives\Redirect;
use CodeZone\Blade\Directives\RequireLogin;
use CodeZone\Blade\Directives\RequirePermission;
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
        Js::class,
        Redirect::class,
        RequireLogin::class,
        RequirePermission::class
    ];

    public function rules()
    {
        return [
            [['directives'], 'required'],
            [['directives'], 'array'],
        ];
    }
}