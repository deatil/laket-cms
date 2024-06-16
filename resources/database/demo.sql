DROP TABLE IF EXISTS `pre__cms_ext_case`;
CREATE TABLE `pre__cms_ext_case` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `content` varchar(250) NOT NULL COMMENT '案例详情',
  `views` varchar(10) DEFAULT NULL COMMENT '浏览量',
  `cover` varchar(32) NOT NULL COMMENT '封面',
  `description` varchar(250) DEFAULT NULL COMMENT '描述',
  `keywords` varchar(200) DEFAULT NULL COMMENT '关键字',
  `title` varchar(250) NOT NULL COMMENT '案列标题',
  `categoryid` varchar(10) NOT NULL COMMENT '所属栏目ID，与栏目关联',
  `status` varchar(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `edit_time` varchar(10) DEFAULT '0' COMMENT '更新时间',
  `edit_ip` varchar(50) DEFAULT NULL COMMENT '更新IP',
  `add_time` varchar(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) DEFAULT NULL COMMENT '添加IP',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='案列';

DROP TABLE IF EXISTS `pre__cms_ext_news`;
CREATE TABLE `pre__cms_ext_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cover` varchar(32) NOT NULL COMMENT '资讯封面',
  `froms` varchar(5) DEFAULT NULL COMMENT '来源',
  `type` varchar(200) DEFAULT NULL COMMENT '类型',
  `views` varchar(10) NOT NULL DEFAULT '0' COMMENT '浏览量',
  `tags` varchar(250) DEFAULT NULL COMMENT '标签',
  `content` varchar(250) NOT NULL COMMENT '内容',
  `description` varchar(250) NOT NULL COMMENT '描述',
  `keywords` varchar(250) DEFAULT NULL COMMENT '关键字',
  `title` varchar(200) NOT NULL COMMENT '标题',
  `categoryid` varchar(10) NOT NULL COMMENT '所属栏目ID，与栏目关联',
  `status` varchar(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `edit_time` varchar(10) DEFAULT '0' COMMENT '更新时间',
  `edit_ip` varchar(50) DEFAULT NULL COMMENT '更新IP',
  `add_time` varchar(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) DEFAULT NULL COMMENT '添加IP',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='资讯';

DROP TABLE IF EXISTS `pre__cms_ext_page`;
CREATE TABLE `pre__cms_ext_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `content` varchar(250) DEFAULT NULL COMMENT '公司详情',
  `views` varchar(10) DEFAULT '0' COMMENT '浏览量',
  `description` varchar(250) DEFAULT NULL COMMENT '描述',
  `keywords` varchar(250) DEFAULT NULL COMMENT '关键字',
  `title` varchar(250) DEFAULT '' COMMENT '公司名称',
  `categoryid` varchar(10) NOT NULL COMMENT '所属栏目ID，与栏目关联',
  `status` varchar(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `edit_time` varchar(10) DEFAULT '0' COMMENT '更新时间',
  `edit_ip` varchar(50) DEFAULT NULL COMMENT '更新IP',
  `add_time` varchar(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) DEFAULT NULL COMMENT '添加IP',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='单页';

DROP TABLE IF EXISTS `pre__cms_ext_product`;
CREATE TABLE `pre__cms_ext_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` varchar(250) DEFAULT NULL COMMENT '产品相关类型',
  `views` varchar(10) DEFAULT '0' COMMENT '浏览量',
  `content` varchar(250) NOT NULL COMMENT '产品详情',
  `description` varchar(250) DEFAULT NULL COMMENT '描述',
  `keywords` varchar(200) DEFAULT NULL COMMENT '关键字',
  `cover` varchar(32) NOT NULL COMMENT '封面',
  `title` varchar(250) NOT NULL COMMENT '产品名称',
  `categoryid` varchar(10) NOT NULL COMMENT '所属栏目ID，与栏目关联',
  `status` varchar(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `edit_time` varchar(10) DEFAULT '0' COMMENT '更新时间',
  `edit_ip` varchar(50) DEFAULT NULL COMMENT '更新IP',
  `add_time` varchar(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) DEFAULT NULL COMMENT '添加IP',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='产品';


REPLACE INTO `pre__cms_category` (`id`,`parentid`,`modelid`,`name`,`title`,`keywords`,`description`,`cover`,`type`,`template_list`,`template_detail`,`template_page`,`index_url`,`content_url`,`order_list`,`list_grid`,`pagesize`,`sort`,`status`,`edit_time`,`edit_ip`,`add_time`,`add_ip`,`is_inchildren`) VALUES (6,0,10,'news','行业动态','行业动态','行业动态','',1,'list_news.html','detail_news.html','','','','','title:标题',10,102,1,1611033388,'127.0.0.1',1611033388,'127.0.0.1',0),(7,0,10,'company','公司动态','公司动态','公司动态','',1,'list_news.html','detail_news.html','','','','','title:标题',10,105,1,1611033459,'127.0.0.1',1611033459,'127.0.0.1',0),(8,0,5,'about','关于我们','关于我们','关于我们','',2,'','','page_about.html','','','','',10,107,1,1612275837,'127.0.0.1',1612275837,'127.0.0.1',1),(9,0,5,'contact','联系我们','联系我们','联系我们','',2,'','','page_contact.html','','','','',10,101,1,1612277435,'127.0.0.1',1612277435,'127.0.0.1',0),(10,0,11,'product','产品中心','产品中心','产品中心','03d7e972592bf4708b218e6dc7f20e01',1,'list_product.html','detail_product.html','','','','','title:标题',10,106,1,1612279679,'127.0.0.1',1612279679,'127.0.0.1',1),(11,0,12,'case','精品案例','精品案例','精品案例','',1,'list_case.html','detail_case.html','','','','','title:标题',10,103,1,1612279705,'127.0.0.1',1612279705,'127.0.0.1',0),(12,10,11,'kongtiao','空调系列','空调系列','空调系列','',1,'list_product.html','detail_product.html','','','','','title:标题',10,100,1,1612327092,'127.0.0.1',1612327092,'127.0.0.1',0),(13,10,11,'binxiang','冰箱系列','冰箱系列','冰箱系列','',1,'list_product.html','detail_product.html','','','','','title:标题',10,105,1,1612327122,'127.0.0.1',1612327122,'127.0.0.1',0);
REPLACE INTO `pre__cms_model` (`id`,`title`,`tablename`,`comment`,`sort`,`status`,`edit_time`,`edit_ip`,`add_time`,`add_ip`) VALUES (5,'单页','page','单页',100,1,1610635100,'127.0.0.1',1610635100,'127.0.0.1'),(10,'资讯','news','资讯',105,1,1610946261,'127.0.0.1',1610946261,'127.0.0.1'),(11,'产品','product','产品',110,1,1612278281,'127.0.0.1',1612278281,'127.0.0.1'),(12,'案列','case','案列',115,1,1612278299,'127.0.0.1',1612278299,'127.0.0.1');
REPLACE INTO `pre__cms_model_field` (`id`,`modelid`,`name`,`title`,`length`,`type`,`options`,`value`,`remark`,`validate_rule`,`validate_message`,`validate_time`,`show_type`,`is_filter`,`is_must`,`is_show`,`is_list_show`,`is_detail_show`,`is_view`,`sort`,`status`,`edit_time`,`edit_ip`,`add_time`,`add_ip`) VALUES (16,5,'categoryid','所属栏目','10','number','','','所属栏目ID，与栏目关联','','','create',0,0,0,0,0,0,0,199,1,1610635100,'127.0.0.1',1610635100,'127.0.0.1'),(17,5,'status','状态','1','switch','','1','状态','','','create',1,0,0,0,0,0,0,100,1,1610635100,'127.0.0.1',1610635100,'127.0.0.1'),(18,5,'edit_time','更新时间','10','datetime','','0','更新时间','','','create',0,0,0,0,0,0,0,100,1,1610635100,'127.0.0.1',1610635100,'127.0.0.1'),(19,5,'edit_ip','更新IP','50','string','','','更新IP',NULL,NULL,'create',1,0,0,0,0,0,0,100,1,1610635100,'127.0.0.1',1610635100,'127.0.0.1'),(20,5,'add_time','添加时间','10','datetime','','0','添加时间','','','create',0,0,0,0,0,0,0,100,1,1610635100,'127.0.0.1',1610635100,'127.0.0.1'),(21,5,'add_ip','添加IP','50','string','','','添加IP',NULL,NULL,'create',1,0,0,0,0,0,0,100,1,1610635100,'127.0.0.1',1610635100,'127.0.0.1'),(34,10,'categoryid','所属栏目','10','number','','','所属栏目ID，与栏目关联','','','create',0,0,0,0,0,0,0,199,1,1610946261,'127.0.0.1',1610946261,'127.0.0.1'),(35,10,'status','状态','1','switch','','1','状态',NULL,NULL,'create',1,0,1,0,0,0,0,100,1,1610946261,'127.0.0.1',1610946261,'127.0.0.1'),(36,10,'edit_time','更新时间','10','datetime','','0','更新时间','','','create',0,0,0,0,0,0,0,100,1,1610946261,'127.0.0.1',1610946261,'127.0.0.1'),(37,10,'edit_ip','更新IP','50','text','','','更新IP','','','create',0,0,0,0,0,0,0,100,1,1610946261,'127.0.0.1',1610946261,'127.0.0.1'),(38,10,'add_time','添加时间','10','datetime','','0','添加时间','','','create',0,0,0,0,0,0,0,100,1,1610946261,'127.0.0.1',1610946261,'127.0.0.1'),(39,10,'add_ip','添加IP','50','text','','','添加IP','','','create',0,0,0,0,0,0,0,100,1,1610946261,'127.0.0.1',1610946261,'127.0.0.1'),(40,10,'title','标题','200','text','','','标题','','标题不能为空','always',1,1,1,1,1,1,0,200,1,1610946553,'127.0.0.1',1610946553,'127.0.0.1'),(41,10,'keywords','关键字','250','text','','','','','','always',1,0,0,0,0,1,0,197,1,1610946602,'127.0.0.1',1610946602,'127.0.0.1'),(42,10,'description','描述','250','textarea','','','','','','always',1,0,0,0,0,1,0,196,1,1610946652,'127.0.0.1',1610946652,'127.0.0.1'),(43,10,'content','内容','250','editor','','','','','','always',1,0,1,0,0,1,0,198,1,1610946797,'127.0.0.1',1610946797,'127.0.0.1'),(44,10,'tags','标签','250','tags','','','标签以半角逗号间隔','','','always',1,0,0,0,1,1,0,195,1,1610947145,'127.0.0.1',1610947145,'127.0.0.1'),(45,5,'title','公司名称','250','text','','','','','','always',1,0,1,0,1,1,0,200,1,1610948116,'127.0.0.1',1610948116,'127.0.0.1'),(46,10,'views','浏览量','10','number','','0','','','','always',0,0,1,1,1,1,1,192,1,1610948166,'127.0.0.1',1610948166,'127.0.0.1'),(47,5,'keywords','关键字','250','text','','','','','','always',1,1,0,0,0,1,0,198,1,1610948235,'127.0.0.1',1610948235,'127.0.0.1'),(48,5,'description','描述','250','textarea','','','','','','always',1,0,0,0,0,1,0,197,1,1610948278,'127.0.0.1',1610948278,'127.0.0.1'),(49,5,'views','浏览量','10','number','','0','','','','always',0,0,0,0,1,1,1,195,1,1610948318,'127.0.0.1',1610948318,'127.0.0.1'),(50,5,'content','公司详情','250','editor','','','公司详情','','','always',1,1,1,0,0,1,0,196,1,1610948384,'127.0.0.1',1610948384,'127.0.0.1'),(51,10,'type','类型','200','checkbox','hot:热门\r\nrecommend:推荐\r\nnew:最新','','类型','','','always',1,0,0,1,1,1,0,194,1,1611206772,'127.0.0.1',1611206772,'127.0.0.1'),(53,10,'froms','来源','100','select','web:转载\r\nperson:原创','','','','','always',1,0,0,0,1,1,0,191,1,1611208153,'127.0.0.1',1611208153,'127.0.0.1'),(54,10,'cover','封面','32','image','','','资讯封面','','','always',1,0,0,0,1,1,0,193,1,1611243868,'127.0.0.1',1611243868,'127.0.0.1'),(55,11,'categoryid','所属栏目','10','number','','','所属栏目ID，与栏目关联',NULL,NULL,'create',4,0,1,0,0,0,0,250,1,1612278281,'127.0.0.1',1612278281,'127.0.0.1'),(56,11,'status','状态','1','switch','','1','状态',NULL,NULL,'create',4,0,1,0,0,0,0,100,1,1612278281,'127.0.0.1',1612278281,'127.0.0.1'),(57,11,'edit_time','更新时间','10','datetime','','0','更新时间',NULL,NULL,'create',4,0,0,0,0,0,0,100,1,1612278281,'127.0.0.1',1612278281,'127.0.0.1'),(58,11,'edit_ip','更新IP','50','text','','','更新IP',NULL,NULL,'create',4,0,0,0,0,0,0,100,1,1612278281,'127.0.0.1',1612278281,'127.0.0.1'),(59,11,'add_time','添加时间','10','datetime','','0','添加时间',NULL,NULL,'create',4,0,0,0,0,0,0,100,1,1612278281,'127.0.0.1',1612278281,'127.0.0.1'),(60,11,'add_ip','添加IP','50','text','','','添加IP',NULL,NULL,'create',4,0,0,0,0,0,0,100,1,1612278281,'127.0.0.1',1612278281,'127.0.0.1'),(61,12,'categoryid','所属栏目','10','number','','','所属栏目ID，与栏目关联',NULL,NULL,'create',4,0,1,0,0,0,0,200,1,1612278299,'127.0.0.1',1612278299,'127.0.0.1'),(62,12,'status','状态','1','switch','','1','状态','','','create',1,0,1,0,1,1,0,100,1,1612278299,'127.0.0.1',1612278299,'127.0.0.1'),(63,12,'edit_time','更新时间','10','datetime','','0','更新时间','','','create',0,0,0,0,0,0,0,100,1,1612278299,'127.0.0.1',1612278299,'127.0.0.1'),(64,12,'edit_ip','更新IP','50','text','','','更新IP',NULL,NULL,'create',4,0,0,0,0,0,0,100,1,1612278299,'127.0.0.1',1612278299,'127.0.0.1'),(65,12,'add_time','添加时间','10','datetime','','0','添加时间','','','create',0,0,0,0,0,0,0,100,1,1612278299,'127.0.0.1',1612278299,'127.0.0.1'),(66,12,'add_ip','添加IP','50','text','','','添加IP',NULL,NULL,'create',4,0,0,0,0,0,0,100,1,1612278299,'127.0.0.1',1612278299,'127.0.0.1'),(67,11,'title','产品名称','250','text','','','产品名称','','','always',1,0,1,1,1,1,0,249,1,1612278430,'127.0.0.1',1612278430,'127.0.0.1'),(68,11,'cover','封面','32','image','','','封面','','','always',1,0,1,1,1,1,0,245,1,1612278482,'127.0.0.1',1612278482,'127.0.0.1'),(69,11,'keywords','关键字','200','text','','','','','','always',1,0,0,1,1,0,0,247,1,1612278529,'127.0.0.1',1612278529,'127.0.0.1'),(70,11,'description','描述','250','textarea','','','','','','always',1,0,0,1,1,1,0,246,1,1612278560,'127.0.0.1',1612278560,'127.0.0.1'),(71,11,'content','产品详情','250','editor','','','产品详情','','','always',1,0,1,1,1,1,0,248,1,1612278608,'127.0.0.1',1612278608,'127.0.0.1'),(72,11,'views','浏览量','10','number','','0','浏览量','','','always',0,0,0,1,1,1,1,242,1,1612278640,'127.0.0.1',1612278640,'127.0.0.1'),(73,12,'title','案列标题','250','text','','','案列标题','','','always',1,1,1,1,1,1,0,199,1,1612279049,'127.0.0.1',1612279049,'127.0.0.1'),(74,12,'keywords','关键字','200','text','','','关键字','','','always',1,0,0,1,1,1,0,197,1,1612279090,'127.0.0.1',1612279090,'127.0.0.1'),(75,12,'description','描述','250','textarea','','','描述','','','always',1,0,0,1,1,1,0,196,1,1612279119,'127.0.0.1',1612279119,'127.0.0.1'),(76,12,'cover','封面','32','image','','','封面','','','always',1,0,1,1,1,1,0,195,1,1612279155,'127.0.0.1',1612279155,'127.0.0.1'),(78,12,'views','浏览量','10','number','','','浏览量','','','always',0,0,0,1,1,1,1,193,1,1612279213,'127.0.0.1',1612279213,'127.0.0.1'),(81,12,'content','案例详情','250','editor','','','','','','always',1,1,1,1,1,1,0,198,1,1612279534,'127.0.0.1',1612279534,'127.0.0.1'),(82,11,'type','类型','250','checkbox','hot:热销推荐\r\nnew:新品推荐','','产品相关类型','','','always',1,0,0,1,1,1,0,243,1,1612329313,'127.0.0.1',1612329313,'127.0.0.1');
REPLACE INTO `pre__cms_navbar` (`id`,`parentid`,`title`,`url`,`description`,`target`,`sort`,`status`,`edit_time`,`edit_ip`,`add_time`,`add_ip`) VALUES (2,0,'产品中心','/cms/cate/product','','_self',106,1,1610944856,'127.0.0.1',1610944856,'127.0.0.1'),(3,0,'公司动态','/cms/cate/company','','_self',105,1,1610944894,'127.0.0.1',1610944894,'127.0.0.1'),(5,0,'行业动态','/cms/cate/news','','_self',102,1,1610945605,'127.0.0.1',1610945605,'127.0.0.1'),(6,0,'关于我们','/cms/page/about','','_self',107,1,1610945626,'127.0.0.1',1610945626,'127.0.0.1'),(7,0,'精品案列','/cms/cate/case','','_self',103,1,1612327691,'127.0.0.1',1612327691,'127.0.0.1'),(8,0,'联系我们','/cms/page/contact','','_self',101,1,1612327834,'127.0.0.1',1612327834,'127.0.0.1');
REPLACE INTO `pre__cms_tags` (`id`,`name`,`title`,`keywords`,`description`,`views`,`sort`,`status`,`edit_time`,`edit_ip`,`add_time`,`add_ip`) VALUES (1,'','关键字',NULL,NULL,8,100,1,1611160429,'127.0.0.1',1611160429,'127.0.0.1'),(2,'20210121004608','测试',NULL,NULL,8,100,1,1611161168,'127.0.0.1',1611161168,'127.0.0.1'),(3,'20210121130258','公司动态',NULL,NULL,8,100,1,1611205378,'127.0.0.1',1611205378,'127.0.0.1'),(4,'20210121130957','添加',NULL,NULL,8,100,1,1611205797,'127.0.0.1',1611205797,'127.0.0.1'),(5,'20210121132202','测试22',NULL,NULL,8,100,1,1611206522,'127.0.0.1',1611206522,'127.0.0.1'),(6,'ceshi33','测试33',NULL,NULL,8,100,1,1611210111,'127.0.0.1',1611210111,'127.0.0.1'),(7,'xingyedongtai','行业动态',NULL,NULL,0,100,1,1612367462,'127.0.0.1',1612367462,'127.0.0.1');
REPLACE INTO `pre__cms_tags_content` (`tagid`,`modelid`,`cateid`,`contentid`,`add_time`,`add_ip`) VALUES (2,10,6,5,1611206454,'127.0.0.1'),(1,10,6,5,1611206454,'127.0.0.1'),(4,10,7,3,1612412417,'127.0.0.1'),(3,10,7,3,1612412417,'127.0.0.1'),(6,10,6,6,1612276024,'127.0.0.1'),(5,10,6,6,1612276024,'127.0.0.1'),(7,10,6,7,1612367462,'127.0.0.1'),(3,10,7,8,1612367537,'127.0.0.1');

REPLACE INTO `pre__cms_ext_case` (`id`,`content`,`views`,`cover`,`description`,`keywords`,`title`,`categoryid`,`status`,`edit_time`,`edit_ip`,`add_time`,`add_ip`) VALUES (1,'&lt;p&gt;新材料案例&lt;/p&gt;','5','9db6c1602930ece6c105e5fc719b95fc','新材料案例','新材料案例 ','新材料案例','11','1','1612364596','127.0.0.1','1612364596','127.0.0.1'),(2,'&lt;p&gt;新材料案例&lt;/p&gt;',NULL,'9db6c1602930ece6c105e5fc719b95fc','新材料案例','新材料案例','新材料案例2','11','1','1612365819','127.0.0.1','1612364706','127.0.0.1'),(3,'&lt;p&gt;新材料案例&lt;/p&gt;',NULL,'9db6c1602930ece6c105e5fc719b95fc','新材料案例','新材料案例','新材料案例3','11','1','1612365813','127.0.0.1','1612364723','127.0.0.1'),(4,'&lt;p&gt;新材料案例&lt;/p&gt;',NULL,'9db6c1602930ece6c105e5fc719b95fc','新材料案例','新材料案例','新材料案例5','11','1','1612365803','127.0.0.1','1612364738','127.0.0.1'),(5,'&lt;p&gt;新材料案例&lt;/p&gt;',NULL,'9db6c1602930ece6c105e5fc719b95fc','新材料案例','新材料案例','新材料案例6','11','1','1612365795','127.0.0.1','1612364753','127.0.0.1');
REPLACE INTO `pre__cms_ext_news` (`id`,`cover`,`froms`,`type`,`views`,`tags`,`content`,`description`,`keywords`,`title`,`categoryid`,`status`,`edit_time`,`edit_ip`,`add_time`,`add_ip`) VALUES (3,'a5026a27086e1c3933f55d186f24ae00','','new','37','公司动态,添加','&lt;p&gt;公司动态&lt;/p&gt;','公司动态','公司动态','公司动态','7','1','1612412417','127.0.0.1','1611034281','127.0.0.1'),(5,'',NULL,NULL,'1','关键字,测试','&lt;p&gt;关键字&lt;/p&gt;','关键字','关键字','关键字','6','1','1611206454','127.0.0.1','1611121715','127.0.0.1'),(6,'b7daae912526add9c6929568ec929313','web','recommend,new','22','测试22,测试33','&lt;p&gt;测试22&lt;/p&gt;','测试22','测试22','测试22','6','1','1612276024','127.0.0.1','1611206522','127.0.0.1'),(7,'a5026a27086e1c3933f55d186f24ae00','','recommend','4','行业动态','&lt;p&gt;行业动态&lt;/p&gt;','行业动态','行业动态','行业动态','6','1','1612367462','127.0.0.1','1612367462','127.0.0.1'),(8,'','',NULL,'19','公司动态','&lt;p&gt;公司动态1&lt;/p&gt;','公司动态1','公司动态1','公司动态1','7','1','1612367537','127.0.0.1','1612367537','127.0.0.1');
REPLACE INTO `pre__cms_ext_page` (`id`,`content`,`views`,`description`,`keywords`,`title`,`categoryid`,`status`,`edit_time`,`edit_ip`,`add_time`,`add_ip`) VALUES (2,'&lt;p&gt;关于我们&lt;/p&gt;','15','关于我们','关于我们','关于我们','8','1','1612280756','127.0.0.1','1612276075','127.0.0.1'),(3,'&lt;p&gt;联系我们&lt;/p&gt;','30','联系我们','联系我们','联系我们','9','1','1612277667','127.0.0.1','1612277644','127.0.0.1');
REPLACE INTO `pre__cms_ext_product` (`id`,`type`,`views`,`content`,`description`,`keywords`,`cover`,`title`,`categoryid`,`status`,`edit_time`,`edit_ip`,`add_time`,`add_ip`) VALUES (2,'hot','5','&lt;p&gt;产品1&lt;/p&gt;','产品1','产品1','f63c7f8accfd25b153b49e9eafd424a6','产品1','12','1','1612365905','127.0.0.1','1612365905','127.0.0.1'),(3,'new','3','&lt;p&gt;产品12&lt;/p&gt;','产品12','产品12','f63c7f8accfd25b153b49e9eafd424a6','产品12','12','1','1612365930','127.0.0.1','1612365930','127.0.0.1'),(4,'hot','8','&lt;p&gt;产品123&lt;/p&gt;','产品123','产品123','f63c7f8accfd25b153b49e9eafd424a6','产品123','13','1','1612365947','127.0.0.1','1612365947','127.0.0.1');