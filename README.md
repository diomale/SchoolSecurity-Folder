🛡️ Campus Security Monitoring System

A Laravel-based security monitoring system designed to manage:
 
 👤 Super Admin
 
 🧑‍💼 Admin
 
 🏫 Inside Users (Student and Staff)
 
 🌍 Outside Users
 
 🛂 Security Guards

This system supports multi-authentication and dual database configuration.

🚀 System Requirements

 Make sure you have the following installed:
 
  PHP >= 8.1
  
  Composer
  
  MySQL
  
  Node.js & NPM
  
  Laravel 10+

📦 Installation Guide

 1️⃣ Clone the Repository
 git clone https://your-repository-link.git
 cd your-project-folder
 
 2️⃣ Install Dependencies
 composer install
 npm install
 
 3️⃣ Create Environment File
 cp .env.example .env
 
 
 4️⃣ Configure .env File

Update your database configuration:

 APP_NAME=Laravel
 APP_ENV=local
 APP_DEBUG=true
 APP_URL=http://localhost
 
 DB_CONNECTION=mysql
 DB_HOST=
 DB_PORT=
 DB_DATABASE=ccsecurity-db
 DB_USERNAME=your_username
 DB_PASSWORD=your_password
 
 # Secondary Database
 DB_SECOND_CONNECTION=mysql
 DB_SECOND_HOST=
 DB_SECOND_PORT=
 DB_SECOND_DATABASE=SecuritySystemDatabase
 DB_SECOND_USERNAME=your_username
 DB_SECOND_PASSWORD=your_password

⚠️ Replace credentials with your own database credentials.

 5️⃣ Generate Application Key
 
      php artisan key:generate
 
 6️⃣ Run Migrations
 
      php artisan migrate
 
 If you are using multiple databases, make sure both databases are created in MySQL before running migrations.
 
7️⃣ Start the Server: 

    php artisan serve or composer run dev

Then visit:

    http://localhost:8000
 
🔎 reCAPTCHA Configuration

This system uses Google reCAPTCHA.

Add your keys in .env:

    RECAPTCHA_SITE_KEY=your_site_key
    RECAPTCHA_SECRET_KEY=your_secret_key

You can get keys from:

    https://www.google.com/recaptcha

🛠️ Development Commands

 Clear cache:
 
     php artisan optimize:clear
 
 Rebuild assets:
 
    npm run dev or composer run dev

 📁 Project Structure Overview
  app/
   ├── Http/Controllers/
   ├── Models/
  resources/
   ├── views/
  routes/
   ├── web.php
    🧪 Troubleshooting
   ❌ Database Connection Error
  
    Check .env
    
    Make sure MySQL is running
    
    Verify database names
  
  ❌ Login Not Working
  
     Check guard configuration in config/auth.php
     
     Verify status column values

🔒 Security Reminder

  Never commit .env file to GitHub
  
  Change default database passwords
  
  Use strong production keys
  
  Set APP_DEBUG=false in production

📌 Production Deployment Notes

Before deploying:

    APP_ENV=production
    APP_DEBUG=false

Run:

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

👨‍💻 Developed By
 1. Diomale Romero
 2. Rushield Tan
 3. Charlize Agsaoay

BitStack Studio 2026
