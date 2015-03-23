<?php
namespace Admin\Controller;
use Admin\Action\AdminAction;

/*
 * 
 * 本类是核心类,是主数据表与附数据表组合,成为一个真正的高扩展CMS系统
 * 但并不是能满足所有功能,例:图片集的人性化管理，加入一张图片集的表,一行数据代表一个图片!(或者扩展字段用分割亦可)
 * 加入的功能全部命名为扩展功能,加入扩展功能应注明...
 * 同时扩展功能不得影响CMS系统原有构架
 * 
 * */
class MsgController extends AdminAction {
	public function msg(){//列表
		$id = I('param.id');
		$data = M('class_name');//栏目表
		$msg = M('msg_main');//关系表联合查询数据表
		
		$row = $data->where(array('id'=>$id))->find();//查询栏目信息
		$this->assign('class_name',$row);
		
		$join = 'left join qiwen_msg_class ON qiwen_msg_main.id = qiwen_msg_class.msg_id';
		if(I('param.search') && I('param.search') != ''){
			$where = array('qiwen_msg_main.main_name'=>array('like',"%".I('param.search')."%"));
		};
		
		if(I('param.main_examine') != false && I('param.main_examine') != 'yes'){
			$where['qiwen_msg_main.main_examine'] = I('param.main_examine');
		}
		$where['qiwen_msg_class.class_id'] = $id;//一个内容可能属于多个栏目,如果没有栏目id内容将会重复显示!
		$count      = $msg->join($join)->where($where)->count();
		$Page       = new \Think\Page($count,30);
		$show       = $Page->show();
		
		$row = $msg->join($join)->where($where)->order('qiwen_msg_main.main_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();//链表查询内容
		$this->assign('web_data',$row);
		$this->assign('page',$show);
		
		$this->display();
	}
	
	public function del_Batch(){//批量删除
		$id = I('param.id');
		$class_id = I('get.class_id');
		if(!$id || !$class_id){//没有内容ID无法删除, 该内容可能在栏目大类进行管理删除,栏目子类属性必须跟随大类(所以删除后跳转到操作管理类)
			$this->error('删除失败,请及时联系管理......');
			exit;
		}
		$class = M('class_name');
		$the = M('msg_class');
		$msg = M('msg_main');
		$pic = M('msg_main_pic');
		
		$row = $class->where(array('id'=>$class_id))->find();
		if(!$row){
			$this->error('删除失败,请联系管理...');//不存在该栏目,可能有数据!	这时应该联系管理处理
			exit;
		}
		$sch = M('msg_main_'.$row['class_type']);//附表模型
		$msg->startTrans();
		
		

		$row1 = $msg->where(array('id'=>array('in',$id)))->delete();
		$row2 = $the->where(array('msg_id'=>array('in',$id)))->delete();
		$row3 = $sch->where(array('pid'=>array('in',$id)))->delete();
		$row4 = $pic->where(array('pid'=>array('in',$id)))->delete();
		
		
		if($row1 !== false && $row2 !== false && $row3 != false && $row4 !== false){
			$msg->commit();
			$this->success('删除成功!',U('msg',array('id'=>$class_id)));//带有栏目,删除成功后跳转栏目
		}else{
			$msg->rollback();
			$this->error('删除失败,请及时联系管理......');
		}
		exit;	
	}
	
	public function del(){//单个删除
		$id = I('param.id');
		$class_id = I('param.class_id');
		if(!$id){//没有内容ID无法删除,不存在栏目id可以跳转至内容跟栏目
			$this->error('非法提交,无法删除......');
			exit;
		}
		$class = M('class_name');
		$the = M('msg_class');
		$msg = M('msg_main');
		$pic = M('msg_main_pic');

		$row = $the->where(array('msg_id'=>$id,'appoint'=>1))->find();
		$row = $class->where(array('id'=>$row['class_id']))->find();
		$sch = M('msg_main_'.$row['class_type']);//附表模型
		
			
		
		$row = $msg->where(array('id'=>$id))->find();
		if(!$row){
			$this->error('该内容不存在,无法删除......');
			exit;
		}
		$msg->startTrans();
		$row1 = $the->where(array('msg_id'=>$id))->delete();//删除关系表数据
		$row2 = $msg->where(array('id'=>$id))->delete();//删除主表数据
		$row3 = $sch->where(array('pid'=>$id))->delete();//删除附表数据
		$row4 = $pic->where(array('pid'=>$id))->delete();//删除图片集
		if($row1 !== false && $row2 !== false && $row3 !== false && $row4 !== false){
			$msg->commit();//成功并提交
			if($class_id){
				$this->success('删除成功!',U('msg',array('id'=>$class_id)));//带有栏目,删除成功后跳转栏目
			}else{
				$this->success('删除成功!',U('class/class_name'));//没有栏目跳转到跟栏目
			}
		}else{
			$msg->rollback();//失败并回滚
			$this->error('删除失败,请及时联系管理......');
			exit;
		}
	}
	
	public function data(){//添加或修改,并且模型数据表
		$id = I('param.id');//更新数据
		$class_id = I('param.class_id');//插入数据时更新或添加必须有栏目
		$class_type = I('param.class_type');//添加数据时可以传递栏目类型,让用户选择该栏目类型的栏目
		
		$class = M('class_name');//栏目数据表
		$msg = M('msg_main');	//数据主表
		$pic = M('msg_main_pic');//数据主表的直属图片集附表
		$the = M('msg_class');//栏目表与数据主表关系表
	
		if(IS_POST){//更新或插入新数据
			if(!$class_id){//必须有栏目信息才可以添加数据或更新,即使修改内容有ID!那么很可能把内容的栏目更换
				$this->error('请先选择栏目......');
				exit;
			}
			$row = $class->where(array('id'=>$class_id))->find();
			if(!$row){
				$this->error('栏目不存在......');
				exit;
			}
			$sch = M('msg_main_'.$row['class_type']);//数据模型表
			/*提交表单,不可非法提交!主表数据name=data[],模型附表数据不可占用*/
			
			
			//启动MYSQL事物
			$msg->startTrans();
			foreach (I('param.data') as $a => $k){//循环出主表的数据
				$data[$a] = $k;
			};
			
			/*============会员名转会员ID===============*/
			$user = get_user_name($data['uid']);//data['uid']目前还是会员名,需要转换成会员id
			if(!$user){
				$this->error('没有找到该会员,请核准后再试...');
				exit;
			}
			$data['uid'] = $user['id'];
			/*========================*/
			
			if(!is_numeric($data['main_time']) || !$data['main_time']){//判断提交过来的时间是否是时间戳
				$data['main_time'] = strtotime($data['main_time']);
			};
			if(!$data['main_time']){//判断提交过来的时间是否是时间戳
				$data['main_time'] = time();
			};
			//$data['main_type'] = $row['class_type'];//把该栏目的模型赋值到内容上,不用查询栏目也可以知道数据模型
			
			//附表不允许出现主表字段,也不许出现栏目ID字段也不许出现内容ID字段,不允许出现主表的直属图片集附表
			foreach (I('post.') as $a => $k){//循环出附表的数据
				if($a != 'data' && $a != 'class_id' && $a != 'id' && $a != 'pic'){
					$data_s[$a] = $k;
				}
			}
			
			//判断是更新数据还是增加数据
			if($id){
				$row1 = $msg->where(array('id'=>$id))->save($data);//更新主表
				
				$row2 = $sch->where(array('pid'=>$id))->save($data_s);//更新附表
				
				$row3 = $the->where(array('msg_id'=>$id))->delete();//删除关系表
				
				$row4 = $pic->where(array('pid'=>$id))->delete();//删除图片集
				$data_c['msg_id'] = $id;
				$data_c['class_id'] = $class_id;
			}else{
				$row1 = $msg->add($data);//插入主表

				$data_s['pid'] = $row1;
				$row2 = $sch->add($data_s);//插入附表
				
				$data_c['msg_id'] = $row1;
				$data_c['class_id'] = $class_id;
			}
			
			
			foreach (I('post.pic') as $oid => $a){//插入图片集
				$row4 = $pic->add(array('pid'=>$data_c['msg_id'],'oid'=>$oid,'pic'=>$a));
				if(!$row4){
					$msg->rollback();//失败并回滚
					$this->error('保存失败!应及时联系管理......');
					exit;
				}
			}
			
			
			//文章所属最底级栏目
			$data_c['appoint'] = 1;
			while ($data_c['class_id']){//插入数据关系表并向上获取栏目再插入关系
				$row3 = $the->add($data_c);
				$data_c['appoint'] = '0';//栏目所属最底级只有1个,插入后应删除
				if(!$row3){
					$msg->rollback();//失败并回滚
					$this->error('保存失败!应及时联系管理......');
					exit;
				}
				$row = $class->where(array('id'=>$data_c['class_id']))->find();
				//var_dump($row);exit;
				if($row && $row['class_top'] != '-1'){
					$data_c['class_id'] = $row['class_top'];
				}else{//跳出循环
					break;
				}
			}
			/*
			* 查询当前栏目的上级ID  class_top
			*
			* */
			if($row1 !== false && $row2 !== false && $row3 !== false && $row4 !== false){
				$msg->commit();//成功并提交
				$this->success("保存成功!",U("msg",array('id'=>$class_id)));
			}else{
				$msg->rollback();//失败并回滚
				$this->error('保存失败,应及时联系管理......');
			}
			exit;
		}
			
		if(!$class_id && !$class_type && !$id){//没有指定栏目就应该指定栏目类型或内容$id
			$this->error('非法提交,请重试!否者联系管理......');
			exit;
		};
		
		if($id){//如果有$id,那么是更新内容,赋值到模版并以内容所在的栏目ID为准
			$row = $msg->where(array('id'=>$id))->find();//查询主表内容数据供前台调用
			$this->assign('web_data',$row);
			
			$pic_list = $pic->where(array('pid'=>$id))->order('oid asc')->select();
			$this->assign('pic_list',$pic_list);
			
			//appoint = 1 是内容所在绝对栏目, != 1 是父栏目或祖父栏目
			$row = $the->where(array('msg_id'=>$id,'appoint'=>1))->find();//查询内容所属栏目信息
			$class_id = $row['class_id'];
			
		}
		
		if($class_id){//查询栏目信息
			$row = $class->where(array('id'=>$class_id))->find();
			if(!$row){
				$this->error('栏目不存在,如有疑问请联系管理......');
				exit;
			}
			$class_type = $row['class_type'];
			$this->assign('class_name',$row);
		};
		
		if($id){//赋值附表信息
			
			$msg = M('msg_main_'.$row['class_type']);
			$row = $msg->where(array('pid'=>$id))->find();//查询附表内容数据供前台调用
			$this->assign('data',$row);
			
		}
		
		$this->assign('class_type','data_'.$class_type);//模板调用附表的模板
		$row = $class->where(array('class_type'=>$class_type))->order('c_order asc')->select();//取得该类型的所有可供用户选择
		if(!$row){
			$this->error('栏目不存在,如有疑问请联系管理......');//没有该栏目的类型
			exit;
		}
		import("@.Action.Tree");
		$tree=new \tree();
		$tree->init($row)->order('-1','-1','━','');//从父栏目几开始-1代码顶级,━传回数组的前缀,''代表嵌套的前缀h后的修饰符
		$row = $tree->tree;
		$this->assign('class_row',$row);
		$this->display();
	}
}