FARMING WITH URUNJI — SYSTEM & WEBSITE

This is a SEPARATE, STANDALONE Farming with Urunji project. It is delivered as its own copy of the PHP + MySQL application with its own branding, configuration, database setup, and public website. It must not be installed over, or configured to use, the Mkanga application folder or Mkanga database. The supplied shop photographs are included.

Business areas supported in the interface:
- Shop
- Beans & Onions Farm
- Cattle Farm
- Shop Advisory Services (client engagements, custom service category, scope, consultant, dates, status, fees, payments, direct costs and outcome notes; tracked under the Shop venture)
- Group Finance with venture tabs and a combined overview
- Employee records with joining date, salary and salary-payment fields

Advisory service categories are editable per engagement through a flexible “Other / custom client request” option. Advisory records and finance entries use the existing shared records table. The public website includes an Agricultural & Agribusiness Advisory section and a consultation enquiry link to the business Facebook page.

INSTALLATION
1. Create a NEW folder/subdomain for Farming with Urunji. Do not overwrite or replace any Mkanga files.
2. Create a NEW, separate MySQL database specifically for Farming with Urunji. Do not import this SQL into the Mkanga database.
3. Upload/extract this package into the new Farming with Urunji folder.
4. Configure config.php with the credentials for the NEW Farming with Urunji database only; do not reuse exposed old credentials.
5. Import database/mufms.sql into that NEW database or run setup.php according to the project instructions.
5. Use setup-admin.php to create/configure the administrator as described in the existing setup instructions.
6. Open website/index.html for the public website; open index.php for the management system.
7. Test create/edit/delete, finance filters, employee salary posting, advisory fee recording and reports using sample data before live use.

IMPORTANT
- No real client data is included.
- This is a PHP/MySQL package; a live database and hosting environment are required to validate full runtime behaviour.
- Salary payments are designed to create finance expense entries through the existing salary-payment logic. Verify this in your own database before production use.
- Financial dashboard and reporting calculations should be reconciled with the client's accounting practice before being used for official accounts.


PROJECT ISOLATION CHECKLIST
- Separate project folder: required.
- Separate database: required.
- Separate database credentials: required.
- Do not overwrite Mkanga files.
- Do not point config.php at the Mkanga database.


OWNER / BUSINESS PROFILE UPDATE
Publicly available media coverage identifies Urunji Mezuwa as the farmer behind Farming with Urunji and describes commercial activity involving groundnuts, potatoes, rice production and livestock. The public website now includes an owner profile section, links to the Facebook page supplied by the client, and a published media feature. Facebook itself blocked automated page retrieval during this update, so this package does not claim to contain every post, photo, product, price or contact detail from that page. No unverified Facebook post data has been invented or copied.

To complete a full page-based content import, provide screenshots or a Facebook data export of the page's About section, posts, products, phone/email, locations and images. Review all owner-facing public details before publishing.


NOVEMBER 2026 OWNER-REQUESTED IMPROVEMENTS
- Dashboard contains four vertically stacked financial trend graphs: Shop, Beans & Onions Farm, Cattle Farm, and the combined business group. Each graph has a shared date-range filter and compares sales/service income against procurement/service costs.
- Shop charts use Shop-tagged sales and procurement only. Farm and cattle charts use their own venture tags. Advisory payments and direct project costs are assigned to Shop.
- The main dashboard revenue/expense/net figures are based on sales, procurement, advisory payments and advisory direct costs. Group Finance transactions remain separate to avoid double-counting the same sale/purchase.
- Inventory item matching ignores capital letters. Adding an existing item with the same venture and unit adds to its existing quantity. A different venture or unit remains a separate stock record. Manual inventory additions are also written to the stock ledger.
- Stock adjustments from procurement and sales are scoped by venture, item name (case-insensitive), and unit. This prevents shop stock from being changed by a farm or cattle transaction for a similarly named item.
- Inventory page instructions explain the quantity merge behaviour.

IMPORTANT TESTING NOTE
PHP syntax checks passed for all PHP files in this package. Full create/edit/sale/procurement/database tests still require the target hosting account and its configured MySQL database. Before live use, test one sample item in each venture, add the same item with different capitalisation, record procurement and sales, and verify all four graphs and stock balances. Back up the live database before replacing any deployed files.
