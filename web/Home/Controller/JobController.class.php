<?php
/*
 * 本类调用后台招聘数据,后台数据更改应及时更新本类
 * 并非CMS自动加载等功能!此功能为会员功能!
 * */
namespace Home\Controller;
use Home\Action\UserAction;
class JobController extends UserAction {
	public function wanted(){//求职
		$msg = M('msg_main');//msg_main_jobw
		$join = 'right join msg_main_jobw ON msg_main.id = msg_main_jobw.pid';
		$where['msg_main.uid'] = session('uid');
		if(I('param.search') && I('param.search') != ''){
			$where = array('msg_main.main_name'=>array('like',"%".I('param.search')."%"));
		};
		$count      = $msg->join($join)->where($where)->count();
		$Page       = new \Think\Page($count,10);
		$Page->rollPage = 5;
		$show       = $Page->show();
		$row = $msg->join($join)->where($where)->order('msg_main.main_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();//链表查询内容
		$this->assign('web_data',$row);
		$this->assign('page',$show);
		$this->display();
	}
	public function wanted_data(){//添加/修改
		$msg = M('msg_main');
		$sch = M('msg_main_jobw');
		$class = M('class_name');
		$the = M('msg_class');
		if(IS_POST){
			$data['main_name'] = I('param.main_name');
			$data['main_text'] = I('param.main_text');
			$data['uid'] = session('uid');
			$data['main_examine'] = -1;//不管添加或修改都需要管理员审核
			
			$datas['job_name'] = I('param.job_name');
			$datas['job_money'] = I('param.job_money');
			$datas['job_tel'] = I('param.job_tel');
			$datas['job_address'] = I('param.job_address');
			$datas['job_exp'] = I('param.job_exp');
			
			$msg->startTrans();//开启mysql事物
			
			if(I('param.id') && is_numeric(I('param.id'))){//更新
				
				$row1 = $msg->where(array('id'=>I('param.id'),'uid'=>session('uid')))->save($data);
				$row2 = $sch->where(array('pid'=>I('param.id')))->save($datas);
				
				$row3 = class_save('jobw',I('param.class_id'),I('param.id'));
						
			}else{//添加
				
				$row1 = $msg->Add($data);
				
				$datas['pid'] = $row1;
				$row2 = $sch->Add($datas);
				
				$row3 = class_save('jobw',I('param.class_id'),$row1);
				
			}
			
			/*
			 * class_save('栏目类型','栏目id','内容id')
			 * 解释,执行后函数回匹配栏目id的类型和指定类型是否匹配
			 * 该函数会插入内容id与栏目id到关系表并向上插入到顶级栏目
			 * 失败会返回具体原因,成功返回true
			 * */
			
			if($row3 !== true){//返回不是true代表返回了错误信息
				$msg->rollback();//失败并回滚
				$this->error($row3);
				exit;
			}
			
			if($row1 !== false && $row2 !== false && $row3 === true){
				$msg->commit();//成功并提交
				$this->success('保存成功!',U('wanted'));
			}else{
				$msg->rollback();//失败并回滚
				$this->error('抱歉,保存失败,请重试...');
			}
			exit;
		}
		if(I('param.id') && is_numeric(I('param.id'))){
			$join = 'right join msg_main_jobw ON msg_main.id = msg_main_jobw.pid';
			$where['msg_main.uid'] = session('uid');
			$where['msg_main.id'] = I('param.id');
			$row = $msg->join($join)->where($where)->find();
			if(!$row){
				$this->error('抱歉,当前编辑的简历不存在,请重试...');
			}
			$this->assign('data',$row);
			
			$row = $the->where(array('msg_id'=>I('param.id'),'appoint'=>1))->find();//查询内容所属栏目id
			$this->assign('class_id',$row['class_id']);
		}
		
		$row = $class->where(array('class_type'=>'jobw'))->order('c_order asc')->select();//查询出求职的栏目
		import("Admin.Action.Tree");
		$tree=new \tree();
		$tree->init($row)->order('-1','-1','━','');//从父栏目几开始-1代码顶级,━传回数组的前缀,''代表嵌套的前缀h后的修饰符
		$row = $tree->tree;
		$this->assign('class_row',$row);
		
		$this->display();
	}
	public function wanted_del(){//删除
		if(!delete_msg(I('param.id'),'msg_main_jobw')){//delete_msg(内容ID,附表)
			$this->error('抱歉,删除失败,请重试...');
		};
		$this->success('删除成功');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function Recruitment(){//招聘
		echo '<div style="margin:20px">企业招聘人才功能正在开发中...</div>';
	}
}