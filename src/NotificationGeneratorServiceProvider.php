<?php

namespace Khuloodbatis\NotificationGenerator;

use Illuminate\Support\ServiceProvider;
use Khuloodbatis\NotificationGenerator\Commands\MakeFullNotificationCommand;

class NotificationGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeFullNotificationCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/stubs' => base_path('stubs/khuloodbatis'),
            ], 'khuloodbatis-stubs');
        }
    }
}
