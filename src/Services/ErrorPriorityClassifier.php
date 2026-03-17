<?php

namespace MohamedSamy902\PulseAlert\Services;

use Throwable;

/**
 * Package: laravel-pulse-alert
 * Service to classify errors by priority.
 */
class ErrorPriorityClassifier
{
    /**
     * Classify exception into CRITICAL, HIGH, MEDIUM, or LOW.
     */
    public static function classify(Throwable $e): string
    {
        $config = config('pulse-alert.priorities');

        // 1. التحقق من الـ Class نفسه في القائمة الحرجة
        foreach ($config['critical_exceptions'] as $class) {
            if ($e instanceof $class) return 'CRITICAL';
        }

        // 2. التحقق من الـ HTTP Code
        if (method_exists($e, 'getStatusCode')) {
            if ($e->getStatusCode() >= 500) return 'CRITICAL';
        }

        // 3. التحقق من الكلمات الدليلية في الرسالة
        $message = strtolower($e->getMessage());
        foreach ($config['high_keywords'] as $keyword) {
            if (str_contains($message, $keyword)) return 'HIGH';
        }

        return 'MEDIUM';
    }
}
