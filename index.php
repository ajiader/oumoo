<?php 
/*
 * 这是一款基于ThinkPHP开发的轻量级CMS,高扩展,结构清晰!
 * 每类都处理者不同的功能,分布式写法
 * 全面采用面向对象写法,并遵守ThinkPHP的命名 
 * */

define('BASE_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/')));
define('APP_NAME', 'web');//项目名称  
define('APP_PATH','./web/');
define('APP_DEBUG',true); // 开启调试模式
require './thinkphp/thinkphp.php';

?>