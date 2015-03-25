<?php
namespace Admin\Controller;
use Admin\Action\AdminAction;
class IndexController extends AdminAction {
	
    public function index(){//登录
    	if(IS_POST && detection(I('post.name')) != null && detection(I('post.name'))!=''){
    		$User = M("admin"); // 实例化User对象
			$data = $User->where(array('name'=>I('post.name')))->getField('password',1);
			if($data==get_md5_password(I('post.password')) && $data!= '' && $data!=null){//登录成功
				session('login',I('post.name'));  //设置session
				$this->success("登录成功",U("main"));//跳转到后台页面
			}else{
				session('login',null);
				$this->error("登录失败",U("index"));//跳转到登录页面
			};
			exit;
    	};
    	$this->display("index");
    }
	
    public function admin_exit(){//退出登录
    	session('login',null);  //设置session
    	$this->success("成功退出",U("index"));//跳转到后台页面
    }
   
    public function main(){//后台首页
    	$this->assign('the_memu',C('CLASS_NAME'));
    	$this->display('main');
    }
    
	public function conf(){//站点配置
		if(IS_POST){
			foreach (I('post.') as $a => $k){
				if(get_magic_quotes_gpc($k)){
					$config[$a] = $k;
				}else{
					$config[$a] = addslashes($k);
				}
			}
			if($this->update_config($config)){
				$this->success('保存成功!');
			}else{
				$this->error("保存失败...");
			}
			exit;
		};
		$this->display();
	}
	
	public function select(){//管理员信息
		$User = M("admin");
		$count      = $User->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show       = $Page->show();// 分页显示输出
		$list = $User->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('webdata',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display('select');
		 
	}
	
	public function add(){//管理员增删改
		$id = I('param.id');
		if(I('get.type') == "delete"  ){//删除管理员
			if(!$id || !is_numeric($id)){
				$this->error("出于安全考虑,批量删除已被管理员禁止....",U("select"));
				exit;
			}
			$link = M("admin");
			$row = $link->where(array('id'=>$id))->find();
			if($row && $row['jurisdiction'] == 'all'){//all为超级管理,且只能有一个
				$this->error("超级管理员禁止被删除....",U("select"));
				exit;
			}
			if($link->where(array('id'=>$id))->delete()){
				$this->success("删除成功",U("select"));
			}else{
				$this->error("删除失败,请联系奇文科技...",U("select"));//跳转到登录页面
			};
			exit;
		}
		
		if(IS_POST){//增加或修改
			$name = strtolower(I('param.name'));
			$password = I('param.password');
			if(!preg_match("/^[\w]+$/",$name)){
				$this->error("账户只能由0-9 a-z组成...",U("select"));
				exit;
			}			
			if(!preg_match("/^[\w]+$/",$password)){
				$this->error("密码只能由A-Z 0-9 a-z组成,且区分大小写...",U("select"));
				exit;
			}			
			if(!test_char($name, 2)){
				$this->error("账户长度不够...",U("select"));
				exit;
			}
			if(!test_char($password, 6)){
				$this->error("密码长度不够...",U("select"));
				exit;
			}
			$link = M("admin"); // 实例化User对象
			$data['name'] = $name;
			$data['password'] = get_md5_password($password);
			if(is_numeric($id) && $id>0){
				$row = $link->where(array('id'=>$id))->save($data); // 根据条件更新记录
			}else{
				$row = $link->add($data);//添加
			};
			if($row !== false){
				$this->success("更新成功",U("select"));
			}else{
				$this->error("更新失败...",U("select"));
			}
			exit;
		}
		
		if( is_numeric($id) && $id>0){
			$link = M("admin");
			$data = $link->where(array('id'=>$id))->find();
			$data['title'] = '修改用户信息';
		}else{
			$data['title'] = '添加用户';
		};
		$this->assign('webdata',$data);//读取配置
		$this->display();
	}

}