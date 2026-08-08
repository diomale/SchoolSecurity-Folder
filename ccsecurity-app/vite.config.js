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
                'resources/css/Superadmin/superadmin_style_login.css',
                'resources/css/Superadmin/superadmin_style_dashboard.css',
                'resources/css/Superadmin/superadmin_style_add_form.css',
                'resources/css/Superadmin/superadmin_style_details.css',
                'resources/css/Superadmin/superadmin_style_edit.css',

                // Security Guard Portal
                'resources/css/SecurityGuardUser/securityguard_style_login.css',
                'resources/css/SecurityGuardUser/securityguard_style_dashboard.css',
                'resources/css/SecurityGuardUser/securityguard_style_entrylogs.css',
                'resources/css/SecurityGuardUser/securityguard_style_qr_status_management.css',
                'resources/css/SecurityGuardUser/securityguard_style_scanner.css',
                'resources/css/SecurityGuardUser/securityguard_style_shift_history.css',
                'resources/css/SecurityGuardUser/securityguard_style_shift_management.css',
                'resources/css/SecurityGuardUser/securityguard_style_shift_schedule.css',
                'resources/css/SecurityGuardUser/securityguard_style_walkin.css',
                'resources/css/SecurityGuardUser/securityguard_style_quickpass.css',

                // Visitor / Outside User Portal
                'resources/css/OutsideUser/outsideuser_style_dashboard.css',
                'resources/css/OutsideUser/outsideuser_style_login.css',
                'resources/css/OutsideUser/outside_user_notifications.css',
                'resources/css/OutsideUser/outsideuser_style_signup.css',
                'resources/css/OutsideUser/outsideuser_style_profile.css',
                'resources/css/OutsideUser/outsideuser_style_visit_history.css',
                'resources/css/OutsideUser/outsideuser_style_visit_request.css',
                'resources/css/OutsideUser/outside_user_connections_history.css',
                'resources/css/OutsideUser/outside_user_request_connection.css',
                'resources/css/OutsideUser/event_registration.css',
                'resources/css/OutsideUser/event_registration_pending.css',
                'resources/css/OutsideUser/event_registration_success.css',

                // Inside User Portal
                'resources/css/InsideUser/insideuser_style_dashboard.css',
                'resources/css/InsideUser/insideuser_style_login.css',
                'resources/css/InsideUser/insideuser_style_user_profile.css',
                'resources/css/InsideUser/insideuser_style_connections.css',
                'resources/css/InsideUser/insideuser_style_events.css',

                // Admin Portal
                'resources/css/Admin/admin_style_shared.css',    // Shared sidebar layout & tokens for all Admin sub-pages
                'resources/css/Admin/admin_style_crud.css',
                'resources/css/Admin/admin_style_dashboard.css',
                'resources/css/Admin/admin_style_login.css',
                'resources/css/Admin/admin_style_qrstatus_management.css',
                'resources/css/Admin/admin_style_user_profile.css',
                'resources/css/Admin/admin_style_user_details.css',
                'resources/css/Admin/admin_style_inside_user_add_form.css',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        origin: 'http://localhost:5173',
        hmr: {
            host: 'localhost',
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
