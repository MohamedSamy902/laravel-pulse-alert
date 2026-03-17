<?php

namespace MohamedSamy902\PulseAlert\Services;

use Illuminate\Foundation\Configuration\Exceptions;
use MohamedSamy902\PulseAlert\Models\ErrorLog;
use MohamedSamy902\PulseAlert\Jobs\SendTelegramAlert;
use Throwable;
use Illuminate\Support\Facades\Log;

/**
 * Package: laravel-pulse-alert
 * Central service for logging exceptions.
 */
class ErrorLogger
{
    /**
     * Register the logger into Laravel 11/12 Exceptions handler.
     */
    public function register(Exceptions $exceptions): void
    {
        $exceptions->report(function (Throwable $e) {
            $this->log($e);
        });
    }

    /**
     * Core logging logic.
     */
    public function log(Throwable $e, ?string $manualPriority = null): void
    {
        if (!$this->shouldLog($e)) return;

        try {
            $priority = $manualPriority ?? ErrorPriorityClassifier::classify($e);

            $log = ErrorLog::create([
                'priority'        => $priority,
                'exception_class' => get_class($e),
                'message'         => $e->getMessage(),
                'file'            => $e->getFile(),
                'line'            => $e->getLine(),
                'trace'           => $this->sanitizeTrace($e->getTraceAsString()),
                'url'             => request()->fullUrl(),
                'method'          => request()->method(),
                'context'         => [
                    'ip'      => request()->ip(),
                    'user_id' => auth()->id() ?? null,
                    'input'   => request()->except(['password', 'token', 'secret', 'key']),
                ],
            ]);

            // إرسال تنبيه فوري إذا كانت الأولوية عالية
            if (in_array($priority, config('pulse-alert.priorities.instant_notify'))) {
                if (config('pulse-alert.telegram.queue_enabled')) {
                    SendTelegramAlert::dispatch($log)->onQueue('notifications');
                } else {
                    TelegramNotifier::send($log);
                    $log->update(['notified' => true]);
                }
            }

        } catch (\Exception $ex) {
            Log::emergency("PulseAlert failed to log error: " . $ex->getMessage());
        }
    }

    protected function shouldLog(Throwable $e): bool
    {
        foreach (config('pulse-alert.priorities.ignore') as $class) {
            if ($e instanceof $class) return false;
        }
        return true;
    }

    protected function sanitizeTrace(string $trace): string
    {
        $sensitive = ['password', 'token', 'secret', 'key', 'auth'];
        $pattern = '/(' . implode('|', $sensitive) . ')\" => \"[^\"]+\"/i';
        return preg_replace($pattern, '$1" => "[FILTERED]"', $trace);
    }
}
