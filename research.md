# System Modules / Functions

Based on the analysis of the `ccsecurity-app/` codebase, the following general modules and their functions have been identified:

## 1. SuperAdmin Module
* **Functions**: Full system oversight, managing Admin accounts (Create, Read, Update, Delete), and high-level configuration.

## 2. Admin Module
* **Functions**: 
    * **User Management**: CRUD operations for Security Guards and Inside Users (Students/Staff).
    * **Access Approval**: Reviewing and approving Outside User registrations and Visit Requests.
    * **Shift Management**: Assigning and monitoring security guard shifts and history.
    * **QR & Security Control**: Managing QR code statuses (Active/Inactive) and monitoring entry logs.
    * **Event Oversight**: Approving and managing school events and attendee analytics.
    * **System Maintenance**: Configuring automated data cleanup settings for logs and notifications.

## 3. Inside User Module (Students & Staff)
* **Functions**: 
    * **Profile Management**: Updating personal information and viewing their unique QR code.
    * **Connection Management**: Accepting or rejecting connection requests from Outside Users (e.g., parents).
    * **Event Management**: Creating events (for authorized creators), managing registrations, and tracking attendance via QR.
    * **QR Access**: Using personal QR codes for school entry/exit.

## 4. Outside User Module (Parents & Guests)
* **Functions**: 
    * **Registration**: Self-signup and profile setup for school access.
    * **Visit Requests**: Submitting and tracking requests to visit the school campus.
    * **Connection Requests**: Requesting links to Inside Users (e.g., linking a parent to a student).
    * **Notifications**: Receiving real-time alerts regarding visit status and school announcements.

## 5. Security Guard Module
* **Functions**: 
    * **QR Scanning**: Using the mobile scanner to validate entry/exit for all user types.
    * **Entry/Exit Logging**: Real-time recording of every scan event with timestamps.
    * **Shift Operations**: Clock-in/out functionality and shift handover reporting.
    * **Quick Pass**: Generating temporary QR codes for immediate, short-term visitor access.
    * **Manual Override**: Manually toggling QR status for users in specific security scenarios.

## 6. Event Management System
* **Functions**: 
    * **Registration**: Public and private event registration workflows.
    * **Verification**: QR-based verification for event attendees.
    * **Analytics**: Tracking event participation and generating attendance reports.

## 7. Automated Cleanup & Maintenance
* **Functions**: 
    * **Data Retention**: Background commands for deleting old notifications, logs, and expired visit requests.
    * **QR Expiration**: Automatically deactivating expired Quick Passes and outsider QR codes to maintain security.

# System Development Methodology

This project follows the **Iterative Development Model**, a standard SDLC (System Development Life Cycle) process that allows for the continuous refinement of security features through repeated cycles (iterations). This approach is particularly suitable for a multi-layered security system where features like visitor management and automated cleanup are developed and refined incrementally.

## 1. Life Cycle of the Study (Process)

### A. Requirement Analysis
The development began by identifying the unique security needs of a school environment. This involved defining the roles of **SuperAdmins, Admins, Security Guards, Students/Staff (Inside Users),** and **Parents/Guests (Outside Users)**. Key requirements identified included real-time QR scanning, shift scheduling for guards, parent-student linking, and automated data retention policies.

### B. System Design
The system architecture was designed using the **Model-View-Controller (MVC)** pattern.
*   **Database Schema**: A dual-connection database approach (e.g., `mysql_second`) was implemented to manage security logs, shifts, and event registrations separately from core application data.
*   **Role-Based Styling**: Modular CSS structures (e.g., `SecurityGuardStyleFolder`, `AdminStyleFolder`) were designed to provide distinct user interfaces for each portal.

### C. Implementation
Development was carried out using **PHP (Laravel 12)**, **Vite**, and **Tailwind CSS**. Features were developed in iterative sprints:
1.  **Phase 1**: Core authentication and basic QR generation for students and staff.
2.  **Phase 2**: Security Guard scanner integration and entry/exit logging.
3.  **Phase 3**: Visitor management (Outside User) and administrative approval workflows.
4.  **Phase 4**: Advanced features like Shift Management, Quick Passes, and Automated Cleanup commands.

### D. Testing
Validation was integrated into every iteration:
*   **Unit Tests**: Verifying QR code expiration logic and date-time calculations for shifts.
*   **Feature Tests**: Simulating complete user journeys, such as a parent requesting a connection and a student accepting it.
*   **Security Validation**: Implementing reCAPTCHA for signups and password-protected administrative deletions.

### E. Maintenance & Optimization
The system includes built-in maintenance tools via **Laravel Console Commands**. Scheduled tasks run daily to:
*   Deactivate expired visitor QR codes.
*   Delete logs and notifications older than the set retention period.
*   Manage shift handovers and log archival.

## 2. Research Flow Illustration

The research and development process followed a logical progression:

1.  **Investigation**: Analyzing the inefficiencies of manual security logs and identifying manual entry bottlenecks.
2.  **Logic Modeling**: Mapping the relationship between visitors, students, and security guards.
3.  **Core Development**: Building the QR scanner engine and real-time activity feed.
4.  **Security Enhancement**: Adding "Admin Password" requirements for critical data modifications and implementing global cleanup toggles.
5.  **Final Verification**: Ensuring that background tasks correctly manage data lifecycle without manual intervention.
