<?php
namespace Home\Action;
use Think\Controller;
/*
 * 加入的功能全部命名为扩展功能,加入扩展功能应注明...
* 同时扩展功能不得影响CMS系统原有构架
* */
class UserAction extends Controller{
	public function _initialize(){
		if(!session('uid') && ACTION_NAME != 'login' && ACTION_NAME != 'reg'){
			$this->error("请先登录后操作",U("Member/login"));
			exit;
		}else if(session('uid') && (ACTION_NAME == 'login' || ACTION_NAME == 'reg')){
			$this->success("您已登录",U("Member/index"));
			exit;
		}
		$this->assign('column',get_all_column(-1));//不管浏览哪个页面,都需要知道顶级栏目
	}
	
}