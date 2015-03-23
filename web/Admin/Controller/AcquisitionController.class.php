<?php
namespace Admin\Controller;
use Admin\Action\AdminAction;
class AcquisitionController extends AdminAction {
	public function data(){
		if(IS_POST){
			ignore_user_abort();
			set_time_limit(0);
			$id = I('class_id');
			if(!$id || $id<1){
				$this->error('非法提交,请联系管理员....');
			}
			$class = M('class_name');
			$row = $class->where(array('id'=>$id))->find();
			if(!$row){
				$this->error('没有找到该栏目,请联系管理员....');
			}
			$class_type = $row['class_type'];
			if($class_type == 'model'){//模型规则采集,批量导入类型
				import('@.Acquisition.Model');
				$a = new \Admin\Acquisition\Model();
				$a->init($class_type);
			};
			if($class_type == 'chartlet'){//贴图规则采集,批量导入类型
				import('@.Acquisition.chartlet');
				$a = new \Admin\Acquisition\Model();
				$a->init($class_type);
			};
			exit;
		}
		
		
		
		
		$class_type = I('param.class_type');
		if($class_type != 'model' && $class_type != 'chartlet'){//实现的导入规则,请在这里添加
			$this->error('该批量导入由系统自动生成,需要由管理员实现导入规则!请联系管理员......',false,10);//没有该栏目的类型
			exit;
		};
		$class = M("class_name");
		$row = $class->where(array('class_type'=>$class_type))->select();//取得该类型的所有可供用户选择
		if(!$row){
			$this->error('该批量导入扩展没有找到相应栏目!......');//没有该栏目的类型
			exit;
		}
		import("@.Action.Tree");
		$tree=new \tree();
		$tree->init($row)->order('-1','-1','━','');
		$row = $tree->tree;
		$this->assign('class_row',$row);
		$this->assign('class_type','data_'.$class_type);
		$this->display();
	}
}