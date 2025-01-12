<?php

use yii\db\Migration;

class m130524_201888_cms extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $sql = "
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `fb_cms_catalog`;
CREATE TABLE `fb_cms_catalog` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint(20) unsigned NOT NULL DEFAULT '1' COMMENT '商家',
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父节点',
  `name` varchar(255) NOT NULL COMMENT '标题',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '代码',
  `is_nav` int(11) NOT NULL DEFAULT '1' COMMENT '导航栏显示',
  `banner` json DEFAULT NULL COMMENT '横幅图',
  `banner_h5` json DEFAULT NULL COMMENT '手机横幅图',
  `seo_title` varchar(255) NOT NULL DEFAULT '' COMMENT '搜索优化标题',
  `seo_keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '搜索关键词',
  `seo_description` text COMMENT '搜索描述',
  `brief` text COMMENT '简介',
  `content` text COMMENT '内容',
  `redirect_url` varchar(255) NOT NULL DEFAULT '' COMMENT '跳转链接',
  `page_size` int(11) NOT NULL DEFAULT '12' COMMENT '分页数量',
  `kind` int(11) NOT NULL DEFAULT '1' COMMENT '种类',
  `template` varchar(255) NOT NULL DEFAULT 'list' COMMENT '模板',
  `template_page` varchar(255) NOT NULL DEFAULT 'page' COMMENT '详情页模板',
  `type` varchar(255) NOT NULL DEFAULT 'list' COMMENT '类型',
  `sort` int(11) NOT NULL DEFAULT '50' COMMENT '排序',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态',
  `created_at` int(11) NOT NULL DEFAULT '1' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '1' COMMENT '更新时间',
  `created_by` bigint(20) unsigned NOT NULL DEFAULT '1' COMMENT '创建用户',
  `updated_by` bigint(20) unsigned NOT NULL DEFAULT '1' COMMENT '更新用户',
  PRIMARY KEY (`id`),
  KEY `cms_catalog_k2` (`store_id`),
  CONSTRAINT `cms_catalog_fk2` FOREIGN KEY (`store_id`) REFERENCES `fb_store` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='栏目';

-- ----------------------------
-- Table structure for fb_cms_page
-- ----------------------------
DROP TABLE IF EXISTS `fb_cms_page`;
CREATE TABLE `fb_cms_page` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint(20) unsigned NOT NULL DEFAULT '1' COMMENT '商家',
  `catalog_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '栏目',
  `name` varchar(255) NOT NULL COMMENT '标题',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '代码',
  `banner` json DEFAULT NULL COMMENT '横幅图',
  `banner_h5` json DEFAULT NULL COMMENT '手机横幅图',
  `thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图',
  `images` json DEFAULT NULL COMMENT '图片集',
  `seo_title` varchar(255) NOT NULL DEFAULT '' COMMENT '搜索优化标题',
  `seo_keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '搜索关键词',
  `seo_description` text COMMENT '搜索描述',
  `brief` text COMMENT '简介',
  `content` text COMMENT '内容',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `redirect_url` varchar(255) NOT NULL DEFAULT '' COMMENT '跳转链接',
  `kind` int(11) NOT NULL DEFAULT '1' COMMENT '种类',
  `format` int(11) NOT NULL DEFAULT '1' COMMENT '格式',
  `template` varchar(255) NOT NULL DEFAULT 'page' COMMENT '模板',
  `click` int(11) NOT NULL DEFAULT '0' COMMENT '浏览量',
  `param1` varchar(255) NOT NULL DEFAULT '' COMMENT '页面参数1',
  `param2` varchar(255) NOT NULL DEFAULT '' COMMENT '页面参数2',
  `param3` varchar(255) NOT NULL DEFAULT '' COMMENT '页面参数3',
  `param4` varchar(255) NOT NULL DEFAULT '' COMMENT '页面参数4',
  `param5` varchar(255) NOT NULL DEFAULT '' COMMENT '页面参数5',
  `param6` varchar(255) NOT NULL DEFAULT '' COMMENT '页面参数6',
  `param7` int(11) NOT NULL DEFAULT '0' COMMENT '页面参数7',
  `param8` int(11) NOT NULL DEFAULT '0' COMMENT '页面参数8',
  `param9` int(11) NOT NULL DEFAULT '0' COMMENT '页面参数9',
  `param10` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '页面参数10',
  `type` varchar(255) NOT NULL DEFAULT 'list' COMMENT '类型',
  `sort` int(11) NOT NULL DEFAULT '50' COMMENT '排序',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态',
  `created_at` int(11) NOT NULL DEFAULT '1' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '1' COMMENT '更新时间',
  `created_by` bigint(20) unsigned NOT NULL DEFAULT '1' COMMENT '创建用户',
  `updated_by` bigint(20) unsigned NOT NULL DEFAULT '1' COMMENT '更新用户',
  PRIMARY KEY (`id`),
  KEY `cms_page_k1` (`catalog_id`),
  KEY `cms_page_k2` (`store_id`),
  CONSTRAINT `cms_page_fk1` FOREIGN KEY (`catalog_id`) REFERENCES `fb_cms_catalog` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `cms_page_fk2` FOREIGN KEY (`store_id`) REFERENCES `fb_store` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='页面';

