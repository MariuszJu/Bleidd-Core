<?php

return [
    /**
     * Define paths of files to be loaded automatically
     */
    'autoloader_paths' => [
        ROOT_DIR . '/src/core',
        ROOT_DIR . '/src/app',
        //ROOT_DIR . '/vendor',
    ],

    /**
     * Path to the custom modules
     */
    'modules_path' => ROOT_DIR . '/src/app',

    /**
     * System loggers
     */
    'loggers' => [
        'http' => [
            'active'    => true,
            'logs_file' => ROOT_DIR . '/logs/http_logs.log',
            'class'     => \Bleidd\Logger\Http\FileHttpLogger::class,
        ],
        'errors' => [
            'active'    => true,
            'logs_file' => ROOT_DIR . '/logs/error_logs.log',
            'class'     => \Bleidd\Logger\Http\FileErrorLogger::class,
        ],
    ],

    /**
     * Database configuration
     */
    'db' => include ROOT_DIR . '/config/db.php',

    /**
     * Key to encrypt and decrypt session
     */
    'session_encryption_key' => 'AlaMaKota123',

    /**
     * Path to the cache directory
     */
    'cache_path' => ROOT_DIR . '/cache',

    /**
     * Languages configuration
     */
    'language' => [
        'default' => 'en',
    ],

    /**
     * View configuration, default layout file etc.
     */
    'view' => [
        //'layout' => 'src/app/Page/resources/views/layout/layout.phtml',
    ],

    /**
     * Global middlewares - runs before each controller action
     */
    'global_middlewares' => [

    ],
];