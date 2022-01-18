<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Action Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'actions' => 'migration_actions',

    'actions_cache' => [
        'config' => false,
        'route'  => false,
        'view'   => false,
        'event'  => false,
    ],

    'actions_daemons' => [
        'queue'   => false,
        'horizon' => false,
        'octane'  => false,
    ],
];
