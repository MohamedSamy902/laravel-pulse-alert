<?php

namespace MohamedSamy902\PulseAlert\Tests;

use MohamedSamy902\PulseAlert\PulseAlertServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            PulseAlertServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
