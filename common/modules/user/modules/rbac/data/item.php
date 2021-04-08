<?php

return [
    'createData'    => [
        'type'        => 2,
        'description' => 'Create a data',
    ],
    'readData'      => [
        'type'        => 2,
        'description' => 'View news, comment, etc',
    ],
    'updateData'    => [
        'type'        => 2,
        'description' => 'Update data',
    ],
    'deleteData'    => [
        'type'        => 2,
        'description' => 'Delete news, comment, etc',
    ],
    'updateOwnData' => [
        'type'        => 2,
        'description' => 'Update own data',
        'ruleName'    => 'author',
    ],
    'deleteOwnData' => [
        'type'        => 2,
        'description' => 'Delete own data',
        'ruleName'    => 'author',
    ],
    'adminAccess'   => [
        'type'        => 2,
        'description' => 'Admin access to site',
        'ruleName'    => 'adminAccess',
    ],
    'guest'         => [
        'type'     => 1,
        'children' => [
            'readData',
        ],
    ],
    'user'          => [
        'type'     => 1,
        'ruleName' => 'notGuestRule',
        'children' => [
            'guest',
            'createData',
            'updateOwnData',
            'deleteOwnData',
        ],
    ],
    'admin'         => [
        'type'     => 1,
        'children' => [
            'user',
            'deleteData',
            'adminAccess',
        ],
    ],
];
