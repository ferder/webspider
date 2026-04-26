-- Phase1: 商品采集管理后台

CREATE TABLE IF NOT EXISTS `fa_site_sources` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `site_name` varchar(120) NOT NULL DEFAULT '',
  `base_url` varchar(255) NOT NULL,
  `domain` varchar(120) NOT NULL,
  `country` varchar(50) NOT NULL DEFAULT '',
  `language` varchar(30) NOT NULL DEFAULT '',
  `currency` varchar(10) NOT NULL DEFAULT 'EUR',
  `platform_type` varchar(30) NOT NULL DEFAULT 'unknown',
  `homepage_url` varchar(255) NOT NULL DEFAULT '',
  `sitemap_url` varchar(255) NOT NULL DEFAULT '',
  `robots_url` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(30) NOT NULL DEFAULT 'pending_detect',
  `detect_score` tinyint unsigned NOT NULL DEFAULT 0,
  `remark` varchar(500) NOT NULL DEFAULT '',
  `createtime` int NOT NULL DEFAULT 0,
  `updatetime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_base_url` (`base_url`),
  UNIQUE KEY `uniq_domain` (`domain`),
  KEY `idx_status` (`status`),
  KEY `idx_platform_type` (`platform_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `fa_site_detect_reports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int unsigned NOT NULL,
  `robots_found` tinyint NOT NULL DEFAULT 0,
  `sitemap_found` tinyint NOT NULL DEFAULT 0,
  `product_sitemap_found` tinyint NOT NULL DEFAULT 0,
  `jsonld_found` tinyint NOT NULL DEFAULT 0,
  `platform_guess` varchar(30) NOT NULL DEFAULT 'unknown',
  `need_js_render` tinyint NOT NULL DEFAULT 0,
  `has_product_links` tinyint NOT NULL DEFAULT 0,
  `has_price` tinyint NOT NULL DEFAULT 0,
  `has_images` tinyint NOT NULL DEFAULT 0,
  `anti_crawl_level` varchar(20) NOT NULL DEFAULT 'none',
  `detect_score` tinyint unsigned NOT NULL DEFAULT 0,
  `detect_status` varchar(30) NOT NULL DEFAULT 'success',
  `detect_result_json` text,
  `error_message` varchar(500) NOT NULL DEFAULT '',
  `createtime` int NOT NULL DEFAULT 0,
  `updatetime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_site_id` (`site_id`),
  KEY `idx_detect_status` (`detect_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `fa_site_rules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int unsigned NOT NULL,
  `rule_name` varchar(120) NOT NULL,
  `rule_type` varchar(30) NOT NULL DEFAULT 'custom',
  `list_url_pattern` varchar(255) NOT NULL DEFAULT '',
  `product_url_pattern` varchar(255) NOT NULL DEFAULT '',
  `pagination_rule` varchar(255) NOT NULL DEFAULT '',
  `title_selector` varchar(255) NOT NULL DEFAULT '',
  `price_selector` varchar(255) NOT NULL DEFAULT '',
  `compare_price_selector` varchar(255) NOT NULL DEFAULT '',
  `image_selector` varchar(255) NOT NULL DEFAULT '',
  `description_selector` varchar(255) NOT NULL DEFAULT '',
  `sku_selector` varchar(255) NOT NULL DEFAULT '',
  `stock_selector` varchar(255) NOT NULL DEFAULT '',
  `variant_selector` varchar(255) NOT NULL DEFAULT '',
  `category_selector` varchar(255) NOT NULL DEFAULT '',
  `need_js_render` tinyint NOT NULL DEFAULT 0,
  `crawl_interval` int NOT NULL DEFAULT 1,
  `max_pages` int NOT NULL DEFAULT 0,
  `max_products` int NOT NULL DEFAULT 0,
  `rule_config_json` text,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `createtime` int NOT NULL DEFAULT 0,
  `updatetime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_site_id` (`site_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `fa_crawl_tasks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `task_no` varchar(64) NOT NULL,
  `site_id` int unsigned NOT NULL,
  `rule_id` int unsigned NOT NULL DEFAULT 0,
  `task_type` varchar(30) NOT NULL DEFAULT 'detect',
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `total_urls` int NOT NULL DEFAULT 0,
  `total_products` int NOT NULL DEFAULT 0,
  `success_count` int NOT NULL DEFAULT 0,
  `fail_count` int NOT NULL DEFAULT 0,
  `new_count` int NOT NULL DEFAULT 0,
  `update_count` int NOT NULL DEFAULT 0,
  `start_time` int NOT NULL DEFAULT 0,
  `end_time` int NOT NULL DEFAULT 0,
  `error_message` varchar(500) NOT NULL DEFAULT '',
  `task_config_json` text,
  `createtime` int NOT NULL DEFAULT 0,
  `updatetime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_task_no` (`task_no`),
  KEY `idx_site_status` (`site_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `fa_crawl_task_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int unsigned NOT NULL,
  `site_id` int unsigned NOT NULL,
  `log_level` varchar(20) NOT NULL DEFAULT 'info',
  `stage` varchar(20) NOT NULL DEFAULT 'detect',
  `message` varchar(1000) NOT NULL DEFAULT '',
  `context_json` text,
  `createtime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_task` (`task_id`),
  KEY `idx_level` (`log_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `fa_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int unsigned NOT NULL,
  `source_url` varchar(500) NOT NULL,
  `source_product_id` varchar(120) NOT NULL DEFAULT '',
  `title` varchar(500) NOT NULL DEFAULT '',
  `product_type` varchar(120) NOT NULL DEFAULT '',
  `description` text,
  `short_description` text,
  `seo_title` varchar(500) NOT NULL DEFAULT '',
  `seo_description` varchar(500) NOT NULL DEFAULT '',
  `handle` varchar(255) NOT NULL DEFAULT '',
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `compare_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) NOT NULL DEFAULT 'EUR',
  `sku` varchar(120) NOT NULL DEFAULT '',
  `stock` int NOT NULL DEFAULT 0,
  `weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `data_hash` char(32) NOT NULL DEFAULT '',
  `last_crawled_at` int NOT NULL DEFAULT 0,
  `raw_data_json` mediumtext,
  `createtime` int NOT NULL DEFAULT 0,
  `updatetime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_source_url` (`source_url`(255)),
  KEY `idx_site_id` (`site_id`),
  KEY `idx_handle` (`handle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `fa_product_variants` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `option1_name` varchar(50) NOT NULL DEFAULT '',
  `option1_value` varchar(100) NOT NULL DEFAULT '',
  `option2_name` varchar(50) NOT NULL DEFAULT '',
  `option2_value` varchar(100) NOT NULL DEFAULT '',
  `option3_name` varchar(50) NOT NULL DEFAULT '',
  `option3_value` varchar(100) NOT NULL DEFAULT '',
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `compare_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sku` varchar(120) NOT NULL DEFAULT '',
  `stock` int NOT NULL DEFAULT 0,
  `image_url` varchar(500) NOT NULL DEFAULT '',
  `raw_data_json` text,
  `createtime` int NOT NULL DEFAULT 0,
  `updatetime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `fa_product_images` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `variant_id` int unsigned NOT NULL DEFAULT 0,
  `image_url` varchar(500) NOT NULL,
  `image_position` int NOT NULL DEFAULT 0,
  `image_hash` varchar(64) NOT NULL DEFAULT '',
  `is_main` tinyint NOT NULL DEFAULT 0,
  `createtime` int NOT NULL DEFAULT 0,
  `updatetime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_variant` (`variant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `fa_product_snapshots` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `site_id` int unsigned NOT NULL,
  `source_url` varchar(500) NOT NULL,
  `title` varchar(500) NOT NULL DEFAULT '',
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `compare_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sku` varchar(120) NOT NULL DEFAULT '',
  `stock` int NOT NULL DEFAULT 0,
  `image_hash` varchar(64) NOT NULL DEFAULT '',
  `data_hash` char(32) NOT NULL DEFAULT '',
  `snapshot_json` mediumtext,
  `createtime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_site` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `fa_export_jobs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `job_no` varchar(64) NOT NULL,
  `site_id` int unsigned NOT NULL,
  `task_id` int unsigned NOT NULL DEFAULT 0,
  `export_type` varchar(20) NOT NULL DEFAULT 'all',
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `file_path` varchar(500) NOT NULL DEFAULT '',
  `total_products` int NOT NULL DEFAULT 0,
  `error_message` varchar(500) NOT NULL DEFAULT '',
  `createtime` int NOT NULL DEFAULT 0,
  `updatetime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_job_no` (`job_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `fa_excel_template_fields` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `field_name` varchar(100) NOT NULL,
  `field_key` varchar(80) NOT NULL,
  `field_type` varchar(10) NOT NULL DEFAULT 'string',
  `is_required` tinyint NOT NULL DEFAULT 0,
  `default_value` varchar(255) NOT NULL DEFAULT '',
  `field_order` int NOT NULL DEFAULT 0,
  `remark` varchar(255) NOT NULL DEFAULT '',
  `createtime` int NOT NULL DEFAULT 0,
  `updatetime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_field_key` (`field_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `fa_excel_template_fields` (`field_name`,`field_key`,`field_type`,`is_required`,`default_value`,`field_order`,`remark`) VALUES
('商品标题*','product_title','string',1,'',1,''),
('商品属性*','product_property','enum',1,'S',2,'支持S/M/P'),
('商品类型','product_type','string',0,'',3,''),
('商品描述','description','string',0,'',4,''),
('简短描述','short_description','string',0,'',5,''),
('SEO 标题','seo_title','string',0,'',6,''),
('SEO 描述','seo_description','string',0,'',7,''),
('SEO URL Handle','seo_handle','string',0,'',8,''),
('商品上架','publish_status','enum',0,'N',9,''),
('商品收税','taxable','enum',0,'Y',10,''),
('库存规则*','inventory_rule','enum',1,'1',11,''),
('款式1','option1','string',0,'',12,''),
('款式2','option2','string',0,'',13,''),
('款式3','option3','string',0,'',14,''),
('商品售价*','price','decimal',1,'0',15,''),
('商品原价','compare_price','decimal',0,'0',16,''),
('商品 SKU','sku','string',0,'',17,''),
('商品重量','weight','decimal',0,'0',18,''),
('商品库存','stock','int',0,'0',19,''),
('商品图片*','images','string',1,'',20,'')
ON DUPLICATE KEY UPDATE field_name=VALUES(field_name),field_order=VALUES(field_order),default_value=VALUES(default_value),remark=VALUES(remark);

-- 可选：后台菜单（仅供超级管理员执行）
-- 请在后台“权限管理->菜单规则”中手工导入，或按需执行以下SQL脚本模板。
