> Current validation: run `python3 tests/store-smoke.py` to check size selection, cart variants, checkout persistence and input rejection. The earlier report below describes the original version.

# Validation

- All 21 PHP files parsed successfully with php-parser 3.7.0.
- All four CREATE TABLE definitions compare byte for byte with source MobileGear commit 73beedee1b1be2db9c0bb132e28c4663db635049.
- Actual products and users INSERT statements execute in a temporary SQLite data fixture; products are exactly five Men and five Women. This checks the seed records, not MySQL compatibility.
- All ten referenced local SVG files exist and parse as XML.
- Orders and order_items have no pre-seeded records.
- Clothing branding and absence of CSS gradients verified.

PHP/MySQL execution and visual browser checks were unavailable in this environment. Complete a local XAMPP smoke check: register, search, add to bag, update quantity, checkout, view order history, then sign in as admin and update the order status.
