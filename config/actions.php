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

    'table' => 'actions',

    /*
    |--------------------------------------------------------------------------
    | Actions Path
    |--------------------------------------------------------------------------
    |
    | This option defines the path to the action directory.
    |
    */

    'path' => base_path('actions'),

    /*
    |--------------------------------------------------------------------------
    | Path Exclusion
    |--------------------------------------------------------------------------
    |
    | This option determines which directory and/or file paths should be
    | excluded when processing files.
    |
    | Valid values: array, string or null
    |
    | Specify `null` to disable.
    |
    | For example,
    |    ['foo', 'bar']
    |    'foo'
    |    null
    |
    */

    'exclude' => null,

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    | This option specifies the queue settings that will process
    | asynchronous actions.
    |
    */

    'queue' => [
        /*
        |--------------------------------------------------------------------------
        | Queue Connection
        |--------------------------------------------------------------------------
        |
        | This parameter defines the default connection.
        |
        */

        'connection' => env('ACTIONS_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sync')),

        /*
        |--------------------------------------------------------------------------
        | Queue Name
        |--------------------------------------------------------------------------
        |
        | This parameter specifies the name of the queue to which asynchronous
        | jobs will be sent.
        |
        */

        'name' => env('ACTIONS_QUEUE_NAME'),
    ],
];
