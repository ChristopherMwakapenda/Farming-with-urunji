MKANGA-ULIMI / FWU v1.5 — NO-COST FARM INTELLIGENCE UPGRADE
=============================================================

WHAT IS NEW
-----------
1. AI Assistant page (new navigation item)
   - Offers a simple natural-language-style question box and suggested questions.
   - Supports database-backed answers for sales revenue, procurement totals,
     sales-versus-procurement comparison, low stock, harvest dates, livestock
     record counts and veterinary follow-ups.
   - Uses existing PHP, MySQL records and session handling. No paid API key,
     external service, new dependency or separate data store is required.
   - Unsupported questions receive a transparent fallback rather than a guess.
   - Answers are read-only; the assistant cannot edit records or authorise spending.

2. Expanded AI Farm Advisor
   - Keeps the existing operational and financial rule-based alerts.
   - Adds a six-month sales/procurement table and an indicative monthly baseline
     based on the last three calendar months, including months with no activity.
   - Adds approximate stock-duration estimates for inventory items with recorded
     OUT movements during the last 90 days.
   - Displays limitations beside the estimates; these are not guaranteed forecasts.

3. Compatibility and preservation
   - Existing dashboard graphs, modules, login, user settings, stock ledger,
     reports and JSON backup/restore flow are retained.
   - No SQL migration is required; the upgrade reuses existing records and tables.
   - No external scripts or chart libraries have been added.
   - New analysis is calculated when an authorised user opens a relevant page.
     No cron job or persistent worker is needed.

IMPORTANT LIMITATIONS
--------------------
- The conversational feature is an intent-based assistant, not a generative
  language model. It answers the supported question types shown in the UI.
- Financial comparison is recorded sales minus recorded procurement, not full
  accounting profit, because other costs and inventory valuation may be absent.
- Stock duration assumes recent 90-day OUT movements continue at a similar rate.
  If stock movements are incomplete, the estimate may be misleading.
- Forecasts need accurate dated records and are not guarantees. Field inspections,
  veterinary advice and formal accounting remain necessary.

SAFE DEPLOYMENT TO INFINITYFREE
------------------------------
1. Export a fresh JSON backup from Settings > Backup & Restore.
2. Download a copy of the current live application files.
3. Extract this ZIP locally and upload the updated index.php and assets/style.css
   to the existing application folder.
4. KEEP the live config.php and database credentials. Do not replace them with
   the config.php included in an archive from another environment.
5. Do not run setup.php, setup-admin.php, or reset the system for this upgrade.
6. Test login, dashboard charts and filters, all existing modules, stock ledger,
   reports, JSON export, AI Farm Advisor, AI Assistant, and dark/light themes.
7. Verify AI totals against the Sales and Procurement screens.

ROLLBACK
--------
If anything unexpected happens, restore the saved previous index.php and
assets/style.css. The upgrade makes no schema changes and should not require a
restore of the database. Keep the JSON backup until live tests pass.
