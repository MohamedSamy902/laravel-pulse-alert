<?php

/**
 * Package: laravel-pulse-alert
 * Default configuration file.
 */
return [
    'telegram' => [
        'bot_token' => env('PULSE_ALERT_TELEGRAM_TOKEN'),
        'chat_id'   => env('PULSE_ALERT_TELEGRAM_CHAT_ID'),
        'enabled'   => env('PULSE_ALERT_TELEGRAM_ENABLED', true),
        'queue_enabled' => env('PULSE_ALERT_TELEGRAM_QUEUE', false),
    ],
    'locale' => env('PULSE_ALERT_LOCALE', 'en'),
    'mail' => [
        'to'      => env('PULSE_ALERT_MAIL_TO'),
        'enabled' => env('PULSE_ALERT_MAIL_ENABLED', true),
    ],
    'report_time' => env('PULSE_ALERT_REPORT_TIME', '08:00'),
    'rate_limit' => [
        'enabled'      => env('PULSE_ALERT_RATE_ENABLED', true),
        'max_requests' => env('PULSE_ALERT_RATE_MAX', 20),
        'window'       => env('PULSE_ALERT_RATE_WINDOW', 1), // بالدقائق
    ],
    'priorities' => [
        'instant_notify' => ['CRITICAL', 'HIGH'],
        'critical_exceptions' => [
            \PDOException::class,
            \Error::class,
            \TypeError::class,
            \Illuminate\Database\QueryException::class,
        ],
        'high_keywords' => [
            'payment', 'auth', 'database', 'token', 'password', 'stripe', 'unauthorized',
        ],
        'ignore' => [
            \Illuminate\Validation\ValidationException::class,
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
            \Illuminate\Database\Eloquent\ModelNotFoundException::class,
            \Illuminate\Auth\AuthenticationException::class,
        ],
    ],
];
