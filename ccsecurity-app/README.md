# Campus Security Monitoring System (CCSS)

Columban College Security System — a Laravel 12 multi-role security monitoring application with QR-based access control, built with PHP 8.5, MariaDB 11, and Tailwind CSS.

---

## Table of Contents

1. [Prerequisites](#prerequisites-one-time-setup)
2. [First-Time Setup](#first-time-setup)
3. [Starting the System](#starting-the-system)
4. [Accessing the System](#accessing-the-system)
5. [Making It Public (ngrok)](#making-it-public-with-ngrok)
6. [Login Credentials](#login-credentials)
7. [Shutting Down](#shutting-down)
8. [Quick Reference](#quick-reference)
9. [Troubleshooting](#troubleshooting)
10. [Architecture](#architecture)

---

## Prerequisites (One-Time Setup)

You need to install these programs on your computer before running the system.

### 1. Docker Desktop

Docker runs the app, database, and web server in isolated containers.

1. Go to https://www.docker.com/products/docker-desktop/
2. Click **Download for Windows**
3. Run the installer and follow the prompts
4. Restart your computer when asked
5. After restart, open **Docker Desktop** from the Start menu
6. Wait until the status says **"Docker Desktop is running"** (green icon in system tray)

### 2. Node.js

Node.js is needed to build the CSS and JavaScript assets (one-time step).

1. Go to https://nodejs.org
2. Download the **LTS** version (recommended)
3. Run the installer with default settings
4. To verify it installed correctly, open PowerShell and type:
   ```powershell
   node --version
   ```
   You should see a version number like `v22.x.x`

### 3. ngrok (Optional — only for public access)

ngrok creates a public URL so others can access your app over the internet. Not needed for local-only use.

1. Go to https://ngrok.com/download
2. Sign up for a free account
3. Download the Windows version
4. Extract the `ngrok.exe` file to a folder (e.g. `C:\ngrok\`)
5. Open PowerShell and run:
   ```powershell
   C:\ngrok\ngrok.exe config add-authtoken YOUR_TOKEN_HERE
   ```
   Replace `YOUR_TOKEN_HERE` with the token from your ngrok dashboard

### 4. Project Files

Make sure the project folder is at:
```
C:\Laravel-Build-Projects\SchoolSecurity-Folder\ccsecurity-app
```

Inside this folder you should see:
- `compose.yaml` — Docker configuration
- `.env` — Environment configuration
- `app/` — Laravel application code
- `docker/` — Docker helper files (Caddyfile, etc.)
- `vendor/` — PHP dependencies
- `node_modules/` — JavaScript dependencies

If `vendor/` or `node_modules/` are missing, run:
```powershell
cd C:\Laravel-Build-Projects\SchoolSecurity-Folder\ccsecurity-app
composer install
npm install
```

---

## First-Time Setup

This only needs to be done once after cloning or downloading the project.

### Step 1: Open PowerShell

1. Press `Win + X` and select **Windows Terminal** or **PowerShell**
2. Navigate to the project:
   ```powershell
   cd C:\Laravel-Build-Projects\SchoolSecurity-Folder\ccsecurity-app
   ```

### Step 2: Start Docker Containers

```powershell
docker compose up -d
```

This starts 3 containers:
- **MariaDB** — the database (port 3307)
- **Laravel** — the PHP app server (port 8080)
- **Caddy** — HTTPS web server (port 443)

Wait until all containers show as "running" or "healthy".

### Step 3: Wait for MariaDB to Be Ready

```powershell
docker compose ps
```

Wait until the MariaDB container shows **"healthy"** in the STATUS column. This takes about 10-15 seconds.

### Step 4: Build Frontend Assets

```powershell
docker compose exec laravel.test npm run build
```

This compiles all CSS and JavaScript files into `public/build/`. It takes 2-3 minutes. You'll see output like:
```
✓ 94 modules transformed.
✓ built in 2m 45s
```

**Note:** This step is only needed once, or after making code changes. The compiled assets are saved on your computer and reused on subsequent starts.

### Step 5: Verify Everything Is Running

```powershell
docker compose ps
```

You should see 3 containers running:

| Name | Status | Ports |
|------|--------|-------|
| laravel.test | Up | 0.0.0.0:8080->80 |
| mariadb | Up (healthy) | 0.0.0.0:3307->3306 |
| caddy | Up | 0.0.0.0:443->443 |

### Step 6: Open in Browser

Go to: **https://localhost**

You will see a security warning because the SSL certificate is self-signed. This is normal.
- **Chrome/Edge:** Click **Advanced** → Click **Proceed to localhost (unsafe)**
- **Firefox:** Click **Advanced** → Click **Accept the Risk and Continue**

---

## Starting the System

After the first-time setup, use these steps every time you want to run the system.

### Step 1: Make Sure Docker Desktop Is Running

Open Docker Desktop from the Start menu. Wait until it says **"Docker Desktop is running"** (green icon in the system tray).

### Step 2: Open PowerShell and Navigate to the Project

```powershell
cd C:\Laravel-Build-Projects\SchoolSecurity-Folder\ccsecurity-app
```

### Step 3: Start Everything

```powershell
docker compose up -d
```

That's it. CSS/JS assets are served from the production build automatically — no need to rebuild unless you change code.

### Step 4: Open in Browser

Go to **https://localhost** and accept the security warning.

---

## Accessing the System

### Local Access (Your Computer Only)

Open your browser and go to:
```
https://localhost
```

### Public Access (Anyone on the Internet)

See [Making It Public (ngrok)](#making-it-public-with-ngrok) below.

### Direct HTTP Access (No HTTPS)

If HTTPS doesn't work, you can also try:
```
http://localhost:8080
```

---

## Making It Public with ngrok

This lets anyone access your app from anywhere by sharing a public URL.

### Step 1: Make Sure the System Is Running Locally

Follow the [Starting the System](#starting-the-system) steps first.

### Step 2: Open a SECOND PowerShell Window

**Important:** Keep the first PowerShell window open (with Docker running). Open a new one.

Press `Win + X` and select **Windows Terminal** or **PowerShell** again.

### Step 3: Start ngrok

```powershell
ngrok http http://localhost:8080
```

If ngrok isn't in your PATH, use the full path:
```powershell
C:\ngrok\ngrok.exe http http://localhost:8080
```

### Step 4: Copy the Public URL

After ngrok starts, you'll see something like this:

```
Session Status    online
Account           your-email@gmail.com (Plan: Free)
Forwarding        https://abc123.ngrok-free.dev -> http://localhost:8080
```

Copy the URL after **Forwarding** (e.g. `https://abc123.ngrok-free.dev`).

### Step 5: Share the URL

Send this URL to anyone you want to access the system. They can open it in any browser.

**Note:** The ngrok URL changes every time you restart ngrok (free plan). You need to share the new URL each time.

### Step 6: Update Google reCAPTCHA (if needed)

If your app uses Google reCAPTCHA, you must add the ngrok domain:

1. Go to [Google reCAPTCHA Admin Console](https://www.google.com/recaptcha/admin)
2. Select your reCAPTCHA site
3. Scroll to **Domains**
4. Add the ngrok domain (e.g. `abc123.ngrok-free.dev`)
5. Save

You'll need to do this each time you restart ngrok (the domain changes).

### Step 7: Stop ngrok

When you're done, press `Ctrl + C` in the ngrok terminal window, or just close the window.

---

## Login Credentials

### Super Admin

| Field | Value |
|-------|-------|
| URL | `https://localhost/superadmin/login` |
| Email | `insane0225@gmail.com` |
| Password | `password` |

### Other Roles

Other user accounts (Admin, Security Guard, Inside User, Outside User) are created from the **Super Admin Dashboard** after logging in.

---

## Shutting Down

### Step 1: Stop Docker Containers

In the PowerShell window where Docker is running:

```powershell
docker compose down
```

Wait until you see all containers removed.

### Step 2: Stop ngrok (if running)

In the ngrok PowerShell window:

- Press `Ctrl + C`, or
- Close the terminal window

### Step 3: Stop Docker Desktop (optional)

You can close Docker Desktop if you're done for the day. Right-click the Docker icon in the system tray and select **Quit Docker Desktop**.

---

## Quick Reference

### Commands

| What You Want To Do | Command |
|---------------------|---------|
| Start the system | `docker compose up -d` |
| Stop the system | `docker compose down` |
| Rebuild CSS/JS (after code changes) | `docker compose exec laravel.test npm run build` |
| Check if containers are running | `docker compose ps` |
| View Laravel app logs | `docker compose logs laravel.test` |
| View database logs | `docker compose logs mariadb` |
| Start ngrok tunnel | `ngrok http http://localhost:8080` |
| Stop ngrok | Press `Ctrl+C` in the ngrok terminal |
| Open a shell inside the app container | `docker compose exec laravel.test bash` |
| Open a database shell | `docker compose exec mariadb mariadb -u root -ppassword` |

### URLs

| What | URL |
|------|-----|
| App (HTTPS, local) | `https://localhost` |
| App (HTTP, local) | `http://localhost:8080` |
| App (public, ngrok) | `https://your-url.ngrok-free.dev` |
| Super Admin login | `/superadmin/login` |
| Admin login | `/admin/login` |
| Security Guard login | `/securityguard/login` |
| Inside User login | `/insideuser/login` |
| Outside User login | `/outsideuser/login` |
| Google reCAPTCHA Admin | https://www.google.com/recaptcha/admin |

---

## Troubleshooting

### "Docker Desktop is not running"

Open Docker Desktop from the Start menu. Wait 10-15 seconds for it to fully start. Look for the green whale icon in the system tray.

### "Port 443 is already in use"

Another program is using port 443. Stop it or change the port in `compose.yaml`:
```yaml
caddy:
    ports:
        - '8443:443'   # Change 443 to 8443
```
Then access via `https://localhost:8443`.

### "Port 8080 is already in use"

Another program is using port 8080. Change the port in `.env`:
```
APP_PORT=8081
```
Then access via `http://localhost:8081` and update the ngrok command:
```powershell
ngrok http http://localhost:8081
```

### Browser shows "This site can't be reached"

1. Make sure Docker containers are running: `docker compose ps`
2. Make sure you're using **https://** not **http://**
3. Try **http://localhost:8080** as a fallback
4. Some browsers (Brave, Edge) force HTTPS. Try **Chrome** or an **incognito window**

### CSS/JS not loading (plain text, no design)

This usually means the `public/hot` file exists (from Vite dev server) but Vite isn't running. Fix:

```powershell
docker compose exec laravel.test bash -c "rm -f public/hot"
docker compose exec laravel.test npm run build
```

Then restart: `docker compose restart laravel.test`

### "Table doesn't exist" errors

The database may need to be reimported. Check:
```powershell
docker compose exec mariadb mariadb -u root -ppassword -e "SHOW DATABASES;"
```

If `ccsecurity_db` or `securitysystemdatabase` are missing, the SQL dumps need to be reimported.

### ngrok shows "offline" error

1. Make sure ngrok is still running in the second terminal
2. The URL changes each time you restart ngrok — copy the new URL
3. Free plan sessions expire after 2 hours — restart ngrok to get a new session

### reCAPTCHA not working via ngrok

Google reCAPTCHA only works on domains you've authorized. When using ngrok:

1. Copy the ngrok domain (e.g. `abc123.ngrok-free.dev`)
2. Go to [Google reCAPTCHA Admin Console](https://www.google.com/recaptcha/admin)
3. Add the domain under **Domains**
4. Save and refresh your app

The domain changes each time you restart ngrok, so you'll need to update it each session.

### Container won't start

Rebuild the Docker image:
```powershell
docker compose down
docker compose build --no-cache
docker compose up -d
docker compose exec laravel.test npm run build
```

---

## Architecture

### Services

| Service | Container | Port | Purpose |
|---------|-----------|------|---------|
| Caddy | caddy | 443 | HTTPS reverse proxy with auto-generated self-signed TLS certificate |
| Laravel | laravel.test | 8080 | PHP 8.5 application server (Nginx + Supervisor inside container) |
| MariaDB | mariadb | 3307 | Database server (port 3306 inside container) |
| Vite (dev only) | laravel.test | 5173 | Frontend dev server with hot-reload (only needed during development) |

### Databases

| Database | Connection | Tables |
|----------|------------|--------|
| `ccsecurity_db` | Primary (default) | `super_admins` |
| `securitysystemdatabase` | Secondary (`mysql_second`) | `admins`, `security_guard_user`, `inside_user`, `outside_user`, `entry_logs`, `events`, `event_registrations`, `shifts`, `shift_logs`, `quick_passes`, `visit_requests`, `notifications`, `cleanup_settings`, `cleanup_table_settings`, `currently_inside`, `parent_child_connections` |

### User Roles

| Role | Guard | Dashboard URL | Description |
|------|-------|---------------|-------------|
| Super Admin | `superadmin` | `/superadmin/dashboard` | Manages admins, views system |
| Admin | `admin` | `/admin/dashboard` | Manages users, shifts, events |
| Security Guard | `securityguard` | `/securityguard/dashboard` | QR scanning, entry/exit logs |
| Inside User | `insideuser` | `/insideuser/dashboard` | Students/staff, QR status |
| Outside User | `outsideuser` | `/outsideuser/dashboard` | Visitors, visit requests |

### Key Files

| File | Purpose |
|------|---------|
| `compose.yaml` | Docker Compose configuration |
| `.env` | Environment variables (DB credentials, app URL, etc.) |
| `docker/Caddyfile` | Caddy reverse proxy configuration |
| `docker/supervisord.conf` | Supervisor config for PHP server inside container |
| `docker/create-second-db.sh` | Creates the secondary database on first run |
| `app/Models/` | Eloquent models for each database table |
| `app/Http/Controllers/` | Controllers for each user role |
| `routes/web.php` | All route definitions |
| `resources/css/` | CSS stylesheets for each portal |
| `resources/js/` | JavaScript files |
| `public/build/` | Compiled production assets (CSS/JS) — auto-served |
| `public/hot` | Created by Vite dev server — overrides production assets |

---

## Development (Optional)

By default, the system runs with **production assets** (pre-built CSS/JS in `public/build/`). This works for both local and public access.

If you're actively developing and want **hot-reload** (changes appear instantly without rebuilding):

### Start Vite Dev Server (Hot-Reload)

```powershell
docker compose exec -d laravel.test bash -c "npm run dev &>/tmp/vite.log"
```

This enables hot-reload — CSS and JS changes appear instantly in the browser.

### After Making Code Changes (Production Build)

If you made code changes and want to see them without Vite, rebuild the production assets:

```powershell
docker compose exec laravel.test npm run build
```

### Stop Vite Dev Server

```powershell
docker compose exec laravel.test bash -c "fuser -k 5173/tcp"
```

### Check Vite Logs

```powershell
docker compose exec laravel.test cat /tmp/vite.log
```

**Important:** Vite dev server uses `public/hot` file which overrides production assets. If CSS stops loading, make sure you either:
- Have Vite running, OR
- Have run `npm run build` and no `public/hot` file exists
