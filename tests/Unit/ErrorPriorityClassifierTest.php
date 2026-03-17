<?php

namespace MohamedSamy902\PulseAlert\Tests\Unit;

use MohamedSamy902\PulseAlert\Services\ErrorPriorityClassifier;
use MohamedSamy902\PulseAlert\Tests\TestCase;
use Exception;
use PDOException;

class ErrorPriorityClassifierTest extends TestCase
{
    /** @test */
    public function it_classifies_pdo_exception_as_critical()
    {
        $e = new PDOException("SQLSTATE[HY000] [2002] Connection refused");
        $this->assertEquals('CRITICAL', ErrorPriorityClassifier::classify($e));
    }

    /** @test */
    public function it_classifies_payment_related_exception_as_high()
    {
        $e = new Exception("The payment failed due to credit card expiry.");
        $this->assertEquals('HIGH', ErrorPriorityClassifier::classify($e));
    }

    /** @test */
    public function it_classifies_generic_exception_as_medium()
    {
        $e = new Exception("Something just happened.");
        $this->assertEquals('MEDIUM', ErrorPriorityClassifier::classify($e));
    }
}
