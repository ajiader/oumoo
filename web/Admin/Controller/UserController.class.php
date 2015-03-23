<?php
namespace Admin\Controller;
use Admin\Acquisition\Acquisition;
class UserController extends Acquisition{
	public function user_list(){
		$user = M('user');
		if(I('param.search')){
			$where['_logic'] = 'or';
			$where['u_name'] = array('like','%'.I('param.search').'%');
			$where['mail'] = array('like','%'.I('param.search').'%');
		}
		$count     = $user->where($where)->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show       = $Page->show();// 分页显示输出
		$list = $user->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('webdata',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
	}
	
	public function del(){
		$id = I('param.id');
		if(!$id){
			$this->error('删除失败,没有选择要删除的会员...');
			exit;
		}
		$user = M('user');
		if($user->where(array('id'=>$id))->delete()){
			$this->success('会员删除成功,',U('user_list'));
		}else{
			$this->error('会员删除失败,请及时联系管理....');
		}
	}
	
	public function del_Batch(){
		$user = M('user');
		if($user->where(array('id'=>array('in',I('post.id'))))->delete()){
			$this->success('会员删除成功,',U('user_list'));
		}else{
			$this->error('会员删除失败,请及时联系管理....');
		}
	}
	
	public function user_data(){
		$user = M('user');
		if(IS_POST){
			$data = I('post.');
			if (empty($data['mail']) || !ereg("^[-a-zA-Z0-9_\.]+\@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,5}$",$data['mail'])){
				$this->error('邮箱格式不正确...');
				exit;
			}
			if($data['id']){
				unset($data['u_name']);//会员名不可更改
				if(!$data['u_password']){
					unset($data['u_password']);//如果密码为空则默认不变
				}else if( mb_strlen($data['u_password']) < 6){
					$this->error('密码必须大于六位....');//密码不为空则必须大于六位
					exit;
				}
			}else if(mb_strlen($data['u_name']) < 6 || mb_strlen($data['u_password']) < 6){//注册用户必须用户名密码大于六位
				$this->error('用户名,密码必须大于六位....');
				exit;
			}
			if($data['u_password']){
				if($data['u_password'] !== $data['r_password']){
					$this->error('两次密码输出不一样,请重试....');
					exit;
				} else {
					$data['u_password'] = get_md5_password($data['u_password']);//加密密码
				}
			}
			
			if(!is_numeric($data['reg_time']) || !$data['reg_time']){//判断提交过来的时间是否是时间戳
				$data['reg_time'] = strtotime($data['reg_time']);
			};
			if(!$data['reg_time']){//判断提交过来的时间是否是时间戳
				$data['reg_time'] = time();
			};
			if(!is_numeric($data['last_time']) || !$data['last_time']){//判断提交过来的时间是否是时间戳
				$data['last_time'] = strtotime($data['last_time']);
			};
			if(!$data['last_time']){//判断提交过来的时间是否是时间戳
				$data['last_time'] = time();
			};
			
			if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]){
				$ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
			}elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"]){
				$ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
			}elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"]){
				$ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
			}elseif (getenv("HTTP_X_FORWARDED_FOR")){
				$ip = getenv("HTTP_X_FORWARDED_FOR");
			}elseif (getenv("HTTP_CLIENT_IP")){
				$ip = getenv("HTTP_CLIENT_IP");
			}elseif (getenv("REMOTE_ADDR")){
				$ip = getenv("REMOTE_ADDR");
			}else{
				$ip = "127.0.0.1";
			}
			if(!$data['reg_ip']){
				$data['reg_ip'] = $ip;
			};
			if(!$data['last_ip']){
				$data['last_ip'] = $ip;
			};

			if($data['id']){
				$row = $user->where(array('id'=>$data['id']))->save($data);
			}else{
				$row = $user->where(array('u_name'=>$data['u_name'],'mail'=>$data['mail'],'_logic'=>'or'))->find();
				if($row){
					$this->error('邮箱或用户名已存在,无法添加新用户....');
					exit;
				}
				$row = $user->add($data);
			}
			if($row !== false){
				$this->success('会员信息保存成功....',U('user_list'));
			}else{
				$this->error('保存会员信息失败,请联系管理....');
			}
			exit;
		}
		if(I('param.id')){
			$row = $user->where(array('id'=>I('param.id')))->find();
			if(!$row){
				$this->error('抱歉,当前编辑的会员不存在，如有疑问请及时联系管理....');
				exit;
			}
			$row['title'] = '编辑会员';
		}else{
			$row['title'] = '添加会员';
		}
		$this->assign('web_data',$row);
		$this->display();
	}
	
	public function get_user_name(){//获取用户信息
		$user = get_user_name(I('param.name'));
		if(!$user){
			exit('no');
		}
		exit(json_encode($user));
			
	}
	
}