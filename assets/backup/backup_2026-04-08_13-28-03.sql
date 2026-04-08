SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin` VALUES("2","admin","0192023a7bbd73250516f069df18b500");


DROP TABLE IF EXISTS `cancel_requests`;
CREATE TABLE `cancel_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cancel_requests` VALUES("1","21","8","change my mind","rejected","Rejected by officer","2026-04-08 10:10:29");


DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` VALUES("10","Baseball Shirt","This pure cotton jersey baseball shirt is the ideal choice for sports lovers. In a relaxed fit with a pinstriped print and a button-through front, it\'s easily paired with shorts and trousers. A badge on the chest adds a varsity-inspired touch.","Boy","99000.00","100","Baseball-shirt.webp","2026-04-05 19:05:59");
INSERT INTO `products` VALUES("11","Green Stripped Polo","With bold stripes adding a hint of sporty, rugby style, this comfy cotton tee will stand out from the scrum on casual days.","Boy","109000.00","100","green-stripped-polo.webp","2026-04-05 19:07:04");
INSERT INTO `products` VALUES("12","Denim Shorts","A comfy classic for casual days, our light-wash denim shorts with pre-worn fading and rip-and-repair detail are designed in a longer length. Made from durable denim with a little stretch to keep up with every adventure.Responsibly sourced cotton is grown with all-natural growing methods, using less water to produce, and using no harmful chemicals. Safer for their skin and helping protect their world.","Boy","99000.00","100","denim-shorts.webp","2026-04-05 19:08:05");
INSERT INTO `products` VALUES("13","Denim Cargo Jeans","Crafted from cotton denim, these pull-on jeans are designed with a barrel-leg shape. Baggy at the leg and tapered at the ankle, they\'re made for easy movement and comfort.","Boy","149000.00","100","denim-cargo-jeans.webp","2026-04-05 19:08:53");
INSERT INTO `products` VALUES("14","Black Butterfly T-Shirt","A go-to top thats as easy as it is eye-catching. With its relaxed, boxy fit and a flutter of delicate butterflies, its made from cool cotton, perfect for dressing up or keeping it casual.","Girl","99000.00","100","black-butterfly-tshirt.webp","2026-04-05 19:09:47");
INSERT INTO `products` VALUES("15","Pink Cat T-Shirt","Bright and fun, this comfy cotton tee is made for feline fans.","Girl","89000.00","99","pink-cat-tshirt.webp","2026-04-05 19:10:19");
INSERT INTO `products` VALUES("16","Floral Woven Skirt","Filled with flowers, this tiered A-line skirt is ready to twirl into today\'s adventures. A fully elasticated waistband and cotton lining ensures all-day comfort.","Girl","109000.00","97","floral-woven-skirt.webp","2026-04-05 19:11:00");
INSERT INTO `products` VALUES("17","2 Pack Ribbed Leggings","Two pairs of full-length leggings to mix, match and layer. Each pair features a wide, elasticated waistband for comfort and easy dressing.","Girl","89000.00","97","2-pack-ribbed-leggings.webp","2026-04-05 19:11:36");


DROP TABLE IF EXISTS `refund_requests`;
CREATE TABLE `refund_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `account_holder` varchar(100) DEFAULT NULL,
  `refund_amount` int(11) DEFAULT 0,
  `status` enum('pending','approved','rejected','refunded') DEFAULT 'pending',
  `admin_note` text DEFAULT NULL,
  `proof_refund` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `refund_requests` VALUES("1","19","8","oasajs","DANA","081938193","dxfghjkl","5000","approved","Approved by officer",NULL,"2026-04-08 10:14:09");


DROP TABLE IF EXISTS `sales`;
CREATE TABLE `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `sales` VALUES("16","16","15","Pink Cat T-Shirt","0","1","89000","2026-04-05 19:16:43");
INSERT INTO `sales` VALUES("17","17","16","Floral Woven Skirt","0","2","218000","2026-04-07 07:07:45");
INSERT INTO `sales` VALUES("18","18","17","2 Pack Ribbed Leggings","0","1","89000","2026-04-08 07:27:58");
INSERT INTO `sales` VALUES("19","19","17","2 Pack Ribbed Leggings","0","1","89000","2026-04-08 08:48:15");
INSERT INTO `sales` VALUES("20","20","16","Floral Woven Skirt","0","1","109000","2026-04-08 08:49:43");
INSERT INTO `sales` VALUES("21","21","17","2 Pack Ribbed Leggings","0","1","89000","2026-04-08 09:55:21");


DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `total` int(11) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `shipping_address` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_proof` varchar(255) DEFAULT NULL,
  `courier` varchar(100) DEFAULT NULL,
  `shipping_service` varchar(100) DEFAULT NULL,
  `shipping_cost` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `transactions` VALUES("16","8","aufa","89000","transfer",NULL,"cancelled","2026-04-05 19:16:43","1775391403_denim-shorts.webp",NULL,NULL,"0");
INSERT INTO `transactions` VALUES("17","8","aufa","218000","transfer","JL. depok","approved","2026-04-07 07:07:45","1775520465_logo.png",NULL,NULL,"0");
INSERT INTO `transactions` VALUES("18","8","aufa","89000","transfer","JL. depok","approved","2026-04-08 07:27:58","1775608078_logo.png",NULL,NULL,"0");
INSERT INTO `transactions` VALUES("19","8","aufa","99000","cod","JL.","refunded","2026-04-08 08:48:15",NULL,"JNE","Regular","10000");
INSERT INTO `transactions` VALUES("20","8","aufa","130000","transfer","JL.","pending","2026-04-08 08:49:43","1775612983_logo.png","Ninja Xpress","Express","21000");
INSERT INTO `transactions` VALUES("21","8","aufa","110000","transfer","JL.","approved","2026-04-08 09:55:21","1775616921_logo.png","Ninja Xpress","Express","21000");


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `refund_name` varchar(100) DEFAULT NULL,
  `refund_method` varchar(50) DEFAULT NULL,
  `refund_number` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `plain_password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','officer','customer') NOT NULL DEFAULT 'officer',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES("1","admin",NULL,NULL,NULL,NULL,NULL,NULL,"0192023a7bbd73250516f069df18b500",NULL,"2026-02-21 22:01:20","admin");
INSERT INTO `users` VALUES("5","najo",NULL,NULL,NULL,NULL,NULL,"n@gmail.com","202cb962ac59075b964b07152d234b70","123","2026-02-21 22:28:55","officer");
INSERT INTO `users` VALUES("8","aufa","aufa","JL.","","","","aufa@gmail.com","202cb962ac59075b964b07152d234b70",NULL,"2026-02-21 23:14:53","customer");
INSERT INTO `users` VALUES("9","rehan",NULL,NULL,NULL,NULL,NULL,"r@gmail.com","698d51a19d8a121ce581499d7b701668",NULL,"2026-02-21 23:19:06","customer");
INSERT INTO `users` VALUES("11","cust",NULL,NULL,NULL,NULL,NULL,"cust123@gmail.com","202cb962ac59075b964b07152d234b70",NULL,"2026-04-07 09:49:38","customer");


SET FOREIGN_KEY_CHECKS=1;
