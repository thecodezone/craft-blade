<?php
return [
    'components' => [
        'view' => function() {
            $config = \CodeZone\Blade\Plugin::viewConfig();
            return Craft::createObject($config);
        },
    ]
];
