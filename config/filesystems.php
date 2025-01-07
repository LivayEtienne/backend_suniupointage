<?php

return [

    /*
    |----------------------------------------------------------------------
    | Default Filesystem Disk
    |----------------------------------------------------------------------
    |
    | Ici, vous pouvez spécifier le disque de stockage par défaut que votre
    | application doit utiliser. Le disque "local", ainsi que plusieurs
    | disques basés sur le cloud sont disponibles pour votre application.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |----------------------------------------------------------------------
    | Filesystem Disks
    |----------------------------------------------------------------------
    |
    | Vous pouvez configurer autant de disques de stockage que nécessaire,
    | et même configurer plusieurs disques pour un même pilote. Des exemples
    | pour les pilotes de stockage les plus courants sont configurés ici.
    |
    | Pilotes supportés : "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'permissions' => [
                'file' => [
                    'public' => 0644,   // Permissions des fichiers publics
                    'private' => 0600,  // Permissions des fichiers privés
                ],
                'dir' => [
                    'public' => 0755,   // Permissions des répertoires publics
                    'private' => 0700,  // Permissions des répertoires privés
                ],
            ],
            'serve' => true, // Permet de servir les fichiers depuis ce disque
            'throw' => false, // Ne lance pas d'exception en cas d'erreur
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public', // Définit la visibilité à publique
            'permissions' => [
                'file' => [
                    'public' => 0644,   // Permissions des fichiers publics
                ],
                'dir' => [
                    'public' => 0755,   // Permissions des répertoires publics
                ],
            ],
            'throw' => false, // Ne lance pas d'exception en cas d'erreur
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'visibility' => 'public', // Définit la visibilité à publique pour S3
            'throw' => false, // Ne lance pas d'exception en cas d'erreur
        ],

    ],

    /*
    |----------------------------------------------------------------------
    | Symbolic Links
    |----------------------------------------------------------------------
    |
    | Ici, vous pouvez configurer les liens symboliques qui seront créés
    | lorsque la commande Artisan `storage:link` sera exécutée. Les clés du
    | tableau devraient être les emplacements des liens et les valeurs 
    | devraient être leurs cibles.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
