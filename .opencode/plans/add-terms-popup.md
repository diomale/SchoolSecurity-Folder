# Add Privacy Policy & Terms of Service Popup

## Files to Modify (in order)

### 1. Migration
**File:** `ccsecurity-app/database/migrations/2026_07_19_180335_add_terms_accepted_at_to_users_tables.php`
- Set `protected $connection = 'mysql_second';`
- In `up()`: add `terms_accepted_at` nullable timestamp to `outside_user` (after `email_verified_at`) and `inside_user`
- In `down()`: drop column from both

### 2. Models
**File:** `ccsecurity-app/app/Models/outsideuser.php`
- Add `'terms_accepted_at'` to `$fillable`

**File:** `ccsecurity-app/app/Models/InsideUser.php`
- Add `'terms_accepted_at'` to `$fillable`

### 3. Outside User Signup - Form
**File:** `ccsecurity-app/resources/views/OutsideUser/signup.blade.php`
- Before submit button, add:
```html
<div class="signup-form-group terms-group">
    <label class="checkbox-label">
        <input type="checkbox" name="agree_terms" value="1" required>
        <span>I agree to the <a href="#" onclick="event.preventDefault(); openModal('privacy')">Privacy Policy</a> and <a href="#" onclick="event.preventDefault(); openModal('terms')">Terms of Service</a></span>
    </label>
</div>
```
- Add two modal popups at bottom (before `</body>`)
- JavaScript functions: `openModal(name)`, `closeModal(name)`

### 4. Outside User Signup - CSS
**File:** `ccsecurity-app/resources/css/OutsideUser/outsideuser_style_signup.css`
- Add styles for modal overlay, modal content, checkbox

### 5. OutsideUserController
**File:** `ccsecurity-app/app/Http/Controllers/OutsideUserController.php`
- In `SignupRequest()` validation: add `'agree_terms' => 'accepted'`
- In `SignupRequest()` create: add `'terms_accepted_at' => now()`

### 6. Route
**File:** `ccsecurity-app/routes/web.php`
- Add: `Route::post('/insideuser/accept-terms', [InsideUserController::class, 'acceptTerms'])->name('insideuser.accept.terms');`

### 7. InsideUserController
**File:** `ccsecurity-app/app/Http/Controllers/InsideUserController.php`
- In `dashboard()`: check `terms_accepted_at`, pass `$showTermsModal`
- Add `acceptTerms()` method

### 8. Inside User Dashboard - Terms Modal
**File:** `ccsecurity-app/resources/views/InsideUser/dashboard.blade.php`
- Full-screen mandatory modal overlay if `$showTermsModal` is true
- No close button, must accept to proceed

### 9. Run migration
```
php artisan migrate --force
```