-- ALTER TABLE `fb_cms_catalog` ADD COLUMN `kind` int(11) NOT NULL DEFAULT '1' COMMENT '种类' AFTER `page_size`;  
-- ALTER TABLE `fb_cms_catalog` ADD COLUMN `code` varchar(255) NOT NULL DEFAULT '' COMMENT '代码' AFTER `name`;  
-- ALTER TABLE `fb_cms_page` ADD COLUMN `kind` int(11) NOT NULL DEFAULT '1' COMMENT '种类' AFTER `redirect_url`;  
-- ALTER TABLE `fb_cms_page` ADD COLUMN `format` int(11) NOT NULL DEFAULT '1' COMMENT '格式' AFTER `kind`;  
-- ALTER TABLE `fb_cms_page` ADD COLUMN `code` varchar(255) NOT NULL DEFAULT '' COMMENT '代码' AFTER `name`;  
-- ALTER TABLE `fb_cms_page` change `para1` `param1` varchar(255) NOT NULL DEFAULT '' COMMENT '页面参数1';  
-- ALTER TABLE `fb_cms_page` change `para2` `param2` varchar(255) NOT NULL DEFAULT '' COMMENT '页面参数2';  
-- ALTER TABLE `fb_cms_page` change `para3` `param3` varchar(255) NOT NULL DEFAULT '' COMMENT '页面参数3';  
-- ALTER TABLE `fb_cms_page` change `para4` `param4` varchar(255) NOT NULL DEFAULT '' COMMENT '页面参数4';  
-- ALTER TABLE `fb_cms_page` change `para5` `param5` varchar(255) NOT NULL DEFAULT '' COMMENT '页面参数5';  
-- ALTER TABLE `fb_cms_page` change `para6` `param6` varchar(255) NOT NULL DEFAULT '' COMMENT '页面参数6';  
-- ALTER TABLE `fb_cms_page` change `para7` `param7` int(11) NOT NULL DEFAULT '0' COMMENT '页面参数7';
-- ALTER TABLE `fb_cms_page` change `para8` `param8` int(11) NOT NULL DEFAULT '0' COMMENT '页面参数8';
-- ALTER TABLE `fb_cms_page` change `para9` `param9` int(11) NOT NULL DEFAULT '0' COMMENT '页面参数9';
-- ALTER TABLE `fb_cms_page` change `para10` `param10` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '页面参数10';

        ";

        $this->execute($sql);


        $sql = "
SET FOREIGN_KEY_CHECKS=0;


