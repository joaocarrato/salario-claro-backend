<?php

declare(strict_types=1);
use Illuminate\Auth\AuthServiceProvider;
use Illuminate\Broadcasting\BroadcastServiceProvider;
use Illuminate\Bus\BusServiceProvider;
use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Cookie\CookieServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Foundation\Providers\ConsoleSupportServiceProvider;
use Illuminate\Foundation\Providers\FoundationServiceProvider;
use Illuminate\Hashing\HashServiceProvider;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Notifications\NotificationServiceProvider;
use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Pipeline\PipelineServiceProvider;
use Illuminate\Queue\QueueServiceProvider;
use Illuminate\Session\SessionServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use Illuminate\View\ViewServiceProvider;

return [
    'name' => env('APP_NAME', 'Salario Claro API'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'America/Sao_Paulo'),
    'locale' => env('APP_LOCALE', 'pt_BR'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'pt_BR'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [],
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],
    'providers' => [
        AuthServiceProvider::class,
        BroadcastServiceProvider::class,
        BusServiceProvider::class,
        CacheServiceProvider::class,
        ConsoleSupportServiceProvider::class,
        CookieServiceProvider::class,
        DatabaseServiceProvider::class,
        EncryptionServiceProvider::class,
        FilesystemServiceProvider::class,
        FoundationServiceProvider::class,
        HashServiceProvider::class,
        MailServiceProvider::class,
        NotificationServiceProvider::class,
        PaginationServiceProvider::class,
        PipelineServiceProvider::class,
        QueueServiceProvider::class,
        SessionServiceProvider::class,
        TranslationServiceProvider::class,
        ValidationServiceProvider::class,
        ViewServiceProvider::class,
    ],
];
