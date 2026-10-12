# PPT compatibility

The Cloth Hub has the same four tables as MobileGear, with one added order-item column:

| Table | Columns |
|---|---|
| users | id, full_name, email, phone, address, district, state, pincode, password, role, created_at |
| products | id, name, category, brand, description, price, image_url, created_at |
| orders | id, user_id, total_amount, status, shipping_address, created_at |
| order_items | id, order_id, product_id, quantity, size, price |

The existing columns, primary keys, indexes, foreign keys and delete rules are unchanged. The nullable `order_items.size VARCHAR(3)` column records the ordered size. The relationships remain users → orders, orders → order_items and products → order_items.

In your PPT, update the project title to The Cloth Hub, the database name to clothhub, screenshots and product examples. Add the size column to the order_items slide; keep the ER relationships. Replace mobile accessory examples with clothing examples. Category values are Men and Women.
