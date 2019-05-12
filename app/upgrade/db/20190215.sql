INSERT IGNORE INTO `[#DB_PREFIX#]system_setting` (`varname`, `value`) VALUES ('geetest_id', 's:32:"a241dea1b2b7e898276a912af75e9039";');
INSERT IGNORE INTO `[#DB_PREFIX#]system_setting` (`varname`, `value`) VALUES ('geetest_key', 's:32:"6917453970f13d6e34135d89b794862c";');
INSERT IGNORE INTO `[#DB_PREFIX#]system_setting` (`varname`, `value`) VALUES ('brand_name', 's:0:"";');

CREATE TABLE IF NOT EXISTS `[#DB_PREFIX#]notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `mobile` char(11) NOT NULL COMMENT '手机号',
  `send_type` varchar(32) NOT NULL COMMENT '短信商',
  `template_code` varchar(32) NOT NULL COMMENT '模板id',
  `content` text COMMENT '短信内容',
  `ip` varchar(32) NOT NULL COMMENT 'ip',
  `add_time` int(11) DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=[#DB_ENGINE#] AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT IGNORE  INTO `[#DB_PREFIX#]menu` (`title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) select '短信记录', 'glyphicon glyphicon-phone', 'admin/tools/notes/', max(id)-2, NULL, '1', '0' from `[#DB_PREFIX#]menu`;

ALTER TABLE  `[#DB_PREFIX#]question_comments` CHANGE  `is_del`  `is_del` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '是否删除0正常1删除';
ALTER TABLE  `[#DB_PREFIX#]article` CHANGE  `is_del`  `is_del` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '是否删除0正常1删除';
ALTER TABLE  `[#DB_PREFIX#]article_comments` CHANGE  `is_del`  `is_del` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '是否删除0正常1删除';
ALTER TABLE  `[#DB_PREFIX#]question` CHANGE  `is_del`  `is_del` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '是否删除0正常1删除';
ALTER TABLE  `[#DB_PREFIX#]answer` CHANGE  `is_del`  `is_del` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '是否删除0正常1删除';
ALTER TABLE  `[#DB_PREFIX#]users` CHANGE  `is_del`  `is_del` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '是否删除0正常1删除';
ALTER TABLE  `[#DB_PREFIX#]users_attrib` CHANGE  `is_del`  `is_del` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '是否删除0正常1删除';
ALTER TABLE  `[#DB_PREFIX#]posts_index` CHANGE  `is_del`  `is_del` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '是否删除0正常1删除';
ALTER TABLE  `[#DB_PREFIX#]topic_relation` CHANGE  `is_del`  `is_del` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '是否删除0正常1删除';
ALTER TABLE  `[#DB_PREFIX#]answer_comments` CHANGE  `is_del`  `is_del` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '是否删除0正常1删除';
ALTER TABLE  `[#DB_PREFIX#]notification` CHANGE  `is_del`  `is_del` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '是否删除0正常1删除';
ALTER TABLE  `[#DB_PREFIX#]user_action_history` CHANGE  `is_del`  `is_del` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '是否删除0正常1删除';
ALTER TABLE  `[#DB_PREFIX#]user_action_history_data` CHANGE  `is_del`  `is_del` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '是否删除0正常1删除';
ALTER TABLE  `[#DB_PREFIX#]user_action_history_fresh` CHANGE  `is_del`  `is_del` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '是否删除0正常1删除';
ALTER TABLE  `[#DB_PREFIX#]user_follow` CHANGE  `is_del`  `is_del` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '是否删除0正常1删除';


alter table `[#DB_PREFIX#]answer` add   `is_best` tinyint(1) NOT NULL COMMENT '是否为最佳回复1是';
alter table `[#DB_PREFIX#]answer` add   `best_uid` int(10) NOT NULL COMMENT '最佳答案的设定人id';
alter table `[#DB_PREFIX#]answer` add   `best_time` int(10) NOT NULL COMMENT '最佳答案的设定时间';
delete from `[#DB_PREFIX#]menu` where `title` = '投诉管理';