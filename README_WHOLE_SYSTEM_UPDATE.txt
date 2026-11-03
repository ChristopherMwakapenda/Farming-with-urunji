Farming with Urunji — Whole System Update

See SYSTEM_WHOLE_SYSTEM_FIXES.txt for the full change list and deployment checklist.

Quick start for an existing InfinityFree installation:
1. Back up the current MySQL database and export the app JSON backup.
2. Extract the ZIP locally and review config.php. Preserve the database credentials already used by the owner's account; do not upload placeholder credentials.
3. Deploy first to a staging folder/site and test all four dashboard graphs, venture filters, inventory merging, procurement/sales stock movements, advisory reporting, CSV exports, backup/restore, and mobile layouts.
4. Do not run setup.php, reset, or restore on the production installation unless you explicitly intend to initialise or replace its data.
5. After successful staging checks, upload application files while preserving the live config.php and any owner-specific assets.

The system uses one shared MySQL database. Venture separation is based on each record's venture field and the venture-prefixed stock key; it is not separate databases per venture.
