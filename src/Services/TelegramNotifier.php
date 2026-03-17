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
        $locale = config('pulse-alert.locale', 'en');

        if (!$token || !$chatId) return false;

        $emoji = $log->priority === 'CRITICAL' ? '🔴' : '🟠';
        
        if ($locale === 'ar') {
            $message = "<b>{$emoji} تم اكتشاف خطأ بنظام {$log->priority}</b>\n\n"
                     . "📌 <b>النوع:</b> <code>" . e($log->exception_class) . "</code>\n"
                     . "💬 <b>الرسالة:</b> " . e($log->message) . "\n"
                     . "📁 <b>الملف:</b> <code>" . e($log->file) . ":" . $log->line . "</code>\n"
                     . "🌐 <b>الرابط:</b> " . e($log->url) . "\n"
                     . "⏰ <b>الوقت:</b> " . $log->created_at . "\n\n"
                     . "💡 <b>إجراء سريع:</b> تحقق من سجلات قاعدة البيانات للتفاصيل الكاملة.";
        } else {
            $message = "<b>{$emoji} {$log->priority} ERROR DETECTED</b>\n\n"
                     . "📌 <b>Exception:</b> <code>" . e($log->exception_class) . "</code>\n"
                     . "💬 <b>Message:</b> " . e($log->message) . "\n"
                     . "📁 <b>File:</b> <code>" . e($log->file) . ":" . $log->line . "</code>\n"
                     . "🌐 <b>URL:</b> " . e($log->url) . "\n"
                     . "⏰ <b>Time:</b> " . $log->created_at . "\n\n"
                     . "💡 <b>Quick Action:</b> Check the database logs for the full stack trace.";
        }

        $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id'    => $chatId,
            'text'       => $message,
            'parse_mode' => 'HTML',
        ]);

        if (!$response->successful()) {
            \Illuminate\Support\Facades\Log::error("PulseAlert Telegram API Error: " . $response->body());
        }

        return $response->successful();
    }
}
