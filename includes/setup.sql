-- Non-destructive first-run setup. Existing records are preserved.
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `category` varchar(50) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `shipping_address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `size` varchar(3) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_orderitems_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_orderitems_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `products` VALUES
(1,'Men’s Sand Cotton Shirt','Men','The Cloth Hub','Cotton casual shirt in sand. Regular fit, short sleeves, chest pocket.',899.00,'assets/images/men-shirt.jpg','2026-10-12 00:00:00'),
(2,'Men’s Charcoal T-Shirt','Men','The Cloth Hub','Cotton crew-neck t-shirt in charcoal. Short sleeves and regular fit.',499.00,'assets/images/men-tee.jpg','2026-10-12 00:00:00'),
(3,'Men’s Stone Trousers','Men','The Cloth Hub','Straight-leg trousers in stone. Cotton-blend fabric with belt loops.',1199.00,'assets/images/men-pants.jpg','2026-10-12 00:00:00'),
(4,'Men’s White Formal Shirt','Men','The Cloth Hub','White long-sleeve formal shirt. Structured collar and button cuffs.',1299.00,'assets/images/men-formal-white.jpg','2026-10-12 00:00:00'),
(5,'Men’s Olive Cotton Shirt','Men','The Cloth Hub','Olive long-sleeve cotton shirt with two chest pockets and button cuffs.',1399.00,'assets/images/men-formal-olive.jpg','2026-10-12 00:00:00'),
(6,'Women’s White Cropped Shirt','Women','The Cloth Hub','White casual cropped shirt with long sleeves and a relaxed fit.',899.00,'assets/images/women-shirt.jpg','2026-10-12 00:00:00'),
(7,'Women’s Maroon T-Shirt','Women','The Cloth Hub','Maroon V-neck t-shirt with short sleeves and a relaxed fit.',499.00,'assets/images/women-tee.jpg','2026-10-12 00:00:00'),
(8,'Women’s Black Trousers','Women','The Cloth Hub','Black straight-leg trousers. Cotton-blend style with belt loops.',1199.00,'assets/images/women-pants.jpg','2026-10-12 00:00:00'),
(9,'Women’s White Formal Shirt','Women','The Cloth Hub','White long-sleeve formal shirt. Point collar and neat button cuffs.',1299.00,'assets/images/women-formal-white.jpg','2026-10-12 00:00:00'),
(10,'Women’s Rose Formal Shirt','Women','The Cloth Hub','Dusty rose long-sleeve formal shirt. Point collar and relaxed fit.',1399.00,'assets/images/women-formal-rose.jpg','2026-10-12 00:00:00');

INSERT IGNORE INTO `users` VALUES (1,'Cloth Hub Admin','admin@clothhub.com',NULL,NULL,NULL,NULL,NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','2026-10-12 00:00:00'),(3,'Demo Customer','customer@clothhub.com',NULL,NULL,NULL,NULL,NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','customer','2026-10-12 00:00:00');
