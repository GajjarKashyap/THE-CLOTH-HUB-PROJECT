# The Cloth Hub

A college clothing-store project adapted from MobileGear. Native PHP + PDO, MySQL/MariaDB, HTML, CSS and vanilla JavaScript. No framework or npm build.

## Get the project
```bash
git clone https://github.com/GajjarKashyap/THE-CLOTH-HUB-PROJECT.git the-cloth-hub
```
Place this folder in XAMPP htdocs.

## Start with XAMPP
1. Extract `the-cloth-hub` into `C:\xampp\htdocs\` on Windows, or `/opt/lampp/htdocs/` on Linux.
2. Start Apache and MySQL in XAMPP.
3. Open the application. It automatically creates the missing `clothhub` database, its tables and demo catalogue on first use. No manual SQL import is required.
4. Default credentials: 127.0.0.1, root, empty password. For other servers set `CLOTHHUB_DB_HOST`, `CLOTHHUB_DB_USER`, `CLOTHHUB_DB_PASSWORD`, and optionally `CLOTHHUB_DB_NAME`, or edit `includes/database.php`. The database account needs CREATE permission for automatic setup.
5. Open `http://localhost/the-cloth-hub/`.

Demo administrator: `admin@clothhub.com` / `password`.
Demo customer: `customer@clothhub.com` / `password`.

Import this SQL into a fresh project database: importing it again resets its tables and sample data.

## Database and sizes
The app uses MySQL/MariaDB, not SQLite. Its database is `clothhub`.
The four tables are `users`, `products`, `orders` and `order_items`.
`order_items.size` stores the selected S, M, L, XL or XXL for each ordered item.
Existing orders retain NULL for sizes that were never recorded.

Existing databases automatically receive the missing size column. For the catalogue photo/description updates on an older installation, import SQL files in `migrations/` in numerical order.
These migrations preserve customers and orders; do not reimport `database.sql` into an existing database.

## Collection
Five products each for Men and Women. Sizes S, M, L, XL and XXL can be selected on product cards and detail pages. Different sizes of the same product remain separate cart entries and are saved in the order confirmation. Sizes are demo choices; this project does not track inventory per size.

Ten real product photos from Amazon-hosted image URLs are saved locally and work offline. The source URLs are recorded in `assets/images/photo-sources.json`. Photos represent sample catalogue products; prices are demo data, not live Amazon prices. Confirm photo reuse rights before publishing a commercial store.

## Features
Registration/login, product search and collection filter, price sorting, product detail, cart quantity updates/removal, cash-on-delivery order creation, address/profile editing, order history, administrator dashboard, product addition, customer list and order status updates.

The initial catalogue contains exactly ten products. The original admin add-product feature is retained, so an admin can expand it.

## Design
White and warm neutral backgrounds, charcoal text, muted maroon accent, serif headings and clear sans-serif body text. Square edges, no gradients and no decorative card shadows. Responsive layouts.

## Project limits
This remains a college demo adapted from the original backend. It does not charge cards, send SMS/email, track couriers, manage stock or provide real review data. Orders are stored in SQL and can be updated by an administrator.

Original project: https://github.com/GajjarKashyap/Mobileger-college-Project-
Original author: Gajjar Kashyap. Adaptation: The Cloth Hub.

## Checks
Run `python3 tests/store-smoke.py` with local Apache and MySQL running.
Run `php tests/database-setup.php` to verify automatic setup and safe migration in a disposable database.
It uses the supplied demo customer, places a test order, and removes that test order afterward.

Automatic setup uses `includes/setup.sql`, which contains no DROP statements and preserves existing records. If MySQL is stopped or credentials are wrong, the app gives a setup message; it cannot repair those server settings itself.