INSERT INTO `fb_base_permission` VALUES ('54', '1', '5', 'Cms网站', 'backend', '', '', 'fab fa-internet-explorer', '', '2', '0', '1', '50', '1', '1599358315', '1603847699', '1', '1');

INSERT INTO `fb_base_permission` VALUES ('541', '1', '54', '页面管理', 'backend', '', '/cms/page/index', 'fas fa-file', '', '3', '0', '1', '50', '1', '1599358315', '1603847794', '1', '1');
INSERT INTO `fb_base_permission` VALUES ('543', '1', '54', '栏目管理', 'backend', '', '/cms/catalog/index', 'fas fa-list', '', '3', '0', '1', '50', '1', '1599358315', '1603847833', '1', '1');

INSERT INTO `fb_base_permission` VALUES ('5411', '1', '541', '查看', 'backend', '', '/cms/page/view*', '', '', '4', '0', '1', '50', '1', '1', '1', '1', '1');
INSERT INTO `fb_base_permission` VALUES ('5412', '1', '541', '编辑', 'backend', '', '/cms/page/edit*', '', '', '4', '0', '1', '50', '1', '1', '1', '1', '1');
INSERT INTO `fb_base_permission` VALUES ('5413', '1', '541', '删除', 'backend', '', '/cms/page/delete*', '', '', '4', '0', '1', '50', '1', '1', '1', '1', '1');
INSERT INTO `fb_base_permission` VALUES ('5414', '1', '541', '启禁', 'backend', '', '/cms/page/status*', '', '', '4', '0', '1', '50', '1', '1', '1', '1', '1');
INSERT INTO `fb_base_permission` VALUES ('5415', '1', '541', '导出', 'backend', '', '/cms/page/export*', '', '', '4', '0', '1', '50', '1', '1', '1', '1', '1');
INSERT INTO `fb_base_permission` VALUES ('5416', '1', '541', '导入', 'backend', '', '/cms/page/import*', '', '', '4', '0', '1', '50', '1', '1', '1', '1', '1');
INSERT INTO `fb_base_permission` VALUES ('5431', '1', '543', '查看', 'backend', '', '/cms/catalog/view*', '', '', '4', '0', '1', '50', '1', '1', '1', '1', '1');
INSERT INTO `fb_base_permission` VALUES ('5432', '1', '543', '编辑', 'backend', '', '/cms/catalog/edit*', '', '', '4', '0', '1', '50', '1', '1', '1', '1', '1');
INSERT INTO `fb_base_permission` VALUES ('5433', '1', '543', '删除', 'backend', '', '/cms/catalog/delete*', '', '', '4', '0', '1', '50', '1', '1', '1', '1', '1');
INSERT INTO `fb_base_permission` VALUES ('5434', '1', '543', '启禁', 'backend', '', '/cms/catalog/status*', '', '', '4', '0', '1', '50', '1', '1', '1', '1', '1');
INSERT INTO `fb_base_permission` VALUES ('5435', '1', '543', '导出', 'backend', '', '/cms/catalog/export*', '', '', '4', '0', '1', '50', '1', '1', '1', '1', '1');
INSERT INTO `fb_base_permission` VALUES ('5436', '1', '543', '导入', 'backend', '', '/cms/catalog/import*', '', '', '4', '0', '1', '50', '1', '1', '1', '1', '1');

