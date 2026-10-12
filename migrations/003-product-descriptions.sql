USE clothhub;
UPDATE products SET description = REPLACE(REPLACE(REPLACE(description, ' Sample size: M.', ''), ' Sample waist: 32 inches.', ''), ' Sample waist: 28 inches.', '') WHERE id BETWEEN 1 AND 10;
UPDATE products SET name = 'Men’s Olive Cotton Shirt', description = 'Olive long-sleeve cotton shirt with two chest pockets and button cuffs.' WHERE id = 5 AND name = 'Men’s Olive Formal Shirt';
UPDATE products SET name = 'Women’s White Cropped Shirt', description = 'White casual cropped shirt with long sleeves and a relaxed fit.' WHERE id = 6 AND name = 'Women’s Ivory Cotton Shirt';
UPDATE products SET description = 'Maroon V-neck t-shirt with short sleeves and a relaxed fit.' WHERE id = 7 AND name = 'Women’s Maroon T-Shirt';
UPDATE products SET description = REPLACE(description, 'High-rise cotton-blend style', 'Cotton-blend style') WHERE id = 8 AND name = 'Women’s Black Trousers';
