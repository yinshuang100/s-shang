
DROP TABLE IF EXISTS wise_web_config;
CREATE TABLE IF NOT EXISTS wise_web_config (
	id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`keyname` varchar(255) NOT NULL DEFAULT '',
	`scope` tinyint(3) NOT NULL default 0,
	`value` text NOT NULL,
	`type` tinyint(3) unsigned NOT NULL DEFAULT 0,
	description text NOT NULL,
	created_time INT(10) NOT NULL DEFAULT 0,
	modified_time INT(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (id),
	UNIQUE KEY idx_key(`keyname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 产品设计类别
DROP TABLE IF EXISTS wise_product_category;
CREATE TABLE IF NOT EXISTS wise_product_category (
	id int(10) unsigned NOT NULL AUTO_INCREMENT,
	pid tinyint(3) NOT NULL default 0,
	seq tinyint(3) NOT NULL default 0,
	`name` varchar(255) NOT NULL DEFAULT '',
	`name_en` varchar(255) NOT NULL DEFAULT '',
	description text NOT NULL,
	status tinyint(3) NOT NULL default 0,
	created_time int(10) NOT NULL DEFAULT 0,
	modified_time int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 产品设计
-- status(0-no, 1-yes)
DROP TABLE IF EXISTS wise_product;
CREATE TABLE IF NOT EXISTS wise_product (
	id int(10) unsigned NOT NULL AUTO_INCREMENT,
	category int(10) unsigned NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	link varchar(255) NOT NULL DEFAULT '',
	thumbpic varchar(255) NOT NULL DEFAULT '',
	pics text NOT NULL,
	summary varchar(255) NOT NULL DEFAULT '',
	content text NOT NULL,
	status tinyint(3) NOT NULL default 0,
	created_time int(10) NOT NULL DEFAULT 0,
	modified_time int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (id),
	INDEX idx_category(category)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 案例类别
DROP TABLE IF EXISTS wise_case_category;
CREATE TABLE IF NOT EXISTS wise_case_category (
	id int(10) unsigned NOT NULL AUTO_INCREMENT,
	pid tinyint(3) NOT NULL default 0,
	seq tinyint(3) NOT NULL default 0,
	`name` varchar(255) NOT NULL DEFAULT '',
	`name_en` varchar(255) NOT NULL DEFAULT '',
	description text NOT NULL,
	status tinyint(3) NOT NULL default 0,
	created_time int(10) NOT NULL DEFAULT 0,
	modified_time int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 案例
DROP TABLE IF EXISTS wise_case;
CREATE TABLE IF NOT EXISTS wise_case (
	id int(10) unsigned NOT NULL AUTO_INCREMENT,
	category int(10) unsigned NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	link varchar(255) NOT NULL DEFAULT '',
	thumbpic varchar(255) NOT NULL DEFAULT '',
	pics text NOT NULL,
	summary varchar(255) NOT NULL DEFAULT '',
	content text NOT NULL,
	status tinyint(3) NOT NULL default 0,
	created_time int(10) NOT NULL DEFAULT 0,
	modified_time int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (id),
	INDEX idx_category(category)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 电子商务类别
DROP TABLE IF EXISTS wise_ecmm_category;
CREATE TABLE IF NOT EXISTS wise_ecmm_category (
	id int(10) unsigned NOT NULL AUTO_INCREMENT,
	pid tinyint(3) NOT NULL default 0,
	seq tinyint(3) NOT NULL default 0,
	`name` varchar(255) NOT NULL DEFAULT '',
	`name_en` varchar(255) NOT NULL DEFAULT '',
	description text NOT NULL,
	status tinyint(3) NOT NULL default 0,
	created_time int(10) NOT NULL DEFAULT 0,
	modified_time int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 电子商务
DROP TABLE IF EXISTS wise_ecmm;
CREATE TABLE IF NOT EXISTS wise_ecmm (
	id int(10) unsigned NOT NULL AUTO_INCREMENT,
	category int(10) unsigned NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	link varchar(255) NOT NULL DEFAULT '',
	thumbpic varchar(255) NOT NULL DEFAULT '',
	pics text NOT NULL,
	summary varchar(255) NOT NULL DEFAULT '',
	content text NOT NULL,
	status tinyint(3) NOT NULL default 0,
	created_time int(10) NOT NULL DEFAULT 0,
	modified_time int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (id),
	INDEX idx_category(category)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wise_articles_category;
CREATE TABLE IF NOT EXISTS wise_articles_category (
	id int(10) unsigned NOT NULL AUTO_INCREMENT,
	pid tinyint(3) NOT NULL default 0,
	seq tinyint(3) NOT NULL default 0,
	`name` varchar(255) NOT NULL DEFAULT '',
	`name_en` varchar(255) NOT NULL DEFAULT '',
	description text NOT NULL,
	status tinyint(3) NOT NULL default 0,
	created_time int(10) NOT NULL DEFAULT 0,
	modified_time int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wise_articles;
CREATE TABLE IF NOT EXISTS wise_articles (
	id int(10) unsigned NOT NULL AUTO_INCREMENT,
	category int(10) unsigned NOT NULL DEFAULT 0,
	author varchar(100) NOT NULL DEFAULT '',
	hit int(10) NOT NULL DEFAULT 0,
	title varchar(255) NOT NULL DEFAULT '',
	content text NOT NULL,
	status tinyint(3) NOT NULL DEFAULT 0,
	created_time int(10) NOT NULL DEFAULT 0,
	modified_time int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (id),
	INDEX idx_category(category)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wise_admins;
CREATE TABLE IF NOT EXISTS wise_admins (
	id int(10) unsigned NOT NULL AUTO_INCREMENT,
	username varchar(20) NOT NULL DEFAULT '',
	userpsw varchar(32) NOT NULL DEFAULT '',
	last_ip varchar(15) NOT NULL DEFAULT '',
	last_date int(11) NOT NULL default 0,
	total_num int(11) NOT NULL default 0,
	created_time int(11) NOT NULL default 0,
	modified_time int(11) NOT NULL default 0,
	PRIMARY KEY  (id),
	INDEX idx_username(username)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO wise_admins set username='admin', userpsw='21232f297a57a5a743894a0e4a801fc3';
