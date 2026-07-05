# Laravel Concepts Guide

## What is Laravel?
Laravel is a free, open-source PHP web application framework with expressive, elegant syntax. It follows the Model-View-Controller (MVC) architectural pattern and aims to make the development process simpler and more enjoyable.

## Key Concepts

### Routes
Routes define the URLs of your application and map them to specific controllers or closures. They determine how your application responds to different HTTP requests at specific endpoints.

Example:
```php
Route::get('/users', [UserController::class, 'index']);
```

### MVC Architecture
- **Model**: Represents the data layer and business logic. Models interact with the database and handle data validation.
- **View**: The presentation layer that displays information to users (usually HTML templates).
- **Controller**: Acts as an intermediary between Models and Views, handling HTTP requests and returning responses.

### Controllers
Controllers group related request handling logic into classes. They receive input from users, validate it, and interact with models to process data.

### Middleware
Middleware acts as a filter for HTTP requests entering your application. Common uses include authentication, CSRF protection, and request modification.

### Eloquent ORM
Laravel's object-relational mapper that provides an elegant and simple ActiveRecord implementation for working with databases.

### Blade Templates
Laravel's templating engine that allows you to use plain PHP code in your views with additional convenient features like template inheritance and sections.

### Migrations
Database schema version control that allows you to modify and share your application's database schema easily.

### Artisan CLI
Laravel's command-line interface for interacting with the framework, generating code, managing migrations, and more.

### Authentication Guards
Laravel supports multiple authentication guards, allowing different user types to authenticate against different user providers and databases. This project uses 5 guards: `superadmin`, `admin`, `securityguard`, `insideuser`, `outsideuser`.

## Common Laravel Tasks for AI Assistant
When helping with Laravel projects, focus on:

1. Creating routes in `routes/web.php` or `routes/api.php`
2. Generating controllers using `php artisan make:controller`
3. Setting up models with `php artisan make:model`
4. Creating migrations with `php artisan make:migration`
5. Using Eloquent relationships between models
6. Implementing middleware for authentication and authorization
7. Building RESTful APIs
8. Working with Blade templates for front-end views
9. Handling form requests and validation
10. Managing dependencies with Composer
11. Configuring environment variables in `.env` files
12. Seeding databases with test data using seeders

## Best Practices
- Follow PSR standards for PHP code
- Use meaningful names for routes, controllers, and models
- Separate concerns using the MVC pattern
- Use Laravel's built-in authentication and authorization features
- Leverage Laravel's helper functions and facades
- Write tests using PHPUnit
- Use Vite for asset compilation (replaces Laravel Mix)
- Implement proper error handling and logging

## This Project's Architecture

### Dual Database Setup
This project uses two separate MySQL/MariaDB databases:

| Database | Connection | Purpose |
|----------|------------|---------|
| `ccsecurity_db` | `mysql` (default) | Primary — `super_admins` table |
| `securitysystemdatabase` | `mysql_second` | Secondary — all other tables (admins, users, logs, etc.) |

Models specify their connection via `protected $connection = 'mysql_second'`. The primary database uses the default connection.

### Docker Setup
The app runs in 3 Docker containers:

| Container | Port | Purpose |
|-----------|------|---------|
| `laravel.test` | 8080 | PHP 8.5 app server with Supervisor |
| `mariadb` | 3307 | MariaDB 11 database |
| `caddy` | 443 | HTTPS reverse proxy with self-signed TLS |

### User Roles and Guards

| Role | Guard | Model | Table | Database |
|------|-------|-------|-------|----------|
| Super Admin | `superadmin` | `SuperAdmin` | `super_admins` | `ccsecurity_db` |
| Admin | `admin` | `admin` | `admins` | `securitysystemdatabase` |
| Security Guard | `securityguard` | `securityguard` | `security_guard_user` | `securitysystemdatabase` |
| Inside User | `insideuser` | `InsideUser` | `inside_user` | `securitysystemdatabase` |
| Outside User | `outsideuser` | `OutsideUser` | `outside_user` | `securitysystemdatabase` |

### Key Files in This Project
- `compose.yaml` — Docker Compose configuration
- `docker/Caddyfile` — Caddy HTTPS reverse proxy config
- `docker/supervisord.conf` — Supervisor config (auto-starts PHP server)
- `docker/create-second-db.sh` — Creates secondary database on first run
- `app/Models/` — Eloquent models with dual-database support
- `app/Http/Controllers/` — Controllers for each user role
- `routes/web.php` — All route definitions grouped by role
- `resources/css/` — Separate CSS files for each portal
- `public/build/` — Compiled production assets (Vite)