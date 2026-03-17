<?php

namespace MohamedSamy902\PulseAlert\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Package: laravel-pulse-alert
 * Model for storing logged errors.
 */
class ErrorLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pulse_alert_error_logs';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'context' => 'array',
    ];
}
