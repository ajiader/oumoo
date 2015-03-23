<?php
/*
 * 本类调用后台招聘数据,后台数据更改应及时更新本类
 * 并非CMS自动加载等功能!此功能为会员功能!
 * */
namespace Home\Controller;
use Home\Action\UserAction;
class MoneyController extends UserAction {
	public function finance(){
		echo '当前功能正在开发中....';
		
	}

	public function finance_list(){
		$msg = M('user_msg');
		$where['uid'] = session('uid');
		$count      = $msg->where($where)->count();
		$Page       = new \Think\Page($count,10);
		$Page->rollPage = 5;
		$show       = $Page->show();
		$row = $msg->where($where)->order('time desc')->limit($Page->firstRow.','.$Page->listRows)->select();//链表查询内容
		$this->assign('web_data',$row);
		$this->assign('page',$show);
		$this->display();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}