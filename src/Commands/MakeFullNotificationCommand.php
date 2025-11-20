<?php

namespace Khuloodbatis\NotificationGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeFullNotificationCommand extends Command
{
    protected $signature = 'notification-generator:make-notification {name? : Notification name} {--event=} {--listener=}';

    protected $description = 'Generate notification, event, listener, and translation files';

    public function handle()
    {
        // Ask for notification name
        $inputName = $this->argument('name') ?: $this->ask('Input notification name');
        
        if (empty($inputName)) {
            $this->error('Notification name is required!');
            return 1;
        }
        
        $name = Str::studly($inputName);
        $slug = Str::kebab($name);

        // Ask for event name
        $eventInput = $this->option('event') ?: $this->ask('Event name', "{$name}Event");
        $event = Str::studly($eventInput);

        // Ask for listener name
        $listenerInput = $this->option('listener') ?: $this->ask('Listener name', "{$name}Listener");
        $listener = Str::studly($listenerInput);

        // Generate Notification
        $this->createFromStub(
            'notification.stub',
            app_path("Notifications/{$name}.php"),
            [
                'DummyClass' => $name,
                'DummySlug' => $slug,
                'DummyNamespace' => 'App\\Notifications'
            ]
        );

        // Generate Markdown View
        $this->createFromStub(
            'markdown.stub',
            resource_path("views/mail/{$slug}.blade.php"),
            [
                'DummySlug' => $slug
            ]
        );

        // Generate Event
        $this->createFromStub(
            'event.stub',
            app_path("Events/{$event}.php"),
            [
                'DummyClass' => $event,
                'DummyNamespace' => 'App\\Events'
            ]
        );

        // Generate Listener
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

        // Generate Language Files
        $this->generateLangFiles($slug);

        $this->info("✅ Notification package generated successfully!");
        $this->info("📁 Files created:");
        $this->line("   - app/Notifications/{$name}.php");
        $this->line("   - app/Events/{$event}.php");
        $this->line("   - app/Listeners/{$listener}.php");
        $this->line("   - resources/views/mail/{$slug}.blade.php");
        $this->line("   - lang/en/{$slug}.php");
        $this->line("   - lang/ar/{$slug}.php");
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

    protected function generateLangFiles($slug)
    {
        foreach (['en', 'ar'] as $lang) {
            $folder = lang_path($lang);
            if (!is_dir($folder)) mkdir($folder, 0777, true);

            $this->createFromStub(
                "lang/{$lang}.stub",
                "{$folder}/{$slug}.php",
                ['DummySlug' => $slug]
            );
        }
    }
}
