# CCSS - Columban College Security System Summary

## Overview
The Columban College Security System (CCSS) is a robust Laravel-based application designed to manage campus access, visitor control, security personnel scheduling, and school events. It utilizes a multi-guard authentication system to serve various stakeholders including students, staff, parents, security guards, and administrators.

## System Architecture & User Roles
The application implements a role-based access control system with dedicated portals:
- **Super Admin**: Oversees the system and manages Admin accounts.
- **Admin**: The primary manager for campus operations, including security guard oversight, user approvals, and data retention policies.
- **Security Guard**: Handles real-time QR scanning at checkpoints, manages their own shifts, and issues temporary access passes.
- **Inside User (Student/Staff)**: Authorized campus members who use personal QR codes for entry and can organize campus events.
- **Outside User (Visitor/Parent)**: External individuals who register for access, request visits, or connect with students to monitor their school attendance.

---

## 1. Core Models (`app/Models`)
The data layer is structured to handle high-volume logging and complex relationships:

- **Identity Models**:
    - `InsideUser`: Represents Students and Staff. Features automated QR value generation.
    - `OutsideUser`: Represents Visitors and Parents. Tracks approval status and QR expiry.
    - `securityguard`: Personnel responsible for checkpoint operations.
    - `Admin` & `SuperAdmin`: System management identities.
- **Access & Logging**:
    - `EntryLog`: The central repository for all QR scan activities (entry, exit, and status changes).
    - `QuickPass`: Temporary, same-day QR codes for deliveries or short-term guests.
    - `VisitRequest`: Workflow for visitors to request specific appointment-based access.
- **Relationships & Operations**:
    - `ParentChildConnection`: Manages the link between parents and students, requiring student approval.
    - `Shift` & `ShiftLog`: Tracks guard scheduling, clock-in/out times, and shift handovers.
- **Events Engine**:
    - `Event`: Campus events created by inside users, requiring admin approval.
    - `EventRegistration`: Tracks participants, their unique event QR codes, and check-in status.
- **System Maintenance**:
    - `CleanupSetting` & `CleanupTableSetting`: Configurations for automated purging of old logs and notifications.

---

## 2. Core Controllers (`app/Http/Controllers`)
The application logic is modularized into specialized controllers:

- **`AdminController`**: 
    - **User Management**: Full CRUD for Security Guards and Inside Users (Students/Staff).
    - **Visitor Operations**: Manages the approval/rejection workflow for new visitor accounts and walk-in registrations.
    - **Operational Oversight**: Assigns shifts to guards, manages visit requests, and oversees parent-child connection statuses.
    - **System Maintenance**: Provides an interface for configuring and manually triggering data cleanup tasks.
- **`AdminEventController`**: 
    - **Event Approval**: Dedicated workflow for Admins to review, approve, or reject event requests submitted by Inside Users.
    - **Lifecycle Management**: Tools to mark events as completed, cancelled, or to perform bulk status updates.
    - **Analytics**: Provides system-wide statistics on event participation and creator activity.
- **`SecurityGuardController`**: 
    - **Checkpoint Operations**: The core engine for QR scanning, identifying user types (Inside, Outside, Quick Pass, or Event), and logging entry/exit.
    - **Active Monitoring**: Real-time view of entry/exit logs and a list of personnel currently on school premises.
    - **Shift Management**: Functionality for guards to clock in/out and leave handover notes for the next shift.
    - **Quick Pass**: Issues temporary same-day QR passes for short-term visitors.
- **`InsideUserEventController`**: 
    - **Creator Workflow**: Allows students/staff to create events, manage their own event dashboard, and track registrations.
    - **Public Interface**: Manages the public-facing registration pages and registration submission logic.
    - **Participant Management**: Tools for exporting registration data to CSV and generating/resending QR codes.
- **`EventCreatorApprovalController`**: 
    - **Registration Review**: Enables event creators to personally approve or reject participants who registered through the public link.
    - **QR Dispatch**: Triggers automated emails containing unique QR codes once a participant is approved.
- **`OutsideUserController`**: 
    - **Visitor Portal**: Handles signup, login, and a dashboard for visitors to view their visit history and active QR status.
    - **Engagement**: Allows visitors to submit visit requests and manage their personal profiles and notifications.
- **`ParentConnectionController`**: 
    - **Request Logic**: Allows parents to search for students and submit connection requests to monitor their arrival/departure.
- **`InsideUserConnectionController`**: 
    - **Handshake Logic**: Allows students to review, accept, or reject connection requests from parents (Acceptance triggers automatic approval).
- **`SuperAdminAuthController`**: 
    - **Admin Management**: Dedicated portal for Super Admins to perform CRUD operations on standard Admin accounts.
- **`InsideUserController`**: 
    - **Authorized Portal**: Manages the dashboard for students/staff, displaying their personal entry/exit history and active connections.

---

## 3. View Structure (`resources/views`)
Views are strictly organized by user role to ensure a tailored user experience:

- **`Admin/`**: Comprehensive tables for user management, request queues, and system settings.
- **`SecurityGuardUser/`**: Mobile-friendly interfaces for the QR scanner, shift clocking, and log viewing.
- **`InsideUser/`**: Dashboards for students/staff to view their entry history and manage their organized events.
- **`OutsideUser/`**: Simple portals for registration, visit requests, and child activity tracking.
- **`Superadmin/`**: Minimalist interface for administrative account management.
- **`emails/`**: Professional templates for event approvals and QR code delivery.
- **`welcome.blade.php`**: The public landing page featuring a unified login gateway and a dynamic listing of approved public school events.

---

## 4. Key System Features
- **Smart QR Scanning**: Logic that automatically alternates between "Entry" and "Exit" based on the user's last scan.
- **Automated Data Lifecycle**: Console commands (`app/Console/Commands`) that run daily to deactivate expired QRs and purge old data according to Admin-defined retention periods.
- **Multi-Database Support**: Uses a secondary connection (`mysql_second`) for primary business data, isolating it from standard Laravel tables.
- **Public Event Integration**: Allows the school to host events where external participants can register and receive instant QR access via email.
