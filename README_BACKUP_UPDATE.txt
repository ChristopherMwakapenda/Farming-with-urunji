FWU BACKUP / DASHBOARD UPDATE
================================

This build remains server-first and multi-device.

NEW FEATURES
-------------
1. JSON backup export from Settings.
   - Exports records, settings and stock ledger from central MySQL.
   - Does not export password hashes or active login sessions.
   - Filename format: FWU-Backup-YYYY-MM-DD_HH-mm-ss.json

2. JSON backup restore from Settings.
   - Administrator-only.
   - Requires selecting a JSON file and typing RESTORE.
   - Runs as one MySQL transaction.
   - Replaces records, settings and stock ledger without changing users/passwords.
   - Compatible with the older FWU-BACKUP 1.0 local-storage JSON format.
   - Legacy module data is converted into the current server record structure.

3. Dashboard enhancements.
   - Central database KPI cards.
   - Revenue, expenses and net totals.
   - Monthly financial performance bars.
   - Low-stock alerts based on inventory minimums.
   - Upcoming harvest reminders (30 days).
   - Veterinary reminders (30 days).
   - Recent audit activity.
   - Quick actions to core modules.

4. Settings user-management actions are now handled server-side.
   - Administrator-only user creation.
   - Administrator-only user enable/disable.

IMPORTANT
---------
The multi-device architecture remains unchanged:
Browser -> PHP -> MySQL.

Do not add the old mufms6 JavaScript/localStorage synchronization layer to this build.
The browser is only the interface; MySQL remains the source of truth.

RESTORE NOTE
------------
Always export a fresh backup before restoring another backup. A restore replaces the
current application records/settings/stock ledger. Users and passwords are retained.
