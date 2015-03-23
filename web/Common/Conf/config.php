<?php
return array(
	'URL_CASE_INSENSITIVE' =>true,
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_HOST'   => 'localhost', // 服务器地址
// 	'DB_HOST'   => '61.153.109.189', // 服务器地址
	'DB_NAME'   => 'oumoo', // 数据库名
	'DB_USER'   => 'root', // 用户名
	'DB_PWD'    => 'root', // 密码
	'DB_PORT'   => 3306, // 端口
	'DB_PREFIX' => '', // 数据库表前缀
	//'SESSION_AUTO_START' => false,
	'VIEW_PATH'=>'./qiwen/',
	'TMPL_FILE_DEPR'=>'_',
	
	'LOAD_EXT_CONFIG' => 'class_name,front,model_exp', //加载分类模型配置   model_exp扩展属于model模型功能全站通用不包含该功能,扩展时注明扩展
	'URL_MODEL' => 2,
	'URL_HTML_SUFFIX'=>'html',
	'STR_YOU'		=> 'Thinkphp_Str_Model',
	'URL_ROUTER_ON'   => true,
	'URL_MAP_RULES'=>array(
	),
	'MODULE_ALLOW_LIST' => array (
		'Home',
		'Admin',
		'Verify',
	),
	'DEFAULT_MODULE' => 'Home',
	'URL_ROUTE_RULES' => array(
		'exp/:id'		=> 'Index/exp',
		'download/:id' => 'Index/download',
		'column/:id'	=> 'Index/column',
		'content/:id'	=> 'Index/content',
		'top/:id/:return'		=>	'Index/top',
		'search'		=> 'Index/search',
		'buy'			=> 'Buy/index',
		'login'			=> 'Member/login',
		'reg'			=> 'Member/reg',
	),
	'ERROR_PAGE' =>'/',
		
);


/**mysql表结构如下
 * 
 * 数据库名称：web_seo
 * 
 //创建数据库
create database qiwen DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

//创建管理员表
create table if not exists qiwen_admin(
	id int(11) primary key not  null  auto_increment,
	name varchar(255) not null unique,
	password varchar(255) not null,
	jurisdiction varchar(255) not null,
	primary `name` (`name`)
)

//创建会员表
create table if not exists qiwen_user(
	id int(11) primary key not  null  auto_increment,
	u_name varchar(255) unique not null,
	u_password varchar(255) not null,
	u_pic varchar(255) not null,
	money int(11) not null,
	mail varchar(255) not null,
	sex int(1) not null,
	qq int(11) not null,
	tel int(11) not null,
	reg_ip varchar(15) not null,
	reg_time int(11) not null,
	last_ip varchar(15) not null,
	last_time int(11) not null
)

//创建会员资金变动记录表
create table if not exists qiwen_user_msg(
	uid int(11) not  null,
	time int(11) not null,
	name varchar(255) not null,
	msg varchar(255) not null,
	key `uid` (`uid`)
)

//创建会员模型记录表,我购买的记录
create table if not exists qiwen_user_model(
	uid int(11) not  null,
	pid int(11) not null,
	time int(11) not null,
	key `uid` (`uid`)
)

//创建栏目表
create table if not exists qiwen_class_name(
	id int(11) primary key not  null  auto_increment,
	class_top int(11) not null,
	class_name varchar(255) not null,
	class_title varchar(255) not null,
	class_key varchar(255) not null,
	class_msg varchar(255) not null,
	class_type varchar(255) not null,
	class_pic varchar(255) not null,
	c_order int(11) not null,
	class_templet varchar(255) not null,
	class_style varchar(255) not null,
	class_url varchar(255) not null,
	KEY `c_order` (`c_order`)
)

//创建栏目与内容关系表
create table if not exists qiwen_msg_class(
	msg_id int(11) not  null,
	class_id int(11) not null,
	appoint int(1) not null,
	KEY `msg_id` (`msg_id`),
	KEY `class_id` (`class_id`),
	KEY `appoint` (`appoint`)
)

//创建内容主表 
create table if not exists qiwen_msg_main(
	id int(11) primary key not  null  auto_increment,
	uid int(11) not null,
	main_name varchar(255) not null,
	main_key varchar(255) not null,
	main_msg varchar(255) not null,
	main_time int(11) not null,
	main_pic varchar(255) not null,
	main_browse int(11) not null,
	main_text text not null,
	main_money int(11) not null,
	main_examine int(1) not null,
	KEY `main_examine` (`main_examine`)
)
//创建图片集表,该表直属内容主表(即所有栏目类型都可以调用)
create table if not exists qiwen_msg_main_pic(
	pid int(11) primary key not null,
	oid int(11) not null,
	pic varchar(255) not null
)

//创建模型内容附表--------以下是数据模型,每一个模型对应不同的数据表
create table if not exists qiwen_msg_main_model(
	pid int(11) primary key not null,
	main_nnnex varchar(255) not null,
	main_size varchar(255) not null,
	main_count varchar(255) not null,
	main_edition varchar(255) not null,
	main_down_number int(11) not null,
	main_style int(1) not null,
	KEY `main_style` (`main_style`)
)


//创建贴图内容附表
 create table if not exists qiwen_msg_main_chartlet(
	pid int(11) not null,
	main_nnnex varchar(255) not null,
	main_size varchar(255) not null,
	main_down_number int(11) not null,
    key `pid` (`pid`)
)
 
//创建模型开发表
 create table if not exists qiwen_msg_main_deve(
	pid int(11) not null,
	main_nnnex varchar(255) not null,
	main_nnnex1 varchar(255) not null,
	main_nnnex2 varchar(255) not null,
	main_deve_type int(1) not null,
	main_money1	int(11) not null,
	main_money2	int(11) not null,
	main_size varchar(255) not null,
	main_down_number int(11) not null,
    key `pid` (`pid`)
    <
		main_nnnex = 需要开发的模型结构/创意图	-可下载的
    	main_nnnex1 = 模型制作完成的				-可下载的
    	main_nnnex2 =  模型渲染后的				-不可下载的
    	main_deve_type = 开发进度!1=可申请开发，2=可申请渲染，3=正在开发，4=正在渲染，
    	5=等待审核(开发)，6=等待审核(渲染)，7=开发完成，-1表示失败的关闭的
    	
    	开发完毕将不可申请制作与渲染,同时开发完毕可不删除该内容!直接创建模型到其他栏目供会员下载
    	
    	main_money1 = 开发模型的工资(欧币)
    	main_money2 = 渲染模型的工资(欧币)
    >
) 
 
//创建申请模型开发表会员
create table if not exists qiwen_deve_user(
	pid int(11) not null,
	uid int(11) not null,
	type int(1) not null,
	examine int(1) not null,
	text varchar(255) not null
	<
		pid		=	模型开发id
		uid		=	会员id
		type	=	开发或渲染，1=开发模型,2=渲染模型
		examine	=	审核,1=通过,-1等于不通过
		text	=	通过与不通过的通知
	>
}

//创建人才求职附表
create table if not exists qiwen_msg_main_jobw(
	pid int(11) not null,
	job_name varchar(255),
	job_money int(11),
	job_tel int(11),
	job_address varchar(255),
	job_exp varchar(255)
)

//创建人才招聘附表
create table if not exists qiwen_msg_main_jobr(
	pid int(11) not null,
}
 * */