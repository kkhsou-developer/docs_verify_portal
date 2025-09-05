# KKHSOU Document Verification Portal

## Overview

This project is a web-based Document Verification Portal for KKHSOU Study Centres. It allows authorized users to log in, view student applications, verify uploaded documents, and update their verification status. The portal streamlines the document verification process for admissions.

## Features

- Secure login with CAPTCHA protection
- Dashboard with application statistics (total, verified, pending, re-verified, suspicious)
- Paginated and searchable list of student applications and their documents
- Document preview and verification modal
- Status update with remarks for each document
- Logout functionality
- Responsive UI with Bootstrap

## Project Structure

```
.
├── assets/
│   ├── css/
│   ├── js/
│   └── kkhsou_logo.png
├── handlers/
│   ├── docVerification_handler.php
│   ├── loginHandler.php
│   └── logoutHandler.php
├── pages/
│   ├── 404_not_found.php
│   ├── dashboard.php
│   ├── documents.php
│   ├── instructions.php
│   └── component/
│       ├── _captcha.php
│       └── _header.php
├── dbConnect.php
├── index.php
└── .htaccess
```

## Workflow

1. **Login**
   - User accesses the portal and logs in with username, password, and CAPTCHA ([pages/login.php](pages/login.php)).
   - Credentials are validated via AJAX ([handlers/loginHandler.php](handlers/loginHandler.php)).
   - On success, user session is created.

2. **Dashboard**
   - After login, user is redirected to the dashboard ([pages/dashboard.php](pages/dashboard.php)).
   - Dashboard displays counts of applications by status using data from the database.

3. **Document Verification**
   - User navigates to the "Unverified Documents" or other status pages ([pages/documents.php](pages/documents.php)).
   - Applications are listed with document details, status, and actions.
   - User can preview documents and update their verification status (Verified, Re-Verified, Suspicious, Pending) with remarks.
   - All actions are handled via AJAX requests to [handlers/docVerification_handler.php](handlers/docVerification_handler.php).

4. **Logout**
   - User can log out using the logout button, which destroys the session ([handlers/logoutHandler.php](handlers/logoutHandler.php)).

5. **Routing**
   - All routes are managed via [index.php](index.php) and `.htaccess` for clean URLs.

## Database

- Connection is managed in [dbConnect.php](dbConnect.php).
- Uses PDO for secure database access.

## Security

- Session management for authentication
- CAPTCHA to prevent automated login attempts
- Prepared statements to prevent SQL injection

## Requirements

- PHP 7.x or higher
- MySQL database
- Apache server with mod_rewrite enabled

## Setup

1. Clone or copy the project files to your web server.
2. Configure database credentials in [dbConnect.php](dbConnect.php).
3. Import the required database schema and data.
4. Ensure the `UploadedFiles` directory is accessible for document previews.
5. Access the portal via your browser.

---
