<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'web',  // Gunakan 'web' atau 'api' jika lebih sesuai
        'passwords' => 't_pengguna',  // Mengubah ke tabel yang sesuai
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | You can define more guards for specific usage like API or web.
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 't_pengguna', // Mengarah ke provider yang sesuai
        ],
        'api' => [
            'driver' => 'sanctum',
            'provider' => 't_pengguna',  // Menggunakan provider 't_pengguna'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    */

    'providers' => [
        't_pengguna' => [
            'driver' => 'eloquent',  // Menggunakan Eloquent untuk model Pengguna
            'model' => App\Models\Pengguna::class,  // Model yang sesuai
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    */

    'passwords' => [
        't_pengguna' => [
            'provider' => 't_pengguna',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | times out and the user is prompted to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => 10800,

];
