# notification-generator
# Notification Generator for Laravel  
A lightweight Laravel package that automatically generates a full Notification workflow:

- Notification (Markdown or Default)
- Event
- Listener
- Translations (English + Arabic)
- Auto-registers service provider using Laravel Auto-Discovery
- Clean, simple, and extendable architecture

Perfect for teams who repeatedly create similar notifications and want consistent scaffolding with one Artisan command.

---

## 🚀 Features

- Generate **Notification + Event + Listener** in one command  
- Auto-create **Markdown email template**  
- Auto-create **Localization files** (`resources/lang/en` + `resources/lang/ar`)  
- Supports **Laravel 10 & Laravel 11**  
- PSR-4 package structure  
- Plug-and-play service provider auto-discovery  
- Clean code, extendable stubs, and maintainable architecture

---

## 🧩 Installation

Require the package through Composer:

```bash
composer require khuloodbatis/notification-generator:dev-main
If you use a specific version:
composer require khuloodbatis/notification-generator:^1.0

Laravel will automatically register the service provider via auto-discovery.

📦 Publish Stubs & Config (optional)
If your package provides stubs or configuration:

bash
Copy code
php artisan vendor:publish --tag=notification-generator
🛠️ Usage
Generate a complete Notification workflow:

bash
Copy code
php artisan khuloodbatis:make-notification UserActivated
This command will generate:

swift
Copy code
app/Notifications/UserActivated.php
app/Events/UserActivatedEvent.php
app/Listeners/UserActivatedListener.php
resources/views/mail/user-activated.blade.php
lang/en/notifications.php
lang/ar/notifications.php
📝 Output Structure
1. Notification
Located at:

swift
Copy code
app/Notifications/{Name}.php
2. Event
swift
Copy code
app/Events/{Name}Event.php
3. Listener
swift
Copy code
app/Listeners/{Name}Listener.php
4. Markdown Template
swift
Copy code
resources/views/mail/{kebab-case-name}.blade.php
5. Localization
bash
Copy code
resources/lang/en/notifications.php
resources/lang/ar/notifications.php
🧪 Testing
You can run PHPUnit tests:

bash
Copy code
php artisan test
or:

bash
Copy code
vendor/bin/phpunit
(If your package includes tests)

🧱 Package Structure
css
Copy code
notification-generator/
├── src/
│   ├── Commands/
│   │   └── MakeFullNotificationCommand.php
│   ├── NotificationGeneratorServiceProvider.php
│   └── ...
├── composer.json
├── README.md
└── ...
🧬 Requirements
PHP 8.1+

Laravel 10 or Laravel 11

🔧 Configuration
If needed, you may override:

default stubs

markdown paths

language structure

By publishing the configuration:

bash
Copy code
php artisan vendor:publish --tag=notification-generator

🤝 Contributing

Contributions, issues, and feature requests are welcome!
Feel free to open a PR or Issue.

🪪 License

This package is open-source software licensed under the MIT License.

🧷 Author

Khulood Batis
Laravel Backend Developer
GitHub: https://github.com/khuloodbatis

📚 Reliable Sources Used

Laravel Package Development
https://laravel.com/docs/11.x/packages

Composer Package Publishing (Packagist)
https://getcomposer.org/doc/04-schema.md

Laravel Notifications (Markdown)
https://laravel.com/docs/11.x/notifications