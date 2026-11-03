FWU / MKANGA-ULIMI - MULTI-DEVICE ENHANCED v1.2

This release is based on the working v1.1 multi-device MySQL application.

CHANGES
- Dashboard revenue is calculated ONLY from Sales records (quantity x selling price).
- Dashboard expenses are calculated ONLY from Procurement records (quantity x unit cost).
- Finance transactions are not added to the dashboard Revenue/Expenses KPIs.
- Financial overview chart uses Sales Revenue vs Procurement Expenses.
- Added visual SVG financial chart; no external chart library is required.
- Added Reports generator with Financial Summary, Sales Revenue and Expenses reports.
- Reports support date filters, Print and CSV export.
- Added Notifications page with live alerts for low stock, upcoming/overdue harvests and veterinary reminders.
- Added notification badge to the top bar.
- Added Gold + Black Mkanga visual identity.
- Added server-side shared Dark Mode toggle stored in MySQL settings.
- Added Stock Ledger page and CSV export.
- Preserved JSON backup/export and JSON restore, including legacy FWU-BACKUP 1.0 compatibility.
- Preserved central MySQL architecture and multi-device operation.

IMPORTANT
The farm records remain server-side. No LocalStorage is used as the source of truth.
Theme preference is stored in MySQL so the current theme is shared across authorised devices.

- Added administrator Reset System Data action to clear farm records, stock ledger and settings while preserving user accounts before a fresh JSON restore.
- Added stronger JSON restore compatibility and stock-ledger reconstruction from Procurement/Sales when an older backup has no ledger section.
- Added calculated Total Price to every Sale (quantity x selling price), recalculated server-side and displayed per customer.
- Added 3D-styled financial overview and livestock distribution pie chart to the dashboard.
- Added Farming with Urunji website link and clickable dashboard logo.
- Updated the light theme to the Mkanga gold/yellow identity.


V1.4 DASHBOARD + AI ADVISOR UPDATE
- Replaced the 3D financial bars with the blue/pink monthly financial line chart shown in the supplied reference image.
- Replaced the inventory pie with a livestock distribution doughnut chart based on livestock records.
- Added an AI Farm Advisor page and dashboard preview with explainable rule-based financial, stock, crop, harvest, veterinary and livestock-health insights.
- Added README_AI_ADVISOR.txt for deployment and feature notes.
