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

-- =========================
-- 已创建表的字段注释更新（可直接执行）
-- 说明：适用于已执行过建表SQL的环境，重复执行安全（仅变更字段注释与定义）
-- =========================

ALTER TABLE `fa_site_sources`
  MODIFY COLUMN `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  MODIFY COLUMN `site_name` varchar(120) NOT NULL DEFAULT '' COMMENT '站点名称',
  MODIFY COLUMN `base_url` varchar(255) NOT NULL COMMENT '站点基准URL',
  MODIFY COLUMN `domain` varchar(120) NOT NULL COMMENT '主域名',
  MODIFY COLUMN `country` varchar(50) NOT NULL DEFAULT '' COMMENT '国家/地区',
  MODIFY COLUMN `language` varchar(30) NOT NULL DEFAULT '' COMMENT '语言',
  MODIFY COLUMN `currency` varchar(10) NOT NULL DEFAULT 'EUR' COMMENT '默认币种',
  MODIFY COLUMN `platform_type` varchar(30) NOT NULL DEFAULT 'unknown' COMMENT '平台类型',
  MODIFY COLUMN `homepage_url` varchar(255) NOT NULL DEFAULT '' COMMENT '首页地址',
  MODIFY COLUMN `sitemap_url` varchar(255) NOT NULL DEFAULT '' COMMENT 'Sitemap地址',
  MODIFY COLUMN `robots_url` varchar(255) NOT NULL DEFAULT '' COMMENT 'Robots地址',
  MODIFY COLUMN `status` varchar(30) NOT NULL DEFAULT 'pending_detect' COMMENT '状态',
  MODIFY COLUMN `detect_score` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '检测得分',
  MODIFY COLUMN `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注',
  MODIFY COLUMN `createtime` int NOT NULL DEFAULT 0 COMMENT '创建时间',
  MODIFY COLUMN `updatetime` int NOT NULL DEFAULT 0 COMMENT '更新时间';

ALTER TABLE `fa_site_detect_reports`
  MODIFY COLUMN `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  MODIFY COLUMN `site_id` int unsigned NOT NULL COMMENT '站点ID',
  MODIFY COLUMN `robots_found` tinyint NOT NULL DEFAULT 0 COMMENT '是否发现robots',
  MODIFY COLUMN `sitemap_found` tinyint NOT NULL DEFAULT 0 COMMENT '是否发现sitemap',
  MODIFY COLUMN `product_sitemap_found` tinyint NOT NULL DEFAULT 0 COMMENT '是否发现商品sitemap',
  MODIFY COLUMN `jsonld_found` tinyint NOT NULL DEFAULT 0 COMMENT '是否发现JSON-LD',
  MODIFY COLUMN `platform_guess` varchar(30) NOT NULL DEFAULT 'unknown' COMMENT '平台识别结果',
  MODIFY COLUMN `need_js_render` tinyint NOT NULL DEFAULT 0 COMMENT '是否需JS渲染',
  MODIFY COLUMN `has_product_links` tinyint NOT NULL DEFAULT 0 COMMENT '是否有商品链接',
  MODIFY COLUMN `has_price` tinyint NOT NULL DEFAULT 0 COMMENT '是否有价格信息',
  MODIFY COLUMN `has_images` tinyint NOT NULL DEFAULT 0 COMMENT '是否有图片信息',
  MODIFY COLUMN `anti_crawl_level` varchar(20) NOT NULL DEFAULT 'none' COMMENT '反爬级别',
  MODIFY COLUMN `detect_score` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '检测得分',
  MODIFY COLUMN `detect_status` varchar(30) NOT NULL DEFAULT 'success' COMMENT '检测状态',
  MODIFY COLUMN `detect_result_json` text COMMENT '检测结果JSON',
  MODIFY COLUMN `error_message` varchar(500) NOT NULL DEFAULT '' COMMENT '错误信息',
  MODIFY COLUMN `createtime` int NOT NULL DEFAULT 0 COMMENT '创建时间',
  MODIFY COLUMN `updatetime` int NOT NULL DEFAULT 0 COMMENT '更新时间';

ALTER TABLE `fa_site_rules`
  MODIFY COLUMN `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  MODIFY COLUMN `site_id` int unsigned NOT NULL COMMENT '站点ID',
  MODIFY COLUMN `rule_name` varchar(120) NOT NULL COMMENT '规则名称',
  MODIFY COLUMN `rule_type` varchar(30) NOT NULL DEFAULT 'custom' COMMENT '规则类型',
  MODIFY COLUMN `list_url_pattern` varchar(255) NOT NULL DEFAULT '' COMMENT '列表页URL规则',
  MODIFY COLUMN `product_url_pattern` varchar(255) NOT NULL DEFAULT '' COMMENT '商品页URL规则',
  MODIFY COLUMN `pagination_rule` varchar(255) NOT NULL DEFAULT '' COMMENT '分页规则',
  MODIFY COLUMN `title_selector` varchar(255) NOT NULL DEFAULT '' COMMENT '标题选择器',
  MODIFY COLUMN `price_selector` varchar(255) NOT NULL DEFAULT '' COMMENT '价格选择器',
  MODIFY COLUMN `compare_price_selector` varchar(255) NOT NULL DEFAULT '' COMMENT '原价选择器',
  MODIFY COLUMN `image_selector` varchar(255) NOT NULL DEFAULT '' COMMENT '图片选择器',
  MODIFY COLUMN `description_selector` varchar(255) NOT NULL DEFAULT '' COMMENT '描述选择器',
  MODIFY COLUMN `sku_selector` varchar(255) NOT NULL DEFAULT '' COMMENT 'SKU选择器',
  MODIFY COLUMN `stock_selector` varchar(255) NOT NULL DEFAULT '' COMMENT '库存选择器',
  MODIFY COLUMN `variant_selector` varchar(255) NOT NULL DEFAULT '' COMMENT '规格选择器',
  MODIFY COLUMN `category_selector` varchar(255) NOT NULL DEFAULT '' COMMENT '分类选择器',
  MODIFY COLUMN `need_js_render` tinyint NOT NULL DEFAULT 0 COMMENT '是否需JS渲染',
  MODIFY COLUMN `crawl_interval` int NOT NULL DEFAULT 1 COMMENT '采集间隔(分钟)',
  MODIFY COLUMN `max_pages` int NOT NULL DEFAULT 0 COMMENT '最大页数',
  MODIFY COLUMN `max_products` int NOT NULL DEFAULT 0 COMMENT '最大商品数',
  MODIFY COLUMN `rule_config_json` text COMMENT '规则配置JSON',
  MODIFY COLUMN `status` varchar(20) NOT NULL DEFAULT 'draft' COMMENT '状态',
  MODIFY COLUMN `createtime` int NOT NULL DEFAULT 0 COMMENT '创建时间',
  MODIFY COLUMN `updatetime` int NOT NULL DEFAULT 0 COMMENT '更新时间';

