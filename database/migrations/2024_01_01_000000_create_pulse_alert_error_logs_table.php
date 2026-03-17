<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Package: laravel-pulse-alert
 * Migration to create error logs table.
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pulse_alert_error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('priority')->index();
            $table->string('exception_class');
            $table->text('message');
            $table->string('file');
            $table->integer('line');
            $table->longText('trace')->nullable();
            $table->string('url')->nullable();
            $table->string('method', 10)->nullable();
            $table->json('context')->nullable();
            $table->boolean('notified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pulse_alert_error_logs');
    }
};
