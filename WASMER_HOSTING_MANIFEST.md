# Wasmer Hosting: Complete Package Manifest

This document contains all the configuration and file mappings required to host your application on Wasmer. You can use these files directly to set up your hosting environment.

## 1. Wasmer Configuration (`wasmer.toml`)
This is the core manifest that tells Wasmer how to run your Laravel application.

```toml
[package]
name = "aditya29-22/edu-id-saas"
version = "0.1.0"
description = "EDU-ID SaaS Laravel Application"
license = "MIT"

[[module]]
name = "php"
source = "php/php:8.2"

[[command]]
name = "serve"
module = "php"
runner = "https://github.com/wasmerio/php-cgi-runner"

[fs]
"/public" = "public"
"/public/build" = "public/build"
"/storage" = "storage"
"/config" = "config"
"/bootstrap" = "bootstrap"
"/vendor" = "vendor"
"/app" = "app"
"/database" = "database"
"/resources" = "resources"
"/routes" = "routes"

[env]
APP_NAME = "EDU-ID-SaaS"
APP_ENV = "production"
APP_KEY = "base64:/ez2MUfcLr9S/RWj+i5qFxMFsM8a+WhpHe83faQOars="
APP_DEBUG = "false"
APP_URL = "https://your-app.wasmer.app"
DB_CONNECTION = "mysql"
DB_HOST = "YOUR_EXTERNAL_DB_HOST"
DB_PORT = "3306"
DB_DATABASE = "your_db_name"
DB_USERNAME = "your_username"
DB_PASSWORD = "your_password"
```

## 2. Exclusion Rules (`.wasmerignore`)
These files are ignored during the upload to keep the package lightweight and secure.

```text
node_modules
.env
.git
storage/*.key
tests
phpunit.xml
.editorconfig
.gitignore
package-lock.json
package.json
vite.config.js
tailwind.config.js
postcss.config.js
```

## 3. Included Directories
The following directories from your project are bundled for hosting:
- `app/` (Core logic)
- `bootstrap/` (Initialization)
- `config/` (Settings)
- `database/` (Migrations & Seeders)
- `public/` (Entry point & Assets)
- `resources/` (Views & Raw Assets)
- `routes/` (Endpoints)
- `storage/` (Logs & Cache - note: ephemeral on Edge)
- `vendor/` (PHP Dependencies)

## 4. How to Host Directly
1.  Ensure you have `wasmer.toml` and `.wasmerignore` in your project root.
2.  Open your terminal in the project root.
3.  Run `wasmer deploy`.
4.  The CLI will automatically read the files listed in this manifest and upload them to Wasmer Edge.

---
*Note: Ensure your `vendor/` directory is fully populated (`composer install --no-dev`) before deploying for production.*