ALTER TABLE `fa_crawl_tasks`
  MODIFY COLUMN `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  MODIFY COLUMN `task_no` varchar(64) NOT NULL COMMENT '任务编号',
  MODIFY COLUMN `site_id` int unsigned NOT NULL COMMENT '站点ID',
  MODIFY COLUMN `rule_id` int unsigned NOT NULL DEFAULT 0 COMMENT '规则ID',
  MODIFY COLUMN `task_type` varchar(30) NOT NULL DEFAULT 'detect' COMMENT '任务类型',
  MODIFY COLUMN `status` varchar(30) NOT NULL DEFAULT 'pending' COMMENT '任务状态',
  MODIFY COLUMN `total_urls` int NOT NULL DEFAULT 0 COMMENT 'URL总数',
  MODIFY COLUMN `total_products` int NOT NULL DEFAULT 0 COMMENT '商品总数',
  MODIFY COLUMN `success_count` int NOT NULL DEFAULT 0 COMMENT '成功数',
  MODIFY COLUMN `fail_count` int NOT NULL DEFAULT 0 COMMENT '失败数',
  MODIFY COLUMN `new_count` int NOT NULL DEFAULT 0 COMMENT '新增数',
  MODIFY COLUMN `update_count` int NOT NULL DEFAULT 0 COMMENT '更新数',
  MODIFY COLUMN `start_time` int NOT NULL DEFAULT 0 COMMENT '开始时间',
  MODIFY COLUMN `end_time` int NOT NULL DEFAULT 0 COMMENT '结束时间',
  MODIFY COLUMN `error_message` varchar(500) NOT NULL DEFAULT '' COMMENT '错误信息',
  MODIFY COLUMN `task_config_json` text COMMENT '任务配置JSON',
  MODIFY COLUMN `createtime` int NOT NULL DEFAULT 0 COMMENT '创建时间',
  MODIFY COLUMN `updatetime` int NOT NULL DEFAULT 0 COMMENT '更新时间';

ALTER TABLE `fa_crawl_task_logs`
  MODIFY COLUMN `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  MODIFY COLUMN `task_id` int unsigned NOT NULL COMMENT '任务ID',
  MODIFY COLUMN `site_id` int unsigned NOT NULL COMMENT '站点ID',
  MODIFY COLUMN `log_level` varchar(20) NOT NULL DEFAULT 'info' COMMENT '日志级别',
  MODIFY COLUMN `stage` varchar(20) NOT NULL DEFAULT 'detect' COMMENT '阶段',
  MODIFY COLUMN `message` varchar(1000) NOT NULL DEFAULT '' COMMENT '日志内容',
  MODIFY COLUMN `context_json` text COMMENT '上下文JSON',
  MODIFY COLUMN `createtime` int NOT NULL DEFAULT 0 COMMENT '创建时间';

