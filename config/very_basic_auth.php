<?php

    /**
     * Configuration for the "HTTP Very Basic Auth"-middleware
     */
    return [
        // Username
        'user'              => env('SHIELD_USER'),

        // Password
        'password'          => env('SHIELD_PASSWORD'),

        // Environments where the middleware is active. Use "*" to protect all envs
        'envs'              => [
            '*'
        ],

        // Message to display if the user "opts out"/clicks "cancel"
        'error_message'     => 'You have to supply your credentials to access this resource.',

        // Message to display in the auth dialogue in some browsers (mainly Internet Explorer).
        // Realm is also used to define a "space" that should share credentials.
        'realm'             => 'Basic Auth',

        // If you prefer to use a view with your error message you can uncomment "error_view".
        // This will supersede your default response message
        // 'error_view'        => 'very_basic_auth::default'
    ];
