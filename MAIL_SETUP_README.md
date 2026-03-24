# Mail Configuration & Notifications Setup Guide

## Overview

The School Security Management System uses Laravel's Mail and Notification systems to send automated emails for:

- **Event Registration QR Codes** - Sent to attendees upon approval
- **Connection Request Notifications** - Notify inside users of parent connection requests
- **Visit Request Updates** - Notify outside users of approval/rejection
- **Event Approval Notifications** - Notify event creators of admin decisions

---

## Table of Contents

1. [Mail Configuration Options](#mail-configuration-options)
2. [Setting Up Mailtrap (Recommended for Development)](#setting-up-mailtrap-recommended-for-development)
3. [Setting Up Gmail SMTP](#setting-up-gmail-smtp)
4. [Setting Up Other Mail Providers](#setting-up-other-mail-providers)
5. [Testing Email Functionality](#testing-email-functionality)
6. [Troubleshooting](#troubleshooting)
7. [Available Mail Classes](#available-mail-classes)

---

## Mail Configuration Options

The application supports multiple mail drivers:

| Driver | Use Case | Configuration |
|--------|----------|---------------|
| **Mailtrap** | Development & Testing | SMTP with sandbox |
| **Gmail SMTP** | Production (small scale) | App Password required |
| **SMTP** | Production (custom) | Any SMTP server |
| **Log** | Debugging | Emails written to log |
| **Array** | Testing | Emails discarded |

---

## Setting Up Mailtrap (Recommended for Development)

Mailtrap is a free email testing service that captures emails in a sandbox environment.

### Step 1: Create a Mailtrap Account

1. Visit [https://mailtrap.io](https://mailtrap.io)
2. Sign up for a free account
3. Create an **Inbox** (default inbox is created automatically)

### Step 2: Get SMTP Credentials

1. Go to **Email Testing** → **Inboxes**
2. Click on your inbox
3. Copy the **SMTP Credentials**:
   - Host: `smtp.mailtrap.io` or `sandbox.smtp.mailtrap.io`
   - Port: `2525` or `587` or `465`
   - Username: Your Mailtrap SMTP username
   - Password: Your Mailtrap SMTP password

### Step 3: Update `.env` File

Open your `.env` file in the `ccsecurity-app` directory and update:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@schoolsecurity.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Replace:**
- `your-mailtrap-username` with your actual Mailtrap username
- `your-mailtrap-password` with your actual Mailtrap password

### Step 4: Clear Configuration Cache

Run the following commands to apply the new settings:

```bash
cd ccsecurity-app

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Optional: Cache configuration for production
php artisan config:cache
```

### Step 5: Test Email Configuration

1. **Check Mailtrap Dashboard:**
   - Visit your Mailtrap inbox at [https://mailtrap.io](https://mailtrap.io)
   - Keep this tab open to see incoming test emails

2. **Send a Test Email:**

   Create a test route in `routes/web.php` (temporary):

   ```php
   use Illuminate\Support\Facades\Mail;
   use Illuminate\Support\Facades\Route;

   Route::get('/test-email', function () {
       Mail::raw('This is a test email from School Security System!', function ($message) {
           $message->to('test@example.com')
                   ->subject('Test Email');
       });

       return 'Test email sent! Check your Mailtrap inbox.';
   });
   ```

3. **Visit the URL:**
   - Go to `http://localhost:8000/test-email`
   - Check your Mailtrap inbox for the test email

4. **Remove Test Route:**
   - Delete the test route from `routes/web.php` after testing

---

## Setting Up Gmail SMTP

### Step 1: Enable 2-Factor Authentication

1. Go to your Google Account settings
2. Enable **2-Step Verification** (2FA)

### Step 2: Generate App Password

1. Visit [https://myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)
2. Select **Mail** and your device
3. Click **Generate**
4. Copy the 16-character app password (e.g., `abcd efgh ijkl mnop`)

### Step 3: Update `.env` File

```env
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password-here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@schoolsecurity.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Important:**
- Remove spaces from the app password: `abcdefghijklmnop`
- Use your full Gmail address as username

### Step 4: Clear Cache

```bash
php artisan config:clear
php artisan config:cache
```

---

## Setting Up Other Mail Providers

### Outlook/Hotmail

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=your-email@outlook.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@outlook.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Custom SMTP Server

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=465
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Using Mailgun (API-based)

1. Install Mailgun package:
   ```bash
   composer require symfony/mailgun-mailer symfony/http-client
   ```

2. Update `.env`:
   ```env
   MAIL_MAILER=mailgun
   MAILGUN_DOMAIN=your-domain.mailgun.org
   MAILGUN_SECRET=key-your-mailgun-api-key
   MAILGUN_ENDPOINT=api.mailgun.net
   MAIL_FROM_ADDRESS="noreply@yourdomain.com"
   MAIL_FROM_NAME="${APP_NAME}"
   ```

---

## Testing Email Functionality

### Test Email Configuration

Run this Artisan command to test mail configuration:

```bash
php artisan tinker
```

Then execute:

```php
use Illuminate\Support\Facades\Mail;

Mail::raw('Test email body', function ($message) {
    $message->to('recipient@example.com')->subject('Test Subject');
});
```

### Test Specific Features

#### 1. Event Registration QR Email

- Create an event as an inside user
- Register for the event (or have someone register)
- Approve the registration
- Check if QR code email is sent

#### 2. Connection Request Notification

- Have an outside user request a parent-child connection
- Check if the inside user receives a notification

#### 3. Visit Request Notification

- Have an outside user submit a visit request
- Admin approves/rejects the request
- Check if notification is created

### View Sent Emails in Logs

If using log driver for debugging:

```bash
# View latest logs
tail -f storage/logs/laravel.log

# Or search for mail-related logs
grep -i "mail" storage/logs/laravel.log
```

---

## Troubleshooting

### Issue: Emails Not Sending

**Check Configuration:**

```bash
# Verify environment variables
php artisan env:display | grep MAIL

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear
```

**Test Connection:**

```bash
php artisan tinker
```

```php
use Illuminate\Support\Facades\Mail;

try {
    Mail::raw('Test', function ($message) {
        $message->to('test@example.com')->subject('Test');
    });
    echo "Email sent successfully!";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Issue: Gmail Authentication Failed

**Solutions:**

1. **Use App Password, not regular password**
   - Regular Gmail passwords don't work
   - Generate an App Password from Google Account settings

2. **Enable Less Secure Apps (not recommended)**
   - Only for testing with old Gmail accounts
   - Visit: https://myaccount.google.com/lesssecureapps

3. **Check 2FA is enabled**
   - App passwords require 2-Step Verification

4. **Verify credentials in `.env`:**
   ```env
   MAIL_USERNAME=youremail@gmail.com
   MAIL_PASSWORD=abcdefghijklmnop  # 16 chars, no spaces
   ```

### Issue: Mailtrap Not Receiving Emails

**Check:**

1. **SMTP credentials are correct**
   - Copy directly from Mailtrap dashboard
   - No extra spaces or characters

2. **Port is not blocked by firewall**
   - Try different ports: `2525`, `587`, `465`

3. **Inbox is not full**
   - Free plan has limits (500 messages, 10/day)
   - Delete old messages

### Issue: Emails Going to Spam

**Solutions:**

1. **Use a verified domain**
   - Don't use `@gmail.com` for production
   - Use your school's domain

2. **Set up SPF, DKIM, DMARC records**
   - Add DNS records for your domain

3. **Use proper From address**
   ```env
   MAIL_FROM_ADDRESS="noreply@yourschooldomain.com"
   ```

4. **Avoid spam trigger words**
   - Review email content

### Issue: Queue Not Processing Emails

If emails are queued but not sent:

1. **Start queue worker:**
   ```bash
   php artisan queue:work
   ```

2. **Check queue connection:**
   ```bash
   php artisan queue:monitor database
   ```

3. **Process failed jobs:**
   ```bash
   php artisan queue:retry all
   ```

4. **Run queue listener (development):**
   ```bash
   php artisan queue:listen --tries=3
   ```

---

## Available Mail Classes

The application includes the following pre-built email templates:

### 1. `EventRegistrationQrMail`

**Purpose:** Sends QR code to event attendees

**Location:** `app/Mail/EventRegistrationQrMail.php`

**Usage:**
```php
use App\Mail\EventRegistrationQrMail;
use Illuminate\Support\Facades\Mail;

Mail::to($registration->email)->send(new EventRegistrationQrMail($registration));
```

**Variables:**
- `$registration` - EventRegistration model instance
- Contains QR code attachment
- Event details and instructions

---

### 2. `EventCreatorApprovalMail`

**Purpose:** Notifies event creators when registrations are approved

**Location:** `app/Mail/EventCreatorApprovalMail.php`

**Usage:**
```php
use App\Mail\EventCreatorApprovalMail;
use Illuminate\Support\Facades\Mail;

Mail::to($registration->email)->send(new EventCreatorApprovalMail($registration));
```

**Variables:**
- `$registration` - EventRegistration model instance
- Event name, date, time
- Approval confirmation

---

### 3. `EventApprovedMail`

**Purpose:** Notifies when an event is approved by admin

**Location:** `app/Mail/EventApprovedMail.php`

**Usage:**
```php
use App\Mail\EventApprovedMail;
use Illuminate\Support\Facades\Mail;

Mail::to($event->creator->email)->send(new EventApprovedMail($event));
```

**Variables:**
- `$event` - Event model instance
- Event details
- Approval status

---

## Database Notifications

The system also uses **database notifications** stored in the `notifications` table.

### Notification Model

**Location:** `app/Models/Notification.php`

**Connection:** `mysql_second`

**Fields:**
- `outside_user_id` - Recipient
- `type` - Notification type
- `title` - Notification title
- `message` - Notification body
- `is_read` - Read status
- `related_type` - Related model type
- `related_id` - Related model ID

### Creating Notifications

```php
use App\Models\Notification;

Notification::create([
    'outside_user_id' => $userId,
    'type' => 'visit_request_approved',
    'title' => 'Visit Request Approved',
    'message' => 'Your visit request has been approved by the admin.',
    'is_read' => false,
    'related_type' => 'visit_request',
    'related_id' => $visitRequestId,
]);
```

### Retrieving Notifications

```php
// Get unread notifications
$unreadNotifications = Notification::where('outside_user_id', $userId)
    ->unread()
    ->get();

// Get all notifications
$notifications = Notification::where('outside_user_id', $userId)
    ->orderBy('created_at', 'desc')
    ->get();

// Mark as read
$notification->markAsRead();
```

---

## Production Deployment Checklist

Before deploying to production:

- [ ] Update `.env` with production mail settings
- [ ] Use a professional email domain (not Gmail/Mailtrap)
- [ ] Set up SPF/DKIM/DMARC DNS records
- [ ] Test email sending in production environment
- [ ] Configure queue worker for email processing
- [ ] Set up email logging/monitoring
- [ ] Configure rate limiting (avoid being flagged as spam)
- [ ] Test all email templates with real addresses

### Production Environment Variables

```env
# Production Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourschooldomain.com
MAIL_PASSWORD=your-secure-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourschooldomain.com"
MAIL_FROM_NAME="School Security System"

# Queue Configuration (recommended for production)
QUEUE_CONNECTION=database
```

### Running Queue Worker in Production

**Option 1: Supervisor (Linux)**

Create `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/ccsecurity-app/artisan queue:work database --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/ccsecurity-app/storage/logs/worker.log
```

**Option 2: Windows Task Scheduler**

```
Program: php.exe
Arguments: C:\path\to\ccsecurity-app\artisan queue:work database --sleep=3 --tries=3
Start in: C:\path\to\ccsecurity-app
```

---

## Additional Resources

- [Laravel Mail Documentation](https://laravel.com/docs/mail)
- [Laravel Notifications Documentation](https://laravel.com/docs/notifications)
- [Mailtrap Documentation](https://mailtrap.io/email-sending/)
- [Gmail SMTP Settings](https://support.google.com/mail/answer/7126229)
- [Laravel Queue Documentation](https://laravel.com/docs/queues)

---

## Support

For issues or questions:

1. Check the troubleshooting section above
2. Review Laravel documentation
3. Check application logs: `storage/logs/laravel.log`
4. Contact BitStack Studio development team

---

**Last Updated:** March 2026  
**Laravel Version:** 12.x  
**Compatible with:** PHP 8.2+
