# PHP Logger

A simple, customizable logger with a web-based log viewer that supports:

- ✅ Pagination
- ✅ Filtering by log level
- ✅ Search functionality
- ✅ Sorting by timestamp
- ✅ Optional password protection
- ✅ Easy publishing of config and viewer

## 📦 Installation

```bash
composer require tijanidevit/php-logger
```

## 🔧 Publishing Assets

### Manual:

```bash
composer run-script publish-logger-assets
```

### With `--auto` (forces overwrite or for CI):

```bash
composer run-script publish-logger-assets -- --auto
```

## ⚙️ Configuration

Edit `config/logger.php`:

```php
return [
    'log_file' => __DIR__ . '/../storage/logs/app.log',
    'password' => 'password',
    'lines_per_page' => 100,
];
```

## 🔍 Log Viewer

Access via browser:

```
http://your-app.com/view-log.php?password=password
```

## ✏️ Usage Example

```php
use TijaniDevIt\Logger\Logger;

$logger = new Logger();
$logger->info("Starting process...");
$logger->warning("Something may be wrong.");
$logger->error("An error occurred.");
```

## 📜 License

MIT
