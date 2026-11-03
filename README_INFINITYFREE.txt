FARMING WITH URUNJI MANAGEMENT SYSTEM (FWU)
==============================================

THIS IS A NEW SERVER-FIRST VERSION
-----------------------------------
This version was rebuilt from scratch around the problem experienced with the previous system:

    A record was changed on Device A,
    but Device B still showed old data,
    or the change disappeared after refresh.

The new version does NOT use browser LocalStorage as the source of truth.

Every login, record, edit and delete is handled by PHP and stored in ONE MySQL database.
Device A and Device B therefore read the same central database.

Architecture
------------
Browser/device
      |
      v
PHP application + server session
      |
      v
InfinityFree MySQL/MariaDB
      |
      +--> records
      +--> users
      +--> stock_ledger
      +--> audit_log
      +--> settings

There is no background "sync bridge", no LocalStorage database, and no "save locally then
try to sync later" behaviour.

INFINITYFREE DEPLOYMENT
-----------------------
InfinityFree currently provides PHP 8.4 and MySQL 8.0 / MariaDB 11.4 on free hosting.
This application is intentionally built with ordinary PHP + MySQL and does not require
Node.js, Composer, Python, a background worker, or an external API service.

1. Create a MySQL database in your InfinityFree control panel.
2. Note the exact:
      MySQL hostname
      Database name
      Database username
      Database password
3. Open this ZIP and edit config.php BEFORE uploading, OR edit it after uploading.
4. Replace:
      YOUR_INFINITYFREE_MYSQL_HOST
      YOUR_DATABASE_NAME
      YOUR_DATABASE_USERNAME
      YOUR_DATABASE_PASSWORD
5. Upload the CONTENTS of this ZIP into InfinityFree htdocs.
6. Open:
      https://YOUR-DOMAIN/setup.php
7. Create the first administrator username and password.
8. After successful installation, DELETE setup.php from htdocs.
9. Open your normal website address and sign in.

IMPORTANT
---------
Do not use the old FWU files.
Do not upload the old mufms6 folder.
Do not upload the old desktop launcher.
Do not mix old JavaScript files with this version.

The database SQL is included in:
      database/mufms.sql

You can either:
A) use setup.php to create the tables automatically, or
B) import database/mufms.sql through phpMyAdmin and then use setup.php to create the
   first administrator.

MULTI-DEVICE TEST
-----------------
After installation, test this exact sequence:

1. On Device A, sign in.
2. Add a test Inventory item.
3. Confirm it appears in the list.
4. On Device B, open the same website.
5. Sign in.
6. Confirm the same item appears.
7. Edit it on Device B.
8. Refresh Device A.
9. The updated value must be visible.
10. Delete it on Device A.
11. Refresh Device B.
12. It must be gone.

If this test fails, do NOT continue entering real farm data. Check the database connection
first.

WHY THIS VERSION SHOULD NOT REPEAT THE OLD SYNC PROBLEM
--------------------------------------------------------
The old approach kept browser-side data and attempted to copy it to MySQL asynchronously.
That creates several possible failure points: wrong API path, expired session, failed POST,
stale LocalStorage, or a later browser write overwriting a newer database value.

This version has one source of truth:

    MySQL = data
    PHP = business logic
    Browser = display/input only

A successful Save message is shown only after the MySQL transaction has completed.
When another device opens a page, it queries MySQL again.

STOCK INTEGRATION
-----------------
Procurement:
    Procurement saved -> inventory quantity increases.

Sales:
    Sale saved -> inventory quantity decreases.

Editing:
    Previous movement is reversed first, then the new movement is applied.

Deleting:
    The previous movement is reversed.

Stock movements are recorded in stock_ledger.

SECURITY
--------
- Passwords use PHP password_hash().
- Login uses server-side PHP sessions.
- Forms use CSRF tokens.
- Database queries use PDO prepared statements.
- An audit log records create/update/delete activity.
- config.php and SQL files are blocked from direct web access by .htaccess.
- Delete setup.php after installation.

IMPORTANT INFINITYFREE NOTE
---------------------------
InfinityFree's free hosting uses a browser security system. Normal browser access to this
PHP website is the intended use. This application therefore avoids requiring an external
mobile app, automated API client, Node server, or background process.

BACKUP
------
Use phpMyAdmin to export the FWU database regularly.
The application itself does not replace database backups.

MODULES
-------
Dashboard
Crops
Harvest
Livestock
Veterinary
Inventory / Stock
Procurement
Sales
Finance / Expenses
Employees
Equipment
Field Mapping
Customers
Suppliers
Reports
Settings

DEFAULT DATA
------------
No universal default password is shipped.
You create the first administrator password during setup.php.

PRODUCTION RULE
---------------
Once real farm data is entered, do not delete/recreate the database unless you have a
backup. All devices depend on the same central database.
