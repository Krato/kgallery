<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Storage Disk
    |--------------------------------------------------------------------------
    |
    | The disk where to store the images, as defined in config/filesystems.php
    |
    */

    'disk' => 'gallery',

    /*
    |--------------------------------------------------------------------------
    | Middlware
    |--------------------------------------------------------------------------
    |
    | Web middleware to access to backup page
    |
    */
    'middleware' => ['web', 'auth', 'role:admin'],

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | Route prefix to backups. 
    |
    | End route will be: route-prefix/backup.
    | For example: dashboard/backup
    |
    */
    'route-prefix' => 'dashboard',
];
