<?php

namespace Khuloodbatis\NotificationGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeFullNotificationCommand extends Command
{
    protected $signature = 'notification-generator:make-notification 
        {name : Notification name} 
        {--event=} 
        {--listener=}';

    protected $description = 'Generate notification, event, listener, and translation files';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $slug = Str::kebab($name);

        $this->createFromStub(
            'notification.stub',
            app_path("Notifications/{$name}.php"),
            [
                'DummyClass' => $name,
                'DummySlug' => $slug,
                'DummyNamespace' => 'App\\Notifications'
            ]
        );

        $event = $this->option('event') ?: "{$name}Event";
        $this->createFromStub(
            'event.stub',
            app_path("Events/{$event}.php"),
            [
                'DummyClass' => $event,
                'DummyNamespace' => 'App\\Events'
            ]
        );

        $listener = $this->option('listener') ?: "{$name}Listener";
        $this->createFromStub(
            'listener.stub',
            app_path("Listeners/{$listener}.php"),
            [
                'DummyClass' => $listener,
                'DummyNamespace' => 'App\\Listeners',
                'DummyEvent' => "App\\Events\\{$event}",
                'DummyNotification' => "App\\Notifications\\{$name}"
            ]
        );

        $this->copyLang();

        $this->info("Notification package generated successfully!");
    }

    protected function createFromStub($stub, $target, $replace)
    {
        $content = file_get_contents(__DIR__ . "/../stubs/{$stub}");
        foreach ($replace as $key => $value) {
            $content = str_replace($key, $value, $content);
        }
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }
        file_put_contents($target, $content);
    }

    protected function copyLang()
    {
        foreach (['en', 'ar'] as $lang) {
            $folder = lang_path($lang);
            if (!is_dir($folder)) mkdir($folder, 0777, true);

            copy(
                __DIR__ . "/../stubs/lang/{$lang}.stub",
                "{$folder}/notifications.php"
            );
        }
    }
}
