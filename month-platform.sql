#create_time: 2017/9/1
#update_time: 2017/9/1 
#@author: winleung
#@email: 393857054@qq.com
#@project: month-platform
#@notice: 前期为初级布局, 中期请根据数据需求进行水平分表和索引优化.


USE `month-platform`;

DROP TABLE IF EXISTS `month_user`;
CREATE TABLE `month_user` (
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`nick_name` varchar(255) not null DEFAULT '' COMMENT '用户昵称',
	`real_name` varchar(255) not null DEFAULT '' COMMENT '用户真实姓名',
	`open_id` varchar(255) not null DEFAULT '' COMMENT '微信openID',
	`phone` bigint(13) unsigned not null DEFAULT 0 COMMENT '手机号',
	`password` varchar(255) not null DEFAULT '' COMMENT '密码',
	`head_url` varchar(255) not null DEFAULT '' COMMENT '用户头像',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	`sex` tinyint(1) unsigned not null DEFAULT 0 COMMENT '1:man,2:women',
	`city` int(10) unsigned not null DEFAULT 0,
	`brithday` bigint(13) unsigned not null DEFAULT 0 COMMENT '生日',
	`status` tinyint(1) not null DEFAULT 1 COMMENT '-1:删除,1:默认',
	KEY `status`(`status`),
	KEY `open_id`(`open_id`),
	KEY `phone`(`phone`),
	KEY `sex`(`sex`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '用户基础数据表';

DROP TABLE IF EXISTS `month_user_ready_pregnancy_info`;
CREATE TABLE `month_user_ready_pregnancy_info`(
	`user_id` int(10) unsigned not null DEFAULT 0 COMMENT '用户id',
	`last_menstruation_time` bigint(13) unsigned not null DEFAULT 0 COMMENT '最后的经期时间',
	`menstruation_time` tinyint(2) unsigned not null DEFAULT 0 COMMENT '经期天数(天)',
	`period` int(10) unsigned not null DEFAULT 0 COMMENT '周期(天)',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	KEY `user_id`(`user_id`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '用户备孕期数据表';

DROP TABLE IF EXISTS `month_user_pregnancy_info`;
CREATE TABLE `month_user_pregnancy_info`(
	`user_id` int(10) unsigned not null DEFAULT 0 COMMENT '用户id',
	`due_date` bigint(13) unsigned not null DEFAULT 0 COMMENT '预产期',
	`pregnancy_date` bigint(13) unsigned not null DEFAULT 0 COMMENT '怀孕日期',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	KEY `user_id`(`user_id`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '用户怀孕中数据表';

DROP TABLE IF EXISTS `month_user_after_pregnancy_info`;
CREATE TABLE `month_user_after_pregnancy_info`(
	`user_id` int(10) unsigned not null DEFAULT 0 COMMENT '用户id',
	`baby_sex` tinyint(1) unsigned not null DEFAULT 0 COMMENT 'bb性别',
	`baby_birthday` bigint(13) unsigned not null DEFAULT 0 COMMENT 'bb生日',
	`menstruation_time` tinyint(2) unsigned not null DEFAULT 0 COMMENT '经期天数(天)',
	`period` int(10) unsigned not null DEFAULT 0 COMMENT '周期(天)',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	KEY `user_id`(`user_id`),
	KEY `create_time`(`create_time`),
	KEY `baby_sex`(`baby_sex`),
	KEY `baby_birthday`(`baby_birthday`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '用户怀孕后数据表';

DROP TABLE IF EXISTS `month_user_detail_info`;
CREATE TABLE `month_user_detail_info`(
	`user_id` int(10) unsigned not null DEFAULT 0 COMMENT '用户id',
	`weight` decimal(5,2) unsigned not null DEFAULT 0.00 COMMENT '用户体重(kg)',
	`height` int(3) unsigned not null DEFAULT 0 COMMENT '身高(cm)',
	`last_login_time` bigint(13) unsigned not null DEFAULT 0 COMMENT '最后登录时间',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	KEY `user_id`(`user_id`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '用户详细数据表';

DROP TABLE IF EXISTS `month_user_visit_track`;
CREATE TABLE `month_user_visit_track`(
	`user_id` int(10) unsigned not null DEFAULT 0 COMMENT '用户id',
	`article_list` longtext not null DEFAULT '' COMMENT '文章足迹',
	`doctor_list` longtext not null DEFAULT '' COMMENT '医生足迹', 
	`tag_list` longtext not null DEFAULT '' COMMENT '标签足迹',
	`organization_list` longtext not null DEFAULT '' COMMENT '机构足迹',
	`service_list` longtext not null DEFAULT '' COMMENT '服务足迹',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	KEY `user_id`(`user_id`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '用户浏览足迹表';

DROP TABLE IF EXISTS `month_user_question_record`;
CREATE TABLE `month_user_question_record`(
	`user_id` int(10) unsigned not null DEFAULT 0 COMMENT '用户id',
	`question_title` longtext not null DEFAULT '' COMMENT '问题标题',
	`question_content` longtext not null DEFAULT '' COMMENT '问题内容',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	KEY `user_id`(`user_id`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '用户问题记录表';

DROP TABLE IF EXISTS `month_user_from_help`;
CREATE TABLE `month_user_from_help`(
	`user_id` int(10) unsigned not null DEFAULT 0 COMMENT '用户id',
	`doctor_id` int(10) not null DEFAULT 0 COMMENT '医生id',
	`score` tinyint(1) not null DEFAULT 5 COMMENT '评分(1-5)分, 默认5分',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	KEY `user_id`(`user_id`),
	KEY `create_time`(`create_time`),
	KEY `doctor_id`(`doctor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '用户寻求帮助记录表';

/*DROP TABLE IF EXISTS `month_doctor_department`;
CREATE TABLE `month_doctor_department`(
	`id` int(10) unsigned not null primary key AUTO_INCREMENT,
	`name` varchar(255) not null DEFAULT '' COMMENT '科室名称',
	`introduce` tinyint(1) unsigned not null DEFAULT 0 COMMENT '科室介绍',
	`tag_classify_id` text not null DEFAULT '' COMMENT '标签分类id',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '医生科室表';*/

DROP TABLE IF EXISTS `month_doctor_info`;
CREATE TABLE `month_doctor_info`(
	`id` int(10) unsigned not null primary key AUTO_INCREMENT,
	`user_name` varchar(255) not null DEFAULT '' COMMENT '帐号名称',
	`password` varchar(255) not null DEFAULT '' COMMENT '登录密码',
	`phone` bigint(13) not null DEFAULT 0 COMMENT '手机号码',
	`open_id` varchar(255) not null DEFAULT '' COMMENT '微信openID',
	`doctor_name` varchar(255) not null DEFAULT '' COMMENT '医生名称',
	`doctor_logo` varchar(255) not null DEFAULT '' COMMENT '医生头像',
	`sex` tinyint(1) unsigned not null DEFAULT 0 COMMENT '医生性别(1:男,2:女)',
	`tag_classify_id` int(10) unsigned not null DEFAULT 0 COMMENT '标签分类id',
	`organization_id` int(10) unsigned not null DEFAULT 0 COMMENT '所属机构id',
	`organization_name` varchar(255) not null DEFAULT '' COMMENT '所属机构名称',
	`organization_tel` varchar(255) not null DEFAULT '' COMMENT '所属机构电话号码',
	`job_title` varchar(255) not null DEFAULT '' COMMENT '职称',
	`tag_list` text not null DEFAULT '' COMMENT '标签列',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	`status` tinyint(1) not null DEFAULT 1 COMMENT '-1:删除,0:申请进驻,1:通过,2:拒绝',
	KEY `status`(`status`),
	KEY `organization_id`(`organization_id`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '医生信息表';

DROP TABLE IF EXISTS `month_tag_classify`;
CREATE TABLE `month_tag_classify`(
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`tag_classify_name` varchar(255)  not null DEFAULT '' COMMENT '标签分类名称',
	`tag_classify_type` tinyint(1) unsigned not null DEFAULT 1 COMMENT '标签分类类型(1:文章,2:专家)',
	`pid` int(10) unsigned not null DEFAULT 0 COMMENT '父级id(0为顶级id)',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	`status` tinyint(1) not null DEFAULT 1 COMMENT '-1:删除,1:默认',
	KEY `status`(`status`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '标签分类表';

DROP TABLE IF EXISTS `month_tag`;
CREATE TABLE `month_tag`(
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`tag_name` varchar(255) not null DEFAULT '' COMMENT '标签名称',
	`classify_id` int(10) not null DEFAULT 0 COMMENT '标签分类id',
	`classify_type` tinyint(1) not null DEFAULT 0 COMMENT '分类类型(1:文章, 2:医生)',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	`status` tinyint(1) not null DEFAULT 1 COMMENT '-1:删除,1:默认',
	KEY `status`(`status`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '标签表';

DROP TABLE IF EXISTS `month_article`;
CREATE TABLE `month_article`(
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`article_name` varchar(255) not null DEFAULT '' COMMENT '文章名称',
	`article_logo` text not null DEFAULT '' COMMENT '文章logo',
	`article_content` varchar(255) not null DEFAULT '' COMMENT '文章内容',
	`doctor_id` int(10) not null DEFAULT 0 COMMENT '0:平台发布, 其他:医生id',
	`tag_classify_id` int(10) unsigned not null DEFAULT 0 COMMENT '标签分类id',
	`tag_list` text not null DEFAULT '' COMMENT '标签列',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	`status` tinyint(1) not null DEFAULT 1 COMMENT '-1:删除,1:默认',
	KEY `status`(`status`),
	KEY `doctor_id`(`doctor_id`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '文章表'; 

DROP TABLE IF EXISTS `month_article_statis`;
CREATE TABLE `month_article_statis`(
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`relevance_id` int(10) unsigned not null DEFAULT 0,
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '文章统计表';

DROP TABLE IF EXISTS `month_question_article`;
CREATE TABLE `month_question_article`(
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`article_name` varchar(255) not null DEFAULT '' COMMENT '问题文章名称',
	`article_content` varchar(255) not null DEFAULT '' COMMENT '问题文章内容',
	`tag_classify_id` int(10) unsigned not null DEFAULT 0 COMMENT '标签分类id',
	`tag_list` text not null DEFAULT '' COMMENT '标签列',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	`status` tinyint(1) not null DEFAULT 1 COMMENT '-1:删除,1:默认',
	KEY `status`(`status`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '问题文章表';

DROP TABLE IF EXISTS `month_question_article_statis`;
CREATE TABLE `month_question_article_statis`(
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`relevance_id` int(10) unsigned not null DEFAULT 0,
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '问题文章统计表';

DROP TABLE IF EXISTS `month_banner`;
CREATE TABLE `month_banner`(
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`title` varchar(255) not null DEFAULT '' COMMENT '广告标题',
	`banner_logo` varchar(255) not null DEFAULT '' COMMENT '广告图片',
	`link` int(10) unsigned not null DEFAULT 0 COMMENT '广告链接',
	`tag_classify_id` int(10) unsigned not null DEFAULT 0 COMMENT '标签分类id',
	`tag_list` text not null DEFAULT '' COMMENT '标签列',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	`status` tinyint(1) not null DEFAULT 1 COMMENT '-1:删除,1:默认',
	KEY `status`(`status`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '广告表';

DROP TABLE IF EXISTS `month_banner_statis`;
CREATE TABLE `month_banner_statis`(
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`relevance_id` int(10) unsigned not null DEFAULT 0,
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '广告统计表';

DROP TABLE IF EXISTS `month_business`;
CREATE TABLE `month_business`(
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`name` varchar(255) not null DEFAULT '' COMMENT '行业名称',
	`description` varchar(255) not null DEFAULT '' COMMENT '行业描述',
	`district_id` varchar(255) not null DEFAULT '' COMMENT '区域id',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	`status` tinyint(1) not null DEFAULT 1 COMMENT '-1:删除,1:默认',
	KEY `status`(`status`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '行业表';

DROP TABLE IF EXISTS `month_business_statis`;
CREATE TABLE `month_business_statis`(
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`relevance_id` int(10) unsigned not null DEFAULT 0,
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '行业统计表';

DROP TABLE IF EXISTS `month_organization`;
CREATE TABLE `month_organization`(
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`organization_ip` varchar(255) not null DEFAULT '' COMMENT '机构平台IP号(唯一)',
	`organization_name` varchar(255) not null DEFAULT '' COMMENT '机构名称',
	`postfix` varchar(255) not null DEFAULT '' COMMENT '机构域名后缀',
	`address_info` varchar(255) not null DEFAULT '' COMMENT '地址数据',
	`business_id` int(10) not null DEFAULT 0 COMMENT '行业id',
	`x_point` decimal(9,6) not null DEFAULT 0 COMMENT '纬度',
	`y_point` decimal(9,6) not null DEFAULT 0 COMMENT '经度',
	`start_time` bigint(13) not null DEFAULT 0 COMMENT '开始使用时间',
	`end_time` bigint(13) not null DEFAULT 0 COMMENT '结束使用时间',
	`make_a_contract_time` bigint(13) not null DEFAULT 0 COMMENT '签约时间',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	`status` tinyint(1) not null DEFAULT 1 COMMENT '-1:删除,1:默认,2:禁用',
	UNIQUE INDEX `organization_ip`(`organization_ip`),
	KEY `status`(`status`),
	KEY `business_id`(`business_id`),
	KEY `postfix`(`postfix`),
	KEY `start_time`(`start_time`,`end_time`),
	KEY `x_point`(`x_point`,`y_point`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '机构基本信息表';

DROP TABLE IF EXISTS `month_organization_detail`;
CREATE TABLE `month_organization_detail`(
	`organization_id` int(10) NOT NULL,
	`description` longtext not null DEFAULT '' COMMENT '介绍',
	`business_license` varchar(255) not null DEFAULT '' COMMENT '营业执照',
	`person_code_front` varchar(255) not null DEFAULT '' COMMENT '身份证正面',
	`person_code_rear` varchar(255) not null DEFAULT '' COMMENT '身份证反面',
	`principal` varchar(255) not null DEFAULT '' COMMENT '负责人姓名',
	`principal_phone` bigint(13) unsigned not null DEFAULT 0 COMMENT '负责人电话',
	`principal_email` varchar(255) not null DEFAULT '' COMMENT '负责人邮箱',
	`business_id` varchar(255) not null DEFAULT '' COMMENT '行业id',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	UNIQUE INDEX `organization_id`(`organization_id`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '机构详细信息表';

DROP TABLE IF EXISTS `month_organization_statis`;
CREATE TABLE `month_organization_statis`(
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`relevance_id` int(10) unsigned not null DEFAULT 0,
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '机构统计表';

DROP TABLE IF EXISTS `month_organization_comment`;
CREATE TABLE `month_organization_comment`(
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`user_id` int(10) unsigned not null DEFAULT 0 COMMENT '用户id',
	`order_no` int(10) unsigned not null DEFAULT 0 COMMENT '订单号码',
	`organization_id` int(10) unsigned not null DEFAULT 0 COMMENT '机构id',
	`attitude_score` int(10) unsigned not null DEFAULT 0 COMMENT '态度评分',
	`totality_score` int(10) unsigned not null DEFAULT 0 COMMENT '总体评分',
	`show_pic` int(10) unsigned not null DEFAULT 0 COMMENT '发表图片(最多5张)',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	`status` tinyint(1) not null DEFAULT 0 COMMENT '-1:删除,0:默认,1:显示',
	KEY `status`(`status`),
	KEY `organization_id`(`organization_id`),
	KEY `user_id`(`user_id`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '机构评论表';

DROP TABLE IF EXISTS `month_organization_service`;
CREATE TABLE `month_organization_service`(
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`organization_id` int(10) unsigned not null DEFAULT 0 COMMENT '机构id',
	`logo` varchar(255) not null DEFAULT 0 COMMENT 'logo图片',
	`service_pic` int(10) unsigned not null DEFAULT 0 COMMENT '内容图片(最多5张)',
	`price` decimal(10,2) unsigned not null DEFAULT 0 COMMENT '服务原价',
	`discount_price` decimal(10,2) unsigned not null DEFAULT 0 COMMENT '折扣价',
	`detail` longtext not null DEFAULT '' COMMENT '详情',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	`status` tinyint(1) not null DEFAULT 0 COMMENT '-1:删除,0:默认,1:显示',
	KEY `status`(`status`),
	KEY `price`(`price`),
	KEY `discount_price`(`discount_price`),
	KEY `organization_id`(`organization_id`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '机构服务表';

DROP TABLE IF EXISTS `month_organization_service_statis`;
CREATE TABLE `month_organization_service_statis`(
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`relevance_id` int(10) unsigned not null DEFAULT 0,
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '机构服务统计表';

DROP TABLE IF EXISTS `month_subscribe_order`;
CREATE TABLE `month_subscribe_order`(
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`organization_id` int(10) unsigned not null DEFAULT 0,
	`order_no` int(10) unsigned not null DEFAULT 0,
	`user_id` int(10) unsigned not null DEFAULT 0,
	`come_time` bigint(13) unsigned not null DEFAULT 0,
	`is_complete` tinyint(1) unsigned not null DEFAULT 0 COMMENT '0:默认, 1:完成',
	`is_reset_come_time` tinyint(1) unsigned not null DEFAULT 0 COMMENT '0:未设置, 1:已设置过',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	`status` tinyint(1) not null DEFAULT 0 COMMENT '-1:删除,0:待服务,1:待评价,2:已完成',
	KEY `status`(`status`),
	KEY `user_id`(`user_id`),
	KEY `organization_id`(`organization_id`),
	UNIQUE INDEX `order_no`(`order_no`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '预约订单表';

DROP TABLE IF EXISTS `month_news_record`;
CREATE TABLE `month_news_record`(
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`send_id` int(10) unsigned not null DEFAULT 0,
	`send_type` tinyint(1) unsigned not null DEFAULT 0 COMMENT '1:用户, 2:医生',
	`receive_id` int(10) unsigned not null DEFAULT 0,
	`receive_type` tinyint(1) unsigned not null DEFAULT 0 COMMENT '1:用户, 2:医生',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	`status` tinyint(1) not null DEFAULT 0 COMMENT '-1:删除,0:未读,1:已读',
	KEY `status`(`status`),
	KEY `send_id`(`send_id`,`send_type`),
	KEY `receive_id`(`receive_id`,`receive_type`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '消息记录表';

DROP TABLE IF EXISTS `month_admin`;
CREATE TABLE `month_admin` (
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`admin_name` varchar(255) not null DEFAULT '' COMMENT '管理员名称(账户名)',
	`password` varchar(255) not null DEFAULT '' COMMENT '密码',
	`phone` bigint(13) unsigned not null DEFAULT 0 COMMENT '手机号',
	`head_url` varchar(255) not null DEFAULT '' COMMENT '管理员头像',
	`sex` tinyint(1) unsigned not null DEFAULT 0 COMMENT '1:man,2:women',
	`is_super` tinyint(1) unsigned not null DEFAULT 0 COMMENT '0:默认, 1:超级管理员',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	`status` tinyint(1) not null DEFAULT 1 COMMENT '-1:删除,1:默认',
	KEY `status`(`status`),
	KEY `is_super`(`is_super`),
	KEY `admin_name`(`admin_name`),
	KEY `phone`(`phone`),
	KEY `sex`(`sex`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '管理员表';

DROP TABLE IF EXISTS `month_rule`;
CREATE TABLE `month_rule` (
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`controller` varchar(255) not null DEFAULT '' COMMENT '控制器权限',
	`method` varchar(255) not null DEFAULT '' COMMENT '方法权限',
	`title` varchar(255) not null DEFAULT '' COMMENT '权限名称',
	`is_display` tinyint(1) not null DEFAULT 0 COMMENT '0:默认, 1:显示',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	`status` tinyint(1) not null DEFAULT 1 COMMENT '-1:删除,1:默认',
	KEY `status`(`status`),
	KEY `controller`(`controller`),
	UNIQUE INDEX `method`(`method`),
	KEY `is_display`(`is_display`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '权限表';

DROP TABLE IF EXISTS `month_rule_group`;
CREATE TABLE `month_rule_group` (
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`title` varchar(255) not null DEFAULT '' COMMENT '分组名称',
	`rule_list` longtext not null DEFAULT '' COMMENT '权限列',
	`is_super` tinyint(1) unsigned not null DEFAULT 0 COMMENT '0:默认, 1:超级管理员',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	`status` tinyint(1) not null DEFAULT 1 COMMENT '-1:删除,1:默认',
	KEY `status`(`status`),
	KEY `create_time`(`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '权限组表';

DROP TABLE IF EXISTS `month_group_access`;
CREATE TABLE `month_group_access` (
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`admin_id` int(10) not null DEFAULT 0 COMMENT '管理员id',
	`group_id` varchar(255) not null DEFAULT '' COMMENT '权限组id',
	`is_super` varchar(255) not null DEFAULT '' COMMENT '0:默认, 1:超级管理员',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	KEY `admin_id`(`admin_id`),
	KEY `is_super`(`is_super`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '权限组表';

DROP TABLE IF EXISTS `month_announcement`;
CREATE TABLE `month_group_access` (
	`id` int(10) unsigned NOT NULL primary key AUTO_INCREMENT,
	`title` varchar(255) not null DEFAULT 0 COMMENT '公告标题',
	`content` text not null DEFAULT '' COMMENT '公告内容',
	`receiver_type` tinyint(1) not null DEFAULT 1 COMMENT '接收者类型只能为(1:用户,2:专家)',
	`receiver_id` int(11) not null DEFAULT 0 COMMENT '接受者id',
	`announcement_id` int(11) not null DEFAULT 1 COMMENT '公告id'，
	`news_type`	tinyint(1) not null DEFAULT 1 COMMENT '消息类型(1-普通公告，2-订单消息'），
	`status` tinyint(1) not null DEFAULT 0 COMMENT '-1-del,0-default',
	`user_status` tinyint(1) not null DEFAULT 0 COMMENT '-1-del,0-default,1-is_read',
	`create_time` bigint(13) unsigned not null DEFAULT 0,
	`update_time` bigint(13) unsigned not null DEFAULT 0,
	KEY `admin_id`(`admin_id`),
	KEY `is_super`(`is_super`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '公告表';

