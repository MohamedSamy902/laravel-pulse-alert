<?php

namespace MohamedSamy902\PulseAlert\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Package: laravel-pulse-alert
 * Service to monitor suspicious traffic.
 */
class RateLimitMonitor
{
    /**
     * Check if a specific IP is exceeding rate limits.
     */
    public static function check(string $ip): void
    {
        $config = config('pulse-alert.rate_limit');
        if (!$config['enabled']) return;

        $key = "pulse_rate_limit_" . md5($ip);
        $hits = Cache::get($key, 0) + 1;
        Cache::put($key, $hits, now()->addMinutes($config['window']));

        if ($hits == $config['max_requests']) {
            self::notifySuspiciousTraffic($ip, $hits);
        }
    }

    /**
     * Send notification about excessive requests.
     */
    protected static function notifySuspiciousTraffic(string $ip, int $hits): void
    {
        $token = config('pulse-alert.telegram.bot_token');
        $chatId = config('pulse-alert.telegram.chat_id');

        if (!$token || !$chatId) return;

        $message = "<b>⚠️ SUSPICIOUS TRAFFIC DETECTED</b>\n\n"
                 . "👤 <b>IP Address:</b> <code>{$ip}</code>\n"
                 . "📉 <b>Requests:</b> {$hits} in " . config('pulse-alert.rate_limit.window') . " min\n"
                 . "🛡️ <b>Status:</b> User is exceeding rate limits.";

        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id'    => $chatId,
            'text'       => $message,
            'parse_mode' => 'HTML',
        ]);
    }
}