ALTER TABLE `fa_products`
  MODIFY COLUMN `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  MODIFY COLUMN `site_id` int unsigned NOT NULL COMMENT '站点ID',
  MODIFY COLUMN `source_url` varchar(500) NOT NULL COMMENT '来源URL',
  MODIFY COLUMN `source_product_id` varchar(120) NOT NULL DEFAULT '' COMMENT '来源商品ID',
  MODIFY COLUMN `title` varchar(500) NOT NULL DEFAULT '' COMMENT '商品标题',
  MODIFY COLUMN `product_type` varchar(120) NOT NULL DEFAULT '' COMMENT '商品类型',
  MODIFY COLUMN `description` text COMMENT '商品描述',
  MODIFY COLUMN `short_description` text COMMENT '简短描述',
  MODIFY COLUMN `seo_title` varchar(500) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  MODIFY COLUMN `seo_description` varchar(500) NOT NULL DEFAULT '' COMMENT 'SEO描述',
  MODIFY COLUMN `handle` varchar(255) NOT NULL DEFAULT '' COMMENT 'SEO Handle',
  MODIFY COLUMN `price` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '售价',
  MODIFY COLUMN `compare_price` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '原价',
  MODIFY COLUMN `currency` varchar(10) NOT NULL DEFAULT 'EUR' COMMENT '币种',
  MODIFY COLUMN `sku` varchar(120) NOT NULL DEFAULT '' COMMENT 'SKU',
  MODIFY COLUMN `stock` int NOT NULL DEFAULT 0 COMMENT '库存',
  MODIFY COLUMN `weight` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '重量',
  MODIFY COLUMN `status` varchar(20) NOT NULL DEFAULT 'draft' COMMENT '状态',
  MODIFY COLUMN `data_hash` char(32) NOT NULL DEFAULT '' COMMENT '数据哈希',
  MODIFY COLUMN `last_crawled_at` int NOT NULL DEFAULT 0 COMMENT '最后采集时间',
  MODIFY COLUMN `raw_data_json` mediumtext COMMENT '原始数据JSON',
  MODIFY COLUMN `createtime` int NOT NULL DEFAULT 0 COMMENT '创建时间',
  MODIFY COLUMN `updatetime` int NOT NULL DEFAULT 0 COMMENT '更新时间';

ALTER TABLE `fa_product_variants`
  MODIFY COLUMN `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  MODIFY COLUMN `product_id` int unsigned NOT NULL COMMENT '商品ID',
  MODIFY COLUMN `option1_name` varchar(50) NOT NULL DEFAULT '' COMMENT '规格1名称',
  MODIFY COLUMN `option1_value` varchar(100) NOT NULL DEFAULT '' COMMENT '规格1值',
  MODIFY COLUMN `option2_name` varchar(50) NOT NULL DEFAULT '' COMMENT '规格2名称',
  MODIFY COLUMN `option2_value` varchar(100) NOT NULL DEFAULT '' COMMENT '规格2值',
  MODIFY COLUMN `option3_name` varchar(50) NOT NULL DEFAULT '' COMMENT '规格3名称',
  MODIFY COLUMN `option3_value` varchar(100) NOT NULL DEFAULT '' COMMENT '规格3值',
  MODIFY COLUMN `price` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '售价',
  MODIFY COLUMN `compare_price` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '原价',
  MODIFY COLUMN `sku` varchar(120) NOT NULL DEFAULT '' COMMENT 'SKU',
  MODIFY COLUMN `stock` int NOT NULL DEFAULT 0 COMMENT '库存',
  MODIFY COLUMN `image_url` varchar(500) NOT NULL DEFAULT '' COMMENT '图片URL',
  MODIFY COLUMN `raw_data_json` text COMMENT '原始数据JSON',
  MODIFY COLUMN `createtime` int NOT NULL DEFAULT 0 COMMENT '创建时间',
  MODIFY COLUMN `updatetime` int NOT NULL DEFAULT 0 COMMENT '更新时间';

ALTER TABLE `fa_product_images`
  MODIFY COLUMN `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  MODIFY COLUMN `product_id` int unsigned NOT NULL COMMENT '商品ID',
  MODIFY COLUMN `variant_id` int unsigned NOT NULL DEFAULT 0 COMMENT '规格ID',
  MODIFY COLUMN `image_url` varchar(500) NOT NULL COMMENT '图片URL',
  MODIFY COLUMN `image_position` int NOT NULL DEFAULT 0 COMMENT '图片排序',
  MODIFY COLUMN `image_hash` varchar(64) NOT NULL DEFAULT '' COMMENT '图片哈希',
  MODIFY COLUMN `is_main` tinyint NOT NULL DEFAULT 0 COMMENT '是否主图',
  MODIFY COLUMN `createtime` int NOT NULL DEFAULT 0 COMMENT '创建时间',
  MODIFY COLUMN `updatetime` int NOT NULL DEFAULT 0 COMMENT '更新时间';

ALTER TABLE `fa_product_snapshots`
  MODIFY COLUMN `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  MODIFY COLUMN `product_id` int unsigned NOT NULL COMMENT '商品ID',
  MODIFY COLUMN `site_id` int unsigned NOT NULL COMMENT '站点ID',
  MODIFY COLUMN `source_url` varchar(500) NOT NULL COMMENT '来源URL',
  MODIFY COLUMN `title` varchar(500) NOT NULL DEFAULT '' COMMENT '标题',
  MODIFY COLUMN `price` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '售价',
  MODIFY COLUMN `compare_price` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '原价',
  MODIFY COLUMN `sku` varchar(120) NOT NULL DEFAULT '' COMMENT 'SKU',
  MODIFY COLUMN `stock` int NOT NULL DEFAULT 0 COMMENT '库存',
  MODIFY COLUMN `image_hash` varchar(64) NOT NULL DEFAULT '' COMMENT '图片哈希',
  MODIFY COLUMN `data_hash` char(32) NOT NULL DEFAULT '' COMMENT '数据哈希',
  MODIFY COLUMN `snapshot_json` mediumtext COMMENT '快照JSON',
  MODIFY COLUMN `createtime` int NOT NULL DEFAULT 0 COMMENT '创建时间';

ALTER TABLE `fa_export_jobs`
  MODIFY COLUMN `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  MODIFY COLUMN `job_no` varchar(64) NOT NULL COMMENT '导出任务号',
  MODIFY COLUMN `site_id` int unsigned NOT NULL COMMENT '站点ID',
  MODIFY COLUMN `task_id` int unsigned NOT NULL DEFAULT 0 COMMENT '采集任务ID',
  MODIFY COLUMN `export_type` varchar(20) NOT NULL DEFAULT 'all' COMMENT '导出类型',
  MODIFY COLUMN `status` varchar(20) NOT NULL DEFAULT 'pending' COMMENT '状态',
  MODIFY COLUMN `file_path` varchar(500) NOT NULL DEFAULT '' COMMENT '文件路径',
  MODIFY COLUMN `total_products` int NOT NULL DEFAULT 0 COMMENT '商品总数',
  MODIFY COLUMN `error_message` varchar(500) NOT NULL DEFAULT '' COMMENT '错误信息',
  MODIFY COLUMN `createtime` int NOT NULL DEFAULT 0 COMMENT '创建时间',
  MODIFY COLUMN `updatetime` int NOT NULL DEFAULT 0 COMMENT '更新时间';

ALTER TABLE `fa_excel_template_fields`
  MODIFY COLUMN `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  MODIFY COLUMN `field_name` varchar(100) NOT NULL COMMENT '字段名称',
  MODIFY COLUMN `field_key` varchar(80) NOT NULL COMMENT '字段键名',
  MODIFY COLUMN `field_type` varchar(10) NOT NULL DEFAULT 'string' COMMENT '字段类型',
  MODIFY COLUMN `is_required` tinyint NOT NULL DEFAULT 0 COMMENT '是否必填',
  MODIFY COLUMN `default_value` varchar(255) NOT NULL DEFAULT '' COMMENT '默认值',
  MODIFY COLUMN `field_order` int NOT NULL DEFAULT 0 COMMENT '字段排序',
  MODIFY COLUMN `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  MODIFY COLUMN `createtime` int NOT NULL DEFAULT 0 COMMENT '创建时间',
  MODIFY COLUMN `updatetime` int NOT NULL DEFAULT 0 COMMENT '更新时间';
