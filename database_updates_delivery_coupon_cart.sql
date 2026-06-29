-- Queenzy Mart delivery, pickup, coupon, and cart/order total updates.
-- Run this after importing queecola_mart_a99_queenzy.sql. It does not drop existing data.

ALTER TABLE `orders_mart`
  ADD COLUMN `subtotal_amount` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `appointment_time`,
  ADD COLUMN `fulfilment_type` enum('delivery','pickup') NOT NULL DEFAULT 'delivery' AFTER `order_notes`,
  ADD COLUMN `delivery_zone_id` int(11) DEFAULT NULL AFTER `fulfilment_type`,
  ADD COLUMN `delivery_location` varchar(150) DEFAULT NULL AFTER `delivery_zone_id`,
  ADD COLUMN `delivery_fee` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `subtotal_amount`,
  ADD COLUMN `coupon_id` int(11) DEFAULT NULL AFTER `delivery_fee`,
  ADD COLUMN `coupon_code` varchar(50) DEFAULT NULL AFTER `coupon_id`,
  ADD COLUMN `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `coupon_code`,
  ADD COLUMN `order_status` varchar(64) NOT NULL DEFAULT 'pending' AFTER `payment_status`;

UPDATE `orders_mart` o
LEFT JOIN (
  SELECT `order_item_id`, COALESCE(SUM(`subtotal`), 0.00) AS `item_subtotal`
  FROM `order_items_mart`
  GROUP BY `order_item_id`
) oi ON oi.`order_item_id` = o.`order_tbl_id`
SET
  o.`subtotal_amount` = COALESCE(oi.`item_subtotal`, 0.00),
  o.`delivery_fee` = GREATEST(o.`total_amount` - COALESCE(oi.`item_subtotal`, 0.00), 0.00)
WHERE o.`subtotal_amount` = 0.00;

CREATE TABLE `coupons` (
  `coupon_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `discount_type` enum('fixed','percentage') NOT NULL DEFAULT 'fixed',
  `discount_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `minimum_order_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `start_date` datetime DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `coupons`
  ADD PRIMARY KEY (`coupon_id`),
  ADD UNIQUE KEY `code` (`code`);

ALTER TABLE `coupons`
  MODIFY `coupon_id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `coupon_usage` (
  `usage_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `coupon_code` varchar(50) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `used_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `coupon_usage`
  ADD PRIMARY KEY (`usage_id`),
  ADD KEY `coupon_id` (`coupon_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `coupon_usage`
  MODIFY `usage_id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `delivery_settings` (
  `id` int(11) NOT NULL,
  `delivery_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `pickup_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `default_delivery_fee` decimal(10,2) NOT NULL DEFAULT 10.00,
  `free_delivery_minimum` decimal(10,2) NOT NULL DEFAULT 100.00,
  `pickup_address` varchar(255) DEFAULT '10 London Street, Larkhall, ML9 1AG',
  `pickup_instruction` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `delivery_settings`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `delivery_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `delivery_settings`
  (`delivery_enabled`, `pickup_enabled`, `default_delivery_fee`, `free_delivery_minimum`, `pickup_address`, `pickup_instruction`)
VALUES
  (1, 1, 10.00, 100.00, '10 London Street, Larkhall, ML9 1AG', 'Pickup is available during store opening hours. Bring your order confirmation when collecting.');

CREATE TABLE `delivery_zones` (
  `zone_id` int(11) NOT NULL,
  `zone_name` varchar(150) NOT NULL,
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `delivery_zones`
  ADD PRIMARY KEY (`zone_id`);

ALTER TABLE `delivery_zones`
  MODIFY `zone_id` int(11) NOT NULL AUTO_INCREMENT;
