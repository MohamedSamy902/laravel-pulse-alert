<?php

namespace MohamedSamy902\PulseAlert\Console\Commands;

use Illuminate\Console\Command;
use MohamedSamy902\PulseAlert\Models\ErrorLog;
use Illuminate\Support\Facades\Mail;

/**
 * Package: laravel-pulse-alert
 * Artisan command to send daily summaries.
 */
class SendDailyErrorReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pulse-alert:daily-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily error report to administrator';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (!config('pulse-alert.mail.enabled') || !config('pulse-alert.mail.to')) {
            $this->warn('PulseAlert Mail is disabled or recipient not set.');
            return;
        }

        $yesterday = now()->subDay();
        $errors = ErrorLog::where('created_at', '>=', $yesterday->startOfDay())
            ->where('created_at', '<=', $yesterday->endOfDay())
            ->orderByRaw("FIELD(priority, 'CRITICAL', 'HIGH', 'MEDIUM', 'LOW')")
            ->get()
            ->groupBy('priority');

        if ($errors->isEmpty()) {
            $this->info('No errors yesterday. Happy coding!');
            return;
        }

        Mail::send('pulse-alert::emails.daily-report', ['errors' => $errors], function ($m) {
            $m->to(config('pulse-alert.mail.to'))
              ->subject('📊 PulseAlert Daily Report — ' . now()->subDay()->format('Y-m-d'));
        });

        $this->info('Daily report dispatched successfully.');
    }
}
