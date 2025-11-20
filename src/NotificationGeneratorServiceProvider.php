<?php

namespace Khuloodbatis\NotificationGenerator\Providers;

use Illuminate\Support\ServiceProvider;
use Khuloodbatis\NotificationGenerator\Commands\MakeFullNotificationCommand;

class NotificationGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register the command
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeFullNotificationCommand::class,
            ]);

            // Allow users to publish stubs
            $this->publishes([
                __DIR__ . '/../../stubs' => base_path('stubs/notification-generator'),
            ], 'notification-generator-stubs');
        }
    }
}
