#  Campus Security Monitoring System

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Node.js](https://img.shields.io/badge/Node.js-339933?style=for-the-badge&logo=nodedotjs&logoColor=white)](https://nodejs.org)

A robust, Laravel-based security monitoring system designed for comprehensive campus management. This system features multi-role authentication, QR-based access control, and a dual-database architecture for enhanced security and performance.

---

##  User Roles & Access Levels

*   ** Super Admin**: Full system configuration and top-level oversight.
*   ** Admin**: Departmental management and user administration.
*   ** Inside Users**: Students, faculty, or staff members.
*   ** Outside Users**: Visitors and external guests.
*   ** Security Guards**: Front-line operators for scanning and log management.

---

##  Key Features

- **Multi-Auth System**: Independent dashboards and logic for all 5 user roles.
- **QR Code Management**: Dynamic QR generation for visitors and staff with automated deactivation.
- **Visitor Tracking**: Visit request workflows, connection history, and real-time notifications.
- **Security Guard Tools**: Integrated QR scanner interface, entry/exit logging, and shift management.
- **Automated Maintenance**: Scheduled cleanup for old notifications, shift logs, and expired QR codes.
- **Dual Database Support**: Configured to handle primary application data and secondary security logs separately.
- **reCAPTCHA Integration**: Enhanced bot protection on all authentication forms.

---

##  Getting Started

### Prerequisites

- PHP >= 8.1
- Composer
- MySQL
- Node.js & NPM
- Laravel 10+

### Installation Guide

1.  **Clone the Repository**
    ```bash
    git clone https://your-repository-link.git
    cd SchoolSecurity-Folder/ccsecurity-app
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    npm install
    ```

3.  **Environment Setup**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Configure Databases**
    Update your `.env` with your MySQL credentials. Note the secondary connection for security logs:
    ```env
    # Primary Database
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=ccsecurity_db
    DB_USERNAME=root
    DB_PASSWORD=

    # Secondary Security Database
    DB_SECOND_CONNECTION=mysql
    DB_SECOND_HOST=127.0.0.1
    DB_SECOND_PORT=3306
    DB_SECOND_DATABASE=SecuritySystemDatabase
    DB_SECOND_USERNAME=root
    DB_SECOND_PASSWORD=
    ```

5.  **Run Migrations & Seeders**
    ```bash
    php artisan migrate
    # If seeders are available:
    # php artisan db:seed
    ```

6.  **Build Assets & Launch**
    ```bash
    npm run dev
    # In a new terminal:
    php artisan serve
    ```

---

##  Development & Maintenance

| Action | Command |
| :--- | :--- |
| **Clear All Caches** | `php artisan optimize:clear` |
| **Run Scheduled Tasks** | `php artisan schedule:run` |
| **Rebuild JS/CSS** | `npm run build` |
| **Run Cleanup Manually**| `php artisan app:run-all-cleanup` |

---

##  Project Structure Highlights

- `app/Http/Controllers/`: Role-specific logic (Admin, Security, etc.)
- `app/Models/`: Core entities like `QuickPass`, `EntryLog`, and `Shift`.
- `app/Console/Commands/`: Automated cleanup routines.
- `resources/views/`: Blade templates organized by user role.
- `resources/css/`: Modular stylesheets for each dashboard type.

---

##  Security Best Practices

- [ ] **Production Env**: Set `APP_DEBUG=false` and `APP_ENV=production`.
- [ ] **API Protection**: Ensure `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` are configured.
- [ ] **Secrets**: Never commit your `.env` file to version control.
- [ ] **Caching**: Run `php artisan config:cache` in production for speed and security.

---

##  Development Team

**BitStack Studio**
- **Diomale Romero**
- **Rushield Tan**
- **Charlize Agsaoay**

&copy; 2026 BitStack Studio. All rights reserved.
