<?php

namespace MohamedSamy902\PulseAlert\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MohamedSamy902\PulseAlert\Models\ErrorLog;
use MohamedSamy902\PulseAlert\Services\TelegramNotifier;

/**
 * Package: laravel-pulse-alert
 * Job to send Telegram alerts asynchronously.
 */
class SendTelegramAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(public ErrorLog $log) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // منع التكرار المزعج لنفس الخطأ في خلال 10 دقائق
        $cacheKey = 'pulse_alert_sent_' . md5($this->log->message . $this->log->file);
        if (cache()->has($cacheKey)) return;

        if (TelegramNotifier::send($this->log)) {
            $this->log->update(['notified' => true]);
            cache()->put($cacheKey, true, now()->addMinutes(10));
        } else {
            throw new \Exception("Failed to send Telegram alert.");
        }
    }
}
