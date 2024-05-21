<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Operations Repository Connection
    |--------------------------------------------------------------------------
    |
    | This option controls the database connection used to store the table
    | of executed operations.
    |
    */

    'connection' => env('DB_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Operations Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the operations that have already run for
    | your application. Using this information, we can determine which of
    | the operations on disk haven't actually been run in the database.
    |
    */

    'table' => 'operations',

    /*
    |--------------------------------------------------------------------------
    | Operations Path
    |--------------------------------------------------------------------------
    |
    | This option defines the path to the operation directory.
    |
    */

    'path' => base_path('operations'),

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
    | asynchronous operations.
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

        'connection' => env('DEPLOY_OPERATIONS_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sync')),

        /*
        |--------------------------------------------------------------------------
        | Queue Name
        |--------------------------------------------------------------------------
        |
        | This parameter specifies the name of the queue to which asynchronous
        | jobs will be sent.
        |
        */

        'name' => env('DEPLOY_OPERATIONS_QUEUE_NAME'),
    ],
];
