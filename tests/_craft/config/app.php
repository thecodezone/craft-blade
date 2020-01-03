<?php
return [
    'components' => [
        'view' => function() {
            $config = \CodeZone\Blade\Plugin::viewConfig(true);
            return Craft::createObject($config);
        },
    ]
];
