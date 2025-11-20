<?php

namespace Khuloodbatis\NotificationGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeFullNotificationCommand extends Command
{
    protected $signature = 'notification-generator:make-notification 
        {name? : Notification name}
        {--event= : Event name}
        {--listener= : Listener name}';

    protected $description = 'Generate notification, event, listener, and translation files';

    public function handle()
    {
        // ----------------------------------------------------
        // 1. NOTIFICATION NAME
        // ----------------------------------------------------
        $inputName = $this->argument('name') ?: $this->ask('Input notification name');

        if (empty($inputName)) {
            $this->error('Notification name is required!');
            return 1;
        }

        // Parse folder + class name
        $parsedNotification = $this->parseName($inputName, 'App\\Notifications');
        $className = $parsedNotification['class'];
        $slug = Str::kebab($className);

        // ----------------------------------------------------
        // 2. EVENT NAME
        // ----------------------------------------------------
        $eventInput = $this->option('event') ?: $this->ask('Event name', "{$className}Event");
        $parsedEvent = $this->parseName($eventInput, 'App\\Events');

        // ----------------------------------------------------
        // 3. LISTENER NAME
        // ----------------------------------------------------
        $listenerInput = $this->option('listener') ?: $this->ask('Listener name', "{$className}Listener");
        $parsedListener = $this->parseName($listenerInput, 'App\\Listeners');

        // ----------------------------------------------------
        // 4. GENERATE NOTIFICATION
        // ----------------------------------------------------
        $this->createFromStub(
            'notification.stub',
            app_path("Notifications/{$parsedNotification['path']}/{$parsedNotification['class']}.php"),
            [
                'DummyClass'     => $parsedNotification['class'],
                'DummyNamespace' => $parsedNotification['namespace'],
                'DummySlug'      => $slug,
                'DummyPath'      => $parsedNotification['dotPath'], // ← HERE
            ]
        );

        // ----------------------------------------------------
        // 5. GENERATE MARKDOWN TEMPLATE
        // ----------------------------------------------------
        $this->createFromStub(
            'markdown.stub',
            resource_path("views/emails/{$parsedNotification['path']}/{$slug}.blade.php"),
            [
                'DummySlug' => $slug
            ]
        );


        // ----------------------------------------------------
        // 6. GENERATE EVENT
        // ----------------------------------------------------
        $this->createFromStub(
            'event.stub',
            app_path("Events/{$parsedEvent['path']}/{$parsedEvent['class']}.php"),
            [
                'DummyClass' => $parsedEvent['class'],
                'DummyNamespace' => $parsedEvent['namespace'],
            ]
        );

        // ----------------------------------------------------
        // 7. GENERATE LISTENER
        // ----------------------------------------------------
        $this->createFromStub(
            'listener.stub',
            app_path("Listeners/{$parsedListener['path']}/{$parsedListener['class']}.php"),
            [
                'DummyClass' => $parsedListener['class'],
                'DummyNamespace' => $parsedListener['namespace'],
                'DummyEvent' => $parsedEvent['namespace'] . '\\' . $parsedEvent['class'],
                'DummyNotification' => $parsedNotification['namespace'] . '\\' . $parsedNotification['class'],
            ]
        );

        // ----------------------------------------------------
        // 8. TRANSLATION FILES
        // ----------------------------------------------------
        $this->generateLangFiles($slug, $parsedNotification);

        // ----------------------------------------------------
        // 9. SUMMARY
        // ----------------------------------------------------
        $this->info("✅ Notification package generated successfully!\n");
        $this->info("📁 Files created:");
        $this->line("   - app/Notifications/{$parsedNotification['path']}/{$parsedNotification['class']}.php");
        $this->line("   - app/Events/{$parsedEvent['path']}/{$parsedEvent['class']}.php");
        $this->line("   - app/Listeners/{$parsedListener['path']}/{$parsedListener['class']}.php");
        $this->line("   - resources/views/mail/{$slug}.blade.php");
        $this->line("   - lang/en/{$slug}.php");
        $this->line("   - lang/ar/{$slug}.php");

        return 0;
    }

    // --------------------------------------------------------
    // Parse folder + class + namespace
    // --------------------------------------------------------
    protected function parseName($input, $baseNamespace = 'App')
    {
        $input = str_replace('\\', '/', $input);
        $parts = explode('/', trim($input, '/'));

        // Class always StudlyCase
        $class = Str::studly(array_pop($parts));

        // Namespace uses StudlyCase folders
        $namespaceParts = array_map(fn($p) => Str::studly($p), $parts);

        $namespace = $baseNamespace;
        if (!empty($namespaceParts)) {
            $namespace .= '\\' . implode('\\', $namespaceParts);
        }

        // PATH MUST BE LOWERCASE — THIS FIXES YOUR ISSUE
        $lowerPath = implode('/', array_map(fn($p) => Str::lower($p), $parts));

        // DOT PATH for Notification = lowercase.orders
        $dotPath = str_replace('/', '.', $lowerPath);

        return [
            'class' => $class,
            'namespace' => $namespace,
            'path' => $lowerPath,     // ← for file system
            'dotPath' => $dotPath,    // ← for view + lang keys
        ];
    }

    // --------------------------------------------------------
    // Create file from stub
    // --------------------------------------------------------
    protected function createFromStub($stub, $target, $replace)
    {
        $content = file_get_contents(__DIR__ . "/../stubs/{$stub}");

        foreach ($replace as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        $directory = dirname($target);

        // FIX: ensure full directory path is created recursively
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($target, $content);
    }

    // --------------------------------------------------------
    // Generate lang files (EN + AR)
    // --------------------------------------------------------
 protected function generateLangFiles($slug, $parsed)
{
    foreach (['en', 'ar'] as $lang) {

        $langFolder = lang_path("$lang/emails/{$parsed['path']}");

        if (!is_dir($langFolder)) {
            mkdir($langFolder, 0775, true);
        }

        $target = "{$langFolder}/{$slug}.php";

        $this->createFromStub(
            "lang/{$lang}.stub",
            $target,
            [
                'DummySlug' => $slug,
                'DummyPath' => $parsed['dotPath'], // for notification stub
            ]
        );
  
}
