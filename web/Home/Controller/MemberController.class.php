<?php
namespace Home\Controller;
use Home\Action\UserAction;
class MemberController extends UserAction {
	public function login(){
		if(IS_POST){
			// if(!check_verify(I('post.Verify'))){
			// 	$this->error("验证码错误!");//跳转到登录页面
			// 	exit;
			// };
			
			if(!I('param.name') || !I('param.password')){
				$this->error('帐号或密码错误!请重试...');
			}
			
			$User = M('user');
			$row = $User->where(array('u_name'=>I('param.name')))->find();	
			if(!$row){
				$this->error('用户名不存在,请核对后登录...');
			}		
			if($row['u_password'] !== get_md5_password(I('param.password'))){
				$this->error('密码错误,登录失败...');
			}
			
			session('uid',$row['id']);
			unset($row['u_password']);
			session('user',$row);
			$this->success('登录成功...',I('get.url') ? I('get.url') : U("Member/index"));
			exit;
		}
		$head['title'] = '登录';
		$this->assign('head',$head);
		$this->display();
	}
	
	public function reg(){
		if(IS_POST){
			// if(!check_verify(I('post.Verify'))){
			// 	$this->error("验证码错误!");
			// 	exit;
			// };
			$data = I('post.');
			if (empty($data['mail']) || !ereg("^[-a-zA-Z0-9_\.]+\@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,5}$",$data['mail'])){
				$this->error('邮箱格式不正确...');				
			}
			if(strlen($data['u_name']) < 6 || strlen($data['u_password']) < 6){
				$this->error('用户名或密码不得小于6位...');				
			}
			if($data['u_password'] != $data['r_password']){
				$this->error('两次密码输入的不正确....');				
			}
			$User = M('user');
			$row = $User->where(array('u_name'=>$data['u_name'],'mail'=>$data['mail'],'_logic'=>'or'))->find();//注册成功,立即登录
			if($row){
				$this->error('邮箱或用户名已存在....');//登录失败....基本不可能哟
			}
			
			unset($data['r_password']);
			$data['u_password'] = get_md5_password($data['u_password']);
			$row = $User->Add($data);
			if(!$row){
				$this->error('注册失败,请重试....');				
			}
			$row = $User->where(array('id'=>$row))->find();//注册成功,立即登录
			if(!$row){
				$this->error('注册失败,请重试....');//登录失败....基本不可能哟
			}
			session('uid',$row['id']);
			unset($row['u_password']);
			session('user',$row);
			$this->success('注册成功....',UU('member'));
			exit;
		}
		$head['title'] = '注册';
		$this->assign('head',$head);
		$this->display();
	}
	
	public function user_out(){
		session('uid',null);
		session('user',null);
		$this->success('退出成功...',UU("index"));
	}
	
	/*以上则是会员登录,注册功能。该类除以上方法,以下方法均需要登陆后才有权限操作*/
	/*以下则是会员中心数据。新闻,公告以及其他信息请使用其他模块操作,该模块仅处理会员数据则必须登录才有权访问*/
	
	
	public function index(){
		$head['title'] = '会员中心';
		$this->assign('head',$head);
		$this->display();
	}
	
	public function my_msg(){
		$this->display();
	}
	
	public function password(){
		$user = M('user');
		$row = $user->where(array('id'=>session('uid')))->find();
		if(IS_POST){
			if(strlen(I('param.x_password')) < 6){
				$this->error('密码长度不足六位,请重试...');
			}
			if(I('param.x_password') != I('param.r_password')){
				$this->error('两次输入密码不一致,请重试...');
			}
			if($row['u_password'] != get_md5_password(I('param.password'))){
				$this->error('原密码不正确,请重试...');
			}
			if($row['u_password'] == get_md5_password(I('param.x_password'))){
				$this->error('新密码与原密码相同,请重试...');
			}
			if($user->where(array('id'=>session('uid')))->save(array('u_password'=>get_md5_password(I('param.x_password'))))){
				$this->success('新密码设置成功,请牢记新密码...',UU('member/welcome'));
			}else{
				$this->error('新密码设置失败,请重试...');
			}
			exit;
		}
		$this->assign('data',$row);
		$this->display();
	}
	
	public function edit(){
		$user = M('user');
		if(IS_POST){
			$data['tel'] = I('param.tel');
			$data['qq'] = I('param.qq');
			if(($user->where(array('id'=>session('uid')))->save($data)) === false){
				$this->error('更新资料失败,请重试...');
			}
			$this->success('更新资料成功...',UU('member/edit'));
			exit;
		}
		$this->assign('data',$user->where(array('id'=>session('uid')))->find());
		$this->display();
	}
}