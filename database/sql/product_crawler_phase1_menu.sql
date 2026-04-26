-- FastAdmin 菜单初始化脚本（fa_auth_rule）
SET @now = UNIX_TIMESTAMP();

INSERT INTO `fa_auth_rule` (`type`,`pid`,`name`,`title`,`icon`,`condition`,`remark`,`ismenu`,`menutype`,`extend`,`py`,`pinyin`,`weigh`,`status`,`createtime`,`updatetime`)
VALUES (1,0,'crawler','商品采集管理','fa fa-spider','','',1,'addtabs','','spgl','shangpincaijiguanli',130,'normal',@now,@now)
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`),`icon`=VALUES(`icon`),`status`='normal',`updatetime`=@now;

SET @crawler_pid = (SELECT id FROM `fa_auth_rule` WHERE `name`='crawler' LIMIT 1);

INSERT INTO `fa_auth_rule` (`type`,`pid`,`name`,`title`,`icon`,`ismenu`,`menutype`,`weigh`,`status`,`createtime`,`updatetime`) VALUES
(1,@crawler_pid,'crawler/site_source','站点来源管理','fa fa-globe',1,'addtabs',129,'normal',@now,@now),
(1,@crawler_pid,'crawler/site_detect_report','站点检测报告','fa fa-stethoscope',1,'addtabs',128,'normal',@now,@now),
(1,@crawler_pid,'crawler/site_rule','采集规则库','fa fa-sliders',1,'addtabs',127,'normal',@now,@now),
(1,@crawler_pid,'crawler/crawl_task','采集任务管理','fa fa-tasks',1,'addtabs',126,'normal',@now,@now),
(1,@crawler_pid,'crawler/crawl_task_log','采集日志','fa fa-file-text-o',1,'addtabs',125,'normal',@now,@now),
(1,@crawler_pid,'crawler/product','商品数据管理','fa fa-cube',1,'addtabs',124,'normal',@now,@now),
(1,@crawler_pid,'crawler/product_variant','商品规格管理','fa fa-list-ul',1,'addtabs',123,'normal',@now,@now),
(1,@crawler_pid,'crawler/product_image','商品图片管理','fa fa-image',1,'addtabs',122,'normal',@now,@now),
(1,@crawler_pid,'crawler/product_snapshot','商品快照管理','fa fa-history',1,'addtabs',121,'normal',@now,@now),
(1,@crawler_pid,'crawler/export_job','Excel导出任务','fa fa-file-excel-o',1,'addtabs',120,'normal',@now,@now),
(1,@crawler_pid,'crawler/excel_template_field','Excel字段映射','fa fa-columns',1,'addtabs',119,'normal',@now,@now)
ON DUPLICATE KEY UPDATE title=VALUES(title),icon=VALUES(icon),status='normal',updatetime=@now;
