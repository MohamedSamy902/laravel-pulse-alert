<?php

namespace MohamedSamy902\PulseAlert\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MohamedSamy902\PulseAlert\Models\ErrorLog;
use MohamedSamy902\PulseAlert\Services\ErrorLogger;
use MohamedSamy902\PulseAlert\Tests\TestCase;
use Exception;

class ErrorLoggingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_log_an_exception_to_database()
    {
        $logger = new ErrorLogger();
        $e = new Exception("Test Exception");
        
        $logger->log($e);

        $this->assertDatabaseHas('pulse_alert_error_logs', [
            'message' => 'Test Exception',
            'exception_class' => Exception::class,
        ]);
    }
}
