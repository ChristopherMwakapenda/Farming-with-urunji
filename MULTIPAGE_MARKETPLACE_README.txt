FARMING WITH URUNJI — MULTI-PAGE MARKETPLACE UPGRADE

Public website pages:
- website/index.php (Home)
- website/about.php (About and owner/business information)
- website/products.php (three marketplace categories)
- website/shop-products.php
- website/onion-beans.php
- website/cattle-products.php
- website/advisory.php
- website/contact.php

All pages use the shared website/includes header/footer and website/assets/marketplace.css + marketplace.js.
The original management system files, config.php, database SQL, and existing application modules were retained. No existing database tables or records were edited by this upgrade.

Product management:
- Sign into the existing Farming with Urunji management system first.
- Visit http://localhost/Farming_with_Urunji/website/product-admin.php
- The first visit creates only a new marketplace_products table, if it does not already exist.
- Add product name, category, description, optional price/unit, availability, photo and publish/featured settings.
- Published listings appear in the matching public category page.
- Images are stored in website/uploads/.
- Existing management login session is required.

Contact details configured:
Facebook: https://web.facebook.com/profile.php?id=61555361564144
Instagram: https://www.instagram.com/ (owner-specific URL was not provided)
Email: mezuwaurunji@gmail.com
Phone: 0997 93 23 39
WhatsApp: https://wa.me/265997932339

Local XAMPP URL:
http://localhost/Farming_with_Urunji/website/

Security/operations:
- Keep your existing database and config.php. Do not rerun setup.php on an installed system.
- Product manager requires an authenticated management session and uses the existing CSRF helper.
- Prices and stock are not invented; unpublished prices display as enquiry-only.
- Verify PHP upload limits in php.ini if image uploads fail (recommended upload_max_filesize/post_max_size >= 6M).
