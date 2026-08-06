<?php

/*
 | This file was missing, so queue.default resolved to null and QUEUE_CONNECTION
 | in .env had no effect. Nothing was dispatched yet so nothing broke, but any
 | queued job would have failed to resolve a connector.
 |
 | retry_after must stay above the longest a job can run, and above the
 | --max-time the scheduled queue drain uses, or a job still running can be
 | released back onto the queue and run twice.
 */
return [
    'default' => env('QUEUE_CONNECTION', 'database'),

    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],
        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 120,
            'after_commit' => false,
        ],
    ],

    'batching' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'job_batches',
    ],

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],
];
