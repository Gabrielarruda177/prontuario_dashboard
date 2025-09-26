<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],

        'medico' => [
            'driver' => 'session',
            'provider' => 'medicos',
        ],

        'enfermeiro' => [
            'driver' => 'session',
            'provider' => 'enfermeiros', // usa Usuario para autenticação
        ],
    ],

    'providers' => [

        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\Usuario::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],

        'medicos' => [
            'driver' => 'eloquent',
            'model' => App\Models\Medico::class,
        ],

        'enfermeiros' => [
            'driver' => 'eloquent',
            'model' => App\Models\Usuario::class, // 🔹 MUDANÇA AQUI
        ],
    ],

    'passwords' => [

        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

    ],

    'password_timeout' => 10800,

];
