DATABASE CONNECTION / PDO NULL FIX

This release fixes the crash where index.php continued loading after db() failed, causing get_records(NULL, 'inventory') and a TypeError.

It now stops safely and shows a database-connection diagnostic instead of crashing. This does NOT invent or overwrite your database credentials.

IMPORTANT: config.php in this package contains template credentials unless you replace them. Open config.php and set DB_HOST, DB_NAME, DB_USER and DB_PASS to the actual database settings for your environment.

For XAMPP, the host is commonly localhost, the username is commonly root, and the password is often blank on a default local installation. The DB_NAME must be the exact database you created/imported in phpMyAdmin. For InfinityFree, use the exact MySQL host/database/user/password shown in the hosting control panel; never use localhost there.

After editing config.php, ensure MySQL is running and refresh the application. Do not import setup SQL over existing production data without a backup.