INSERT INTO `fb_base_setting_type` VALUES (54, 1, 0, 'backend', 'Cms网站', 'cms', '', 7, 4, 'text', '', '', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5411, 1, 54, 'backend', '主题', 'cms_theme', '', 7, 4, 'text', '', 'default', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5412, 1, 54, 'backend', '主页模板', 'cms_template', '', 7, 4, 'text', '', 'index', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5413, 1, 54, 'backend', '横幅', 'cms_banner', '', 7, 4, 'images', '', '', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5414, 1, 54, 'backend', '横幅手机版', 'cms_banner_h5', '', 7, 4, 'images', '', '', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5429, 1, 54, 'backend', '列表页默认每页数量', 'cms_list_page_size', '', 7, 4, 'text', '', '12', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5443, 1, 54, 'backend', '关于我们页面ID', 'cms_about_id', '', 7, 4, 'text', '', '', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5444, 1, 54, 'backend', '联系我们页面ID', 'cms_contact_id', '', 7, 4, 'text', '', '', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5451, 1, 54, 'backend', '网站参数1', 'cms_param1', '', 7, 4, 'text', '', '', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5452, 1, 54, 'backend', '网站参数2', 'cms_param2', '', 7, 4, 'text', '', '', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5453, 1, 54, 'backend', '网站参数3', 'cms_param3', '', 7, 4, 'text', '', '', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5454, 1, 54, 'backend', '网站参数4', 'cms_param4', '', 7, 4, 'text', '', '', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5455, 1, 54, 'backend', '网站参数5', 'cms_param5', '', 7, 4, 'text', '', '', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5456, 1, 54, 'backend', '网站参数6', 'cms_param6', '', 7, 4, 'text', '', '', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5457, 1, 54, 'backend', '网站参数7', 'cms_param7', '', 7, 4, 'textarea', '', '', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5458, 1, 54, 'backend', '网站参数8', 'cms_param8', '', 7, 4, 'textarea', '', '', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5459, 1, 54, 'backend', '网站参数9', 'cms_param9', '', 7, 4, 'textarea', '', '', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5461, 1, 54, 'backend', '侧图1', 'cms_image_side_01', '', 7, 4, 'image', '', '/resources/images/cms_image_side_01.jpg', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5462, 1, 54, 'backend', '侧图2', 'cms_image_side_02', '', 7, 4, 'image', '', '/resources/images/cms_image_side_02.jpg', 50, 1, 1600948360, 1600948360, 1, 1);
INSERT INTO `fb_base_setting_type` VALUES (5463, 1, 54, 'backend', '侧图3', 'cms_image_side_03', '', 7, 4, 'image', '', '/resources/images/cms_image_side_03.jpg', 50, 1, 1600948360, 1600948360, 1, 1);

INSERT INTO `fb_base_role_permission` VALUES ('321', '1', '', '50', '54', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('322', '1', '', '50', '541', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('323', '1', '', '50', '543', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('333', '1', '', '50', '5411', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('334', '1', '', '50', '5412', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('335', '1', '', '50', '5413', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('336', '1', '', '50', '5414', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('337', '1', '', '50', '5415', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('338', '1', '', '50', '5416', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('343', '1', '', '50', '5431', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('344', '1', '', '50', '5432', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('345', '1', '', '50', '5433', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('346', '1', '', '50', '5434', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('347', '1', '', '50', '5435', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('348', '1', '', '50', '5436', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('420', '1', '', '54', '5', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('421', '1', '', '54', '54', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('422', '1', '', '54', '541', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('423', '1', '', '54', '543', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('433', '1', '', '54', '5411', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('434', '1', '', '54', '5412', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('435', '1', '', '54', '5413', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('436', '1', '', '54', '5414', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('437', '1', '', '54', '5415', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('438', '1', '', '54', '5416', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('443', '1', '', '54', '5431', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('444', '1', '', '54', '5432', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('445', '1', '', '54', '5433', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('446', '1', '', '54', '5434', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('447', '1', '', '54', '5435', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('448', '1', '', '54', '5436', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('450', '1', '', '54', '56', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('451', '1', '', '54', '567', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('453', '1', '', '54', '5671', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('454', '1', '', '54', '5672', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('455', '1', '', '54', '5673', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('456', '1', '', '54', '5674', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('457', '1', '', '54', '5675', '1', '50', '1', '1602505044', '1606818825', '1', '1');
INSERT INTO `fb_base_role_permission` VALUES ('458', '1', '', '54', '5676', '1', '50', '1', '1602505044', '1606818825', '1', '1');

        ";

        //add user: admin  password: 123456
        $this->execute($sql);

    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
