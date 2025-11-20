<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    'default' => env('LOG_CHANNEL', 'stack'),

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    'channels' => [

        // 🧱 Genel Stack
        'stack' => [
            'driver' => 'stack',
            'channels' => explode(',', (string) env('LOG_STACK', 'single')),
            'ignore_exceptions' => false,
        ],

        // 📄 Tek Dosya Log
        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        // 📅 Günlük Log
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => env('LOG_DAILY_DAYS', 14),
            'replace_placeholders' => true,
        ],

        // 💬 Slack Bildirimleri
        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => env('LOG_SLACK_USERNAME', 'Laravel Log'),
            'emoji' => env('LOG_SLACK_EMOJI', ':boom:'),
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        // 📡 Papertrail
        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://' . env('PAPERTRAIL_URL') . ':' . env('PAPERTRAIL_PORT'),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        // 🧠 Cockpit Asset Performans Logu
        'cockpit' => [
            'driver' => 'single',
            'path' => storage_path('logs/cockpit.log'),
            'level' => 'info',
        ],

        // ⚠️ Cockpit Asset Uyarı Logu (1000ms+)
        'cockpit_alert' => [
            'driver' => 'single',
            'path' => storage_path('logs/cockpit-alert.log'),
            'level' => 'warning',
        ],

        // 🛍️ Vendor Erişim Logu
        'vendor' => [
            'driver' => 'single',
            'path' => storage_path('logs/vendor-access.log'),
            'level' => 'info',
        ],

        // 📦 Export Logları
        'exports' => [
            'driver' => 'single',
            'path' => storage_path('logs/exports.log'),
            'level' => 'info',
        ],

        // 🧪 Standart Hata
        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'handler_with' => [
                'stream' => 'php://stderr',
            ],
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'processors' => [PsrLogMessageProcessor::class],
        ],

        // 🧾 Syslog
        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => env('LOG_SYSLOG_FACILITY', LOG_USER),
            'replace_placeholders' => true,
        ],

        // 🧱 Errorlog
        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        // 🚫 Null Log
        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        // 🚨 Acil Durum Logu
        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        // 🎯 Kampanya Logu
        'incentive' => [
            'driver' => 'single',
            'path' => storage_path('logs/incentive.log'),
            'level' => 'info',
        ],

        // 🧠 Sprint Refleksi Logu
        'sprint' => [
            'driver' => 'single',
            'path' => storage_path('logs/sprint.log'),
            'level' => 'info',
        ],

        // 💳 Payment Gateway Logu
        'payment' => [
            'driver' => 'daily',
            'path' => storage_path('logs/payment.log'),
            'level' => 'info',
            'days' => 30,
        ],
    ],
];