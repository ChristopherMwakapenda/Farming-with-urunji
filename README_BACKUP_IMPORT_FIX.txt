FWU BACKUP IMPORT FIX
=======================

Issue fixed:
The current FWU JSON importer filtered every record because normalise_import_backup()
used the global $MODULES registry without declaring it global inside the function.
This caused valid backups to report: "The backup contains no restorable records."

Change:
- Exposed the global module registry inside normalise_import_backup().
- Kept the existing JSON format and database restore flow intact.
- No database credentials, user accounts, or backup data were changed.

Validation:
- PHP syntax check passed (php -l).
- Supplied FWU-BACKUP version 2.0 file contains 771 records across supported modules.

Deployment:
1. Keep a copy of the current hosted files and database backup.
2. Extract this ZIP.
3. Upload/replace the application files, especially index.php, on the FWU host.
4. Sign in as an administrator and retry Settings > Backup & Restore.
5. Confirm RESTORE only when ready to replace current farm records. Restore replaces records,
   stock ledger, and settings; it does not replace user accounts or passwords.
