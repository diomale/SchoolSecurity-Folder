import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Base & App Level
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/welcome.css',

                // Super Admin Portal
                'resources/css/SuperadminStyleFolder/superadmin_style_login.css',
                'resources/css/SuperadminStyleFolder/superadmin_style_dashboard.css',
                'resources/css/SuperadminStyleFolder/superadmin_style_add_form.css',
                'resources/css/SuperadminStyleFolder/superadmin_style_details.css',
                'resources/css/SuperadminStyleFolder/superadmin_style_edit.css',

                // Security Guard Portal
                'resources/css/SecurityGuardStyleFolder/securityguard_style_login.css',
                'resources/css/SecurityGuardStyleFolder/securityguard_style_dashboard.css',
                'resources/css/SecurityGuardStyleFolder/securityguard_style_entrylogs.css',
                'resources/css/SecurityGuardStyleFolder/securityguard_style_qr_status_management.css',
                'resources/css/SecurityGuardStyleFolder/securityguard_style_scanner.css',
                'resources/css/SecurityGuardStyleFolder/securityguard_style_shift_history.css',
                'resources/css/SecurityGuardStyleFolder/securityguard_style_shift_management.css',
                'resources/css/SecurityGuardStyleFolder/securityguard_style_shift_schedule.css',

                // Visitor / Outside User Portal
                'resources/css/OutsideUSerStyleFolder/outsideuser_style_dashboard.css',
                'resources/css/OutsideUSerStyleFolder/outsideuser_style_login.css',
                'resources/css/OutsideUSerStyleFolder/outsideuser_style_notifications.css',
                'resources/css/OutsideUSerStyleFolder/outsideuser_style_signup.css',
                'resources/css/OutsideUSerStyleFolder/outsideruser_style_profile.css',
                'resources/css/OutsideUSerStyleFolder/outsideruser_style_visit_history.css',
                'resources/css/OutsideUSerStyleFolder/outsideruser_style_visit_request..css',

                // Inside User Portal
                'resources/css/InsideUserStyleFolder/insideuser_style_dashboard.css',
                'resources/css/InsideUserStyleFolder/insideuser_style_login.css',
                'resources/css/InsideUserStyleFolder/insideuser_style_user_profile.css',
                'resources/css/InsideUserStyleFolder/insideuser_style_connections.css',
                'resources/css/InsideUserStyleFolder/insideuser_style_events.css',

                // Admin Portal
                'resources/css/AdminStyleFolder/admin_style_dashboard.css',
                'resources/css/AdminStyleFolder/admin_style_login.css',
                'resources/css/AdminStyleFolder/admin_style_qrstatus_management.css',
                'resources/css/AdminStyleFolder/admin_style_user_profile.css',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
