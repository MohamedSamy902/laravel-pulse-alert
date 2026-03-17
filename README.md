# ⚡ Laravel Pulse Alert

[Arabic Version (النسخة العربية)](README_AR.md)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mohamedsamy902/laravel-pulse-alert.svg)](https://packagist.org/packages/mohamedsamy902/laravel-pulse-alert)
[![PHP Version](https://img.shields.io/badge/PHP-8.2+-blue)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-11%7C12-red)](https://laravel.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

> Real-time error monitoring, intelligent alerts, and traffic surveillance for Laravel applications.

PulseAlert is a lightweight, zero-configuration package designed to keep you informed about your application's health. It goes beyond simple logging by classifying errors, sending instant Telegram notifications for critical issues, and providing daily summaries.

## ✨ Features
- **Intelligent Priority Engine**: Automatically classifies exceptions (CRITICAL, HIGH, MEDIUM, LOW).
- **Telegram Instant Alerts**: Receive 🔴 Critical and 🟠 High priority alerts directly on Telegram.
- **Smart Rate Limiting**: Prevents alert spam by silencing duplicate errors for 10 minutes.
- **Traffic Surveillance**: Middleware to detect and notify about suspicious request volume from single IPs.
- **Daily Error Digest**: Beautifully formatted email summaries sorted by importance.
- **Data Sanitization**: Automatically scrubs sensitive data (passwords, tokens) from stack traces.

## 🚀 Installation

### Step 1 — Install via Composer
```bash
composer require mohamedsamy902/laravel-pulse-alert
```

### Step 2 — Publish Config & Migrations
```bash
php artisan vendor:publish --tag=pulse-alert-config
php artisan vendor:publish --tag=pulse-alert-migrations
```

### Step 3 — Run Migrations
```bash
php artisan migrate
```

## 📋 Configuration

### Environment Variables
Add these keys to your `.env` file:

| Variable | Description | Default |
|---|---|---|
| `PULSE_ALERT_TELEGRAM_TOKEN` | Your Telegram Bot Token (@BotFather) | `null` |
| `PULSE_ALERT_TELEGRAM_CHAT_ID` | Your Telegram Chat/Group ID | `null` |
| `PULSE_ALERT_TELEGRAM_ENABLED` | Toggle Telegram alerts | `true` |
| `PULSE_ALERT_TELEGRAM_QUEUE` | Set `false` for instant delivery without Worker | `false` |
| `PULSE_ALERT_LOCALE` | Message language (`en` or `ar`) | `en` |
| `PULSE_ALERT_MAIL_TO` | Recipient for daily reports | `null` |
| `PULSE_ALERT_MAIL_ENABLED` | Toggle email reports | `true` |
| `PULSE_ALERT_REPORT_TIME` | Departure time for daily report | `08:00` |
| `PULSE_ALERT_RATE_MAX` | Max requests allowed per window | `20` |
| `PULSE_ALERT_RATE_WINDOW` | Time window in minutes | `1` |

### Registering the Logger (Laravel 11/12)
In your `bootstrap/app.php`, register the PulseAlert logger within the `withExceptions` block:

```php
use MohamedSamy902\PulseAlert\Services\ErrorLogger;

// ...
->withExceptions(function (Exceptions $exceptions) {
    app(ErrorLogger::class)->register($exceptions);
})
```

### Scheduling the Daily Report
In your `routes/console.php`, schedule the report to run daily:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('pulse-alert:daily-report')->dailyAt('08:00');
```

## 📖 Usage

### Traffic Surveillance Middleware
You can protect specific routes or groups from suspicious traffic using the provided middleware:

```php
// On a single route
Route::middleware(['pulse-alert.rate'])->post('/api/login', ...);

// On a group
Route::middleware(['pulse-alert.rate'])->group(function () {
    Route::post('/api/payment', ...);
    Route::post('/api/sensitive-data', ...);
});
```

### Manual Logging
You can manually log errors with a specific priority if needed:

```php
use MohamedSamy902\PulseAlert\Services\ErrorLogger;

try {
    // ... code
} catch (\Exception $e) {
    app(ErrorLogger::class)->log($e, 'CRITICAL');
}
```

## 🎯 Priority Classification Logic
- **CRITICAL**: Database errors, 500 status codes, or severe engine errors. (Instant Alert 🔴)
- **HIGH**: Keywords like "payment", "unauthorized", "auth", or "token" detected in message. (Instant Alert 🟠)
- **MEDIUM**: General exceptions that don't match critical/high criteria. (Daily Report only)
- **LOW**: Minor issues or manually logged low-priority events. (Daily Report only)

## 🧪 Testing
```bash
composer test
```

## 📄 License
The MIT License (MIT). Please see [License File](LICENSE) for more information.
