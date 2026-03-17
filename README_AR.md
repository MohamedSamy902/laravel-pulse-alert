# ⚡ لارافل بولس أليرت (Laravel Pulse Alert)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mohamedsamy902/laravel-pulse-alert.svg)](https://packagist.org/packages/mohamedsamy902/laravel-pulse-alert)
[![PHP Version](https://img.shields.io/badge/PHP-8.2+-blue)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-11%7C12-red)](https://laravel.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

> مراقبة الأخطاء في الوقت الفعلي، تنبيهات ذكية، ومراقبة حركة المرور المشبوهة لتطبيقات لارافل.

PulseAlert هي حزمة خفيفة الوزن، سهلة الإعداد، مصممة لإبقائك على اطلاع دائم وبشكل فوري بحالة تطبيقك. تتجاوز الحزمة مجرد تسجيل الأخطاء العادي، حيث تقوم بتصنيف الأخطاء حسب الأهمية، وإرسال تنبيهات فورية عبر تيليجرام للأخطاء الحرجة، وتقديم ملخصات يومية.

## ✨ المميزات
- **محرك أولويات ذكي**: يصنف الاستثناءات تلقائياً إلى (CRITICAL, HIGH, MEDIUM, LOW).
- **تنبيهات تيليجرام فورية**: استلم تنبيهات 🔴 حرجة (Critical) و 🟠 عالية الأهمية (High) مباشرة على تيليجرام.
- **تحديد معدل التنبيهات الذكي**: يمنع إزعاج التنبيهات المتكررة عن طريق إسكات الأخطاء المتكررة لمدة 10 دقائق.
- **مراقبة حركة المرور**: "Middleware" لاكتشاف وتنبيهك عن عدد الطلبات المشبوه من عنوان IP واحد.
- **ملخص الأخطاء اليومي**: تقارير بريد إلكتروني منسقة بشكل جميل ومصنفة حسب الأهمية.
- **تطهير البيانات**: يقوم تلقائياً بحذف البيانات الحساسة (كلمات المرور، الرموز) من الـ Trace.

## 🚀 التثبيت

### الخطوة 1 — التثبيت عبر Composer
```bash
composer require mohamedsamy902/laravel-pulse-alert
```

### الخطوة 2 — نشر الإعدادات والمهاجرات
```bash
php artisan vendor:publish --tag=pulse-alert-config
php artisan vendor:publish --tag=pulse-alert-migrations
```

### الخطوة 3 — تشغيل المهاجرات
```bash
php artisan migrate
```

## 📋 الإعدادات

### متغيرات البيئة (.env)
أضف هذه المفاتيح إلى ملف `.env` الخاص بك:

| المتغير | الوصف | القيمة الافتراضية |
|---|---|---|
| `PULSE_ALERT_TELEGRAM_TOKEN` | توكن بوت تيليجرام (@BotFather) | `null` |
| `PULSE_ALERT_TELEGRAM_CHAT_ID` | معرف المحادثة أو المجموعة | `null` |
| `PULSE_ALERT_TELEGRAM_ENABLED` | تفعيل/تعطيل تنبيهات تيليجرام | `true` |
| `PULSE_ALERT_TELEGRAM_QUEUE` | اجعلها `false` للإرسال الفوري بدون خادم انتظار | `false` |
| `PULSE_ALERT_LOCALE` | لغة الرسائل (`en` أو `ar`) | `en` |
| `PULSE_ALERT_MAIL_TO` | البريد المستلم للملخص اليومي | `null` |
| `PULSE_ALERT_MAIL_ENABLED` | تفعيل/تعطيل تقارير البريد | `true` |
| `PULSE_ALERT_REPORT_TIME` | وقت إرسال التقرير اليومي | `08:00` |
| `PULSE_ALERT_RATE_MAX` | أقصى عدد طلبات مسموح به | `20` |
| `PULSE_ALERT_RATE_WINDOW` | النافذة الزمنية بالدقائق | `1` |

### تسجيل المسجل (Laravel 11/12)
في ملف `bootstrap/app.php` الخاص بك، قم بتسجيل مسجل PulseAlert داخل كتلة `withExceptions`:

```php
use MohamedSamy902\PulseAlert\Services\ErrorLogger;

// ...
->withExceptions(function (Exceptions $exceptions) {
    app(ErrorLogger::class)->register($exceptions);
})
```

### جدولة التقرير اليومي
في ملف `routes/console.php` الخاص بك، قم بجدولة التقرير ليعمل يومياً:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('pulse-alert:daily-report')->dailyAt('08:00');
```

## 📖 الاستخدام

### "Middleware" مراقبة حركة المرور
يمكنك حماية مسارات معينة من الطلبات المشبوهة باستخدام الـ middleware المرفق:

```php
// على مسار واحد
Route::middleware(['pulse-alert.rate'])->post('/api/login', ...);

// على مجموعة مسارات
Route::middleware(['pulse-alert.rate'])->group(function () {
    Route::post('/api/payment', ...);
    Route::post('/api/sensitive-data', ...);
});
```

### التسجيل اليدوي
يمكنك تسجيل الأخطاء يدوياً مع تحديد الأولوية إذا لزم الأمر:

```php
use MohamedSamy902\PulseAlert\Services\ErrorLogger;

try {
    // ... كود
} catch (\Exception $e) {
    app(ErrorLogger::class)->log($e, 'CRITICAL');
}
```

## 🎯 منطق تصنيف الأولويات
- **CRITICAL**: أخطاء قاعدة البيانات، أكواد الحالة 500، أو أخطاء المحرك الشديدة. (تنبيه فوري 🔴)
- **HIGH**: الكلمات الدليلية مثل "payment", "unauthorized", "auth", أو "token" المكتشفة في الرسالة. (تنبيه فوري 🟠)
- **MEDIUM**: الاستثناءات العامة التي لا تطابق معايير الخطورة العالية. (تقرير يومي فقط)
- **LOW**: المشكلات البسيطة أو الأحداث المسجلة يدوياً ذات الأولوية المنخفضة. (تقرير يومي فقط)

## 🧪 الاختبار
```bash
composer test
```

## 📄 الترخيص
ترخيص MIT. يرجى الاطلاع على [ملف الترخيص](LICENSE) لمزيد من المعلومات.
