<?php

namespace MohamedSamy902\PulseAlert\Services;

use Illuminate\Support\Facades\Http;
use MohamedSamy902\PulseAlert\Models\ErrorLog;

/**
 * Package: laravel-pulse-alert
 * Service for sending Telegram notifications.
 */
class TelegramNotifier
{
    /**
     * Send structured error notification to Telegram.
     */
    public static function send(ErrorLog $log): bool
    {
        if (!config('pulse-alert.telegram.enabled')) return false;

        $token = config('pulse-alert.telegram.bot_token');
        $chatId = config('pulse-alert.telegram.chat_id');

        if (!$token || !$chatId) return false;

        $emoji = $log->priority === 'CRITICAL' ? '🔴' : '🟠';
        
        $message = "<b>{$emoji} {$log->priority} ERROR DETECTED</b>\n\n"
                 . "📌 <b>Exception:</b> <code>" . e($log->exception_class) . "</code>\n"
                 . "💬 <b>Message:</b> " . e($log->message) . "\n"
                 . "📁 <b>File:</b> <code>" . e($log->file) . ":" . $log->line . "</code>\n"
                 . "🌐 <b>URL:</b> " . e($log->url) . "\n"
                 . "⏰ <b>Time:</b> " . $log->created_at . "\n\n"
                 . "💡 <b>Quick Action:</b> Check the database logs for the full stack trace.";

        $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id'    => $chatId,
            'text'       => $message,
            'parse_mode' => 'HTML',
        ]);

        return $response->successful();
    }
}
