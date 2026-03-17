<?php

namespace MohamedSamy902\PulseAlert;

use Illuminate\Support\ServiceProvider;
use MohamedSamy902\PulseAlert\Console\Commands\SendDailyErrorReport;
use MohamedSamy902\PulseAlert\Http\Middleware\PulseAlertRateLimit;
use MohamedSamy902\PulseAlert\Services\ErrorLogger;

/**
 * Package: laravel-pulse-alert
 * ServiceProvider for initializing all components.
 */
class PulseAlertServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // دمج الإعدادات الافتراضية
        $this->mergeConfigFrom(__DIR__.'/../config/pulse-alert.php', 'pulse-alert');

        // تسجيل الـ ErrorLogger كـ Singleton
        $this->app->singleton(ErrorLogger::class, function ($app) {
            return new ErrorLogger();
        });
    }

    public function boot(): void
    {
        // تسجيل الـ Middleware تلقائياً في مجموعات الويب والـ API
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', PulseAlertRateLimit::class);
        $router->pushMiddlewareToGroup('api', PulseAlertRateLimit::class);

        // نشر الملفات (Publishing)
        if ($this->app->runningInConsole()) {
            $this->publishResources();
            $this->commands([
                SendDailyErrorReport::class,
            ]);
        }

        // تحميل الـ Views والمهاجرات
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'pulse-alert');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // تسجيل الـ Middleware
        $this->app['router']->aliasMiddleware('pulse-alert.rate', PulseAlertRateLimit::class);
    }

    protected function publishResources(): void
    {
        $this->publishes([
            __DIR__.'/../config/pulse-alert.php' => config_path('pulse-alert.php'),
        ], 'pulse-alert-config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'pulse-alert-migrations');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/pulse-alert'),
        ], 'pulse-alert-views');
    }
}
