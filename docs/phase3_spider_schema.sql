-- Phase 3 schema additions (execute manually)
CREATE TABLE IF NOT EXISTS `fa_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int unsigned NOT NULL DEFAULT 0,
  `crawl_task_id` int unsigned NOT NULL DEFAULT 0,
  `source_url` varchar(1024) NOT NULL,
  `source_product_id` varchar(128) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL,
  `product_type` varchar(128) NOT NULL DEFAULT '',
  `description` longtext,
  `short_description` text,
  `seo_title` varchar(255) NOT NULL DEFAULT '',
  `seo_description` text,
  `handle` varchar(255) NOT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `compare_price` decimal(12,2) DEFAULT NULL,
  `currency` varchar(16) NOT NULL DEFAULT 'USD',
  `sku` varchar(128) NOT NULL DEFAULT '',
  `stock` int NOT NULL DEFAULT 0,
  `weight` varchar(64) NOT NULL DEFAULT '',
  `data_hash` varchar(32) NOT NULL,
  `image_count` int NOT NULL DEFAULT 0,
  `variant_count` int NOT NULL DEFAULT 0,
  `status` varchar(32) NOT NULL DEFAULT 'draft',
  `last_compare_status` varchar(32) NOT NULL DEFAULT 'new',
  `last_crawled_at` int NOT NULL DEFAULT 0,
  `raw_data_json` longtext,
  `createtime` int NOT NULL DEFAULT 0,
  `updatetime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_site_source_url` (`site_id`,`source_url`(191)),
  KEY `idx_site_handle` (`site_id`,`handle`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `fa_product_images` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `image_url` varchar(1024) NOT NULL,
  `image_hash` varchar(32) NOT NULL,
  `image_position` int NOT NULL DEFAULT 1,
  `is_main` tinyint NOT NULL DEFAULT 0,
  `createtime` int NOT NULL DEFAULT 0,
  `updatetime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_product_url` (`product_id`,`image_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `fa_product_variants` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `option1_name` varchar(64) NOT NULL DEFAULT '',
  `option1_value` varchar(128) NOT NULL DEFAULT '',
  `option2_name` varchar(64) NOT NULL DEFAULT '',
  `option2_value` varchar(128) NOT NULL DEFAULT '',
  `option3_name` varchar(64) NOT NULL DEFAULT '',
  `option3_value` varchar(128) NOT NULL DEFAULT '',
  `price` decimal(12,2) DEFAULT NULL,
  `compare_price` decimal(12,2) DEFAULT NULL,
  `sku` varchar(128) NOT NULL DEFAULT '',
  `stock` int NOT NULL DEFAULT 0,
  `image_url` varchar(1024) NOT NULL DEFAULT '',
  `createtime` int NOT NULL DEFAULT 0,
  `updatetime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `fa_product_snapshots` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `site_id` int unsigned NOT NULL,
  `source_url` varchar(1024) NOT NULL,
  `data_hash` varchar(32) NOT NULL,
  `change_type` varchar(32) NOT NULL DEFAULT 'update',
  `snapshot_json` longtext,
  `createtime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
