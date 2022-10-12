<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Actions Repository Connection
    |--------------------------------------------------------------------------
    |
    | This option controls the database connection used to store the table
    | of executed actions.
    |
    */

    'connection' => env('DB_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Actions Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the actions that have already run for
    | your application. Using this information, we can determine which of
    | the actions on disk haven't actually been run in the database.
    |
    */

    'table' => 'migration_actions',

    /*
    |--------------------------------------------------------------------------
    | Actions Path
    |--------------------------------------------------------------------------
    |
    | This option defines the path to the action directory.
    |
    */

    'path' => base_path('actions'),
];
