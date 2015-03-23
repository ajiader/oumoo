<?php
//实现一个功能即无需更改核心代码！只需加一个后台模版表单,出于功能需求该表单由程序员自定义
//再增加一张数据附表,一个栏目的功能完成!只需要前台调用即可。同时前台也无需更改核心代码
/*
 * model = 数据库模型(qiwen_msg_main_模型)
 * model = 后台添加内容模板(Msg_data_模型)
 * model =	前台首页默认		Index_index
 * 			前台列表页默认	Index_column
 * 			前台内容页默认	Index_content
 * 			前台扩展页默认	Index_exp(exp,exp1,exp2,..........)
 * 
 * 			出于1个栏目可能有多个风格,前台模版除默认外可以更改,需要在栏目指定......
 * 			后台模版没有更改的必要!遵守规则即可
 * 
 * 注:添加模型,一旦数据库插入和内容模板!该模型配置不可被更改,更该时应及时更新数据库与模板
 * 		后台内容模板由自己定义,无需更改核心内容即可完成想要的功能
 * 		建表必须按照规定,且表必须有唯一索引字段名为pid,且其他字段由自己定义!但不能出现data,main_*,id,class_id,pic的字段
 * */
return array(
		'CLASS_NAME' =>array(
				array(
						'model'	=> 'model',
						'name' 	=> '模型',
				),
				array(
						'model'	=> 'chartlet',
						'name' 	=> '贴图',
				),
				array(
						'model'	=> 'jobw',
						'name' 	=> '人才求职',
				),
				array(
						'model'	=> 'jobr',
						'name' 	=> '人才招聘(等待开发)',
				),
				array(
						'model'	=> 'deve',
						'name' 	=> '开发模型(等待开发)',
				),
				/*array(
						'model'	=> 'article',
						'name' 	=> '文章',
				),
				array(
						'model'	=> 'soft',
						'name' 	=> '软件',
				),*/
		),
);