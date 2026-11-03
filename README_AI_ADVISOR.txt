MKANGA-ULIMI / FWU v1.4 — DASHBOARD CHARTS & FARM ADVISOR
===========================================================

DASHBOARD CHANGES
-----------------
- Replaced the 3D bar chart with a responsive SVG line chart matching the supplied reference: blue sales revenue and pink procurement expenses.
- Preserved the This month / Last month / This year / All time filter.
- Replaced the inventory-quantity pie chart with a livestock distribution doughnut chart.
- Livestock counts are calculated from livestock records grouped by animal type. Inventory is no longer misrepresented as animal population.
- Chart values are calculated at page load from central MySQL records. No chart library or external network dependency is required.

AI FARM ADVISOR
---------------
- Added an AI Farm Advisor navigation page and dashboard preview.
- The advisor currently uses transparent, rule-based decision support calculated from saved records. It does not call a generative AI service and does not require an API key or subscription.
- It compares recorded sales and procurement totals using the existing system rules; flags inventory at or below configured minimums; flags overdue/approaching expected harvests and veterinary due dates; highlights recorded animal health statuses containing sick, ill, poor or injured; and points out crop records missing expected harvest dates.
- Each insight contains a severity label, explanation, suggested next action, and link to the relevant module.
- Recommendations are only as accurate as the underlying data and should support, not replace, field inspections, veterinary advice or formal accounting.

PRESERVATION / DEPLOYMENT
-------------------------
- Existing login/session flow, users, record storage, stock movements, reports, settings, backup/restore, database schema and config.php have been preserved.
- No database migration is required for these dashboard and advisor features.
- Before deploying to the live website, download a fresh JSON backup from Settings and keep a copy of the current live files.
- Upload the updated application files (especially index.php and assets/style.css) over the current deployment. Keep the existing live config.php and database credentials; do not replace them with another environment's config.php.
- Test Dashboard, period filter, Livestock Distribution, AI Farm Advisor, Sales, Procurement, Inventory, Reports and JSON export after deployment.
