<?php
namespace Admin\Controller;
use Admin\Action\AdminAction;
class ClassController extends AdminAction {
	public function class_name(){//栏目列表
		$User = M("class_name");
		
		if(IS_POST){//更新排序
			$User->startTrans();//开启MYSQL事物
			foreach (I('param.id') as $id => $v){
				if(is_numeric($id) && is_numeric($v)){
					if(($User->where(array('id'=>$id))->save(array('c_order'=>$v))) === false){
						$User->rollback();//失败并回滚
						$this->error('更新排序失败!应及时联系管理......');
						exit;
					}
				}
			}
			$User->commit();
			$this->success('更新排序完毕...',U('class_name'));
			exit;
		}
		/*if(I('get.search')){
			$where = array('name'=>array('like',"%".I('get.search')."%"));
		};
		if(I('get.class_top')){
			$where['class_top'] = I('get.class_top');
		}
		$count      = $User->where($where)->count();
		$Page       = new \Think\Page($count,10);
		$show       = $Page->show();
		$list = $User->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();*/
		$list = $User->order('c_order asc')->select();
		
		import("@.Action.Tree");
		$tree=new \tree();
		$tree->init($list)->order('-1');
		$list = $tree->tree;
		
		$this->assign('webdata',$list);
		$this->assign('count',$User->count());
	
		$this->display();
	}
	
	public function is_class(){//判断该类下是否还有分类
		$id = I('get.id');
		if(!id || !is_numeric($id)){
			exit('error');
		}
		$User = M("class_name");
		if($User->where(array('class_top'=>$id))->find()){
			exit('yes');
		};
		exit('no');
	}
	public function is_msg(){//判断该类下是否还有内容
		
	}
	
	public function class_add(){//增加栏目
		if(I('post.class_top') || I('get.class_top')){//增加子级栏目
			$class_top = I('post.class_top') ? I('post.class_top') : I('get.class_top');
		}else{//没有则增加顶级栏目
			$class_top = -1;
			$this->assign('class_name',C('CLASS_NAME'));
		}
		$class_name = M("class_name");
		if($class_top != -1){
			$temp = $class_name->where(array('id'=>$class_top))->find();
		};
		if(IS_POST){//增加栏目处理
			foreach ($_POST as $k=>$v){
				$data[$k] =  $v;
			};
			if($class_top != -1 ){//继承上级分类的属性
				$data['class_type'] = $temp['class_type'];
			};
			if($class_name->add($data)){
				$this->success("添加成功",U("class_name"));
			}else{
				$this->error("添加失败,请重试...");
			}
			exit;
		};
		//如果有上级栏目,那么模版继承上级栏目的!也可以修改
		//但栏目属性必须继承上级栏目,且不可修改
		$this->assign('web_data',$temp);
		$this->assign('class_top',$class_top);
		$this->display();
	}
	
	public function class_del(){//删除栏目
		$id = I('get.id');
		if(!is_numeric($id) || !$id){
			$this->error("出于安全考虑,批量删除已被管理员禁止...");
			exit;
		};
		$link = M("class_name"); // 实例化User对象
		
		//删除时检查该分类下是否有子类
		$row = $link->where(array('class_top'=>$id))->find();
		if($row){
			$this->error("该分类下还有子类!请先删除子类...");
			exit;
		}
		$data = M('msg_class');
		$row = $data->where(array('class_id'=>$id))->find();
		if($row){
			$this->error("该分类下还有内容,请先删除内容...");
			exit;
		}
		
		//删除时检查该分类下是否有内容
		$row = $link->where(array('class_top'=>$id))->find();
		if($row){
			$this->error("该分类下还有子类!请先删除子类...");
			exit;
		}
		
		if($link->where(array('id'=>$id))->delete()){
			$this->success("删除成功",U("class_name"));
		}else{
			$this->error("删除失败,请联系奇文科技...",U("class_name"));//跳转到登录页面
		};
	}
	
	public function class_edit(){
		$id = I('get.id') ? I('get.id') : I('post.id') ;//需要修改的分类
		if(!id || !is_numeric($id)){
			$this->error("非法操作......",U("className"));
		}
		$link = M("class_name");
		$row = $link->where(array('id'=>$id))->find();
		if(!row){
			$this->error("没有找到该分类,无法修改");
			exit;
		}
		if(IS_POST){
			foreach ($_POST as $k=>$v){
				$data[$k] =  $v;
			};
			$row = $link->where(array('id'=>$data['id']))->save($data);
			if($row !== false){
				$this->success("修改成功",U("class_name"));
			}else{
				$this->error("修改失败,请联系管理员....");
			}
			exit;
		}
		$this->assign('webdata',$row);
		$this->display();
	}
}