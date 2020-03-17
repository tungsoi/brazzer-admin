<?php
return [
    'site_config_groups'      => [
        'setting' => 'Setting',
        'image'   => 'Image config'
    ],
    'site_config_permissions' => [
        'setting' => [],
        'image'   => [],
    ],
    'setting'                 => [
        'logo'        => [
            'type' => 'image',
            'uniqueName'
        ],
        'title'       => [
            'label text',
            'placeholder' => 'typing...',
            'rules'       => 'required'
        ],
        'description' => [
            'label text',
            'placeholder' => 'typing...',
            'rules'       => 'required'
        ],
        'keywords'    => ['type' => 'tags'],
    ],
    'image'                   => [

    ],
];