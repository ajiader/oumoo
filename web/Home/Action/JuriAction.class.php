<?php
namespace Home\Action;
use Think\Controller;

class JuriAction extends Controller{

	public function _initialize() {
		$this->assign('column',get_all_column(-1));
	}
	
	public function get_column($id) {

		$row = $this->column_call($id);

		if(!$row){
			$this->error('抱歉,栏目不存在...','/');
		}
		if($row['class_url']){
			header("location:".$row['class_url']);
			exit;
		}

		$templet = $row['class_templet'] ? 'Index_'.$row['class_templet'].'_column' : '';
		$head['title'] = $row['class_title'];
		$head['name'] = $row['class_name'];
		$head['keywords'] = $row['class_key'];
		$head['description'] = $row['class_msg'];
		$head['description'] = $row['class_msg'];
		$head['description'] = $row['class_msg'];
		$head['id'] = $row['id'];
		$this->assign('head',$head);
		$this->get_position($id);
		$msg = M('msg_main');
		$join = 'left join msg_class ON msg_class.msg_id = msg_main.id';

		$join2 = 'left join msg_main_'.$row['class_type'].' ON msg_main.id = msg_main_'.$row['class_type'].'.pid';

		$order = 'msg_main.main_top DESC';

		if($row['class_type'] == 'model'){
				$order .= ', msg_main_model.main_down_number DESC';
		}

		if($row['class_type'] == 'model'){
			if(I('param.style')){
				$where['msg_main_model.main_style'] = I('param.style');
			}
			if(I('param.order') == 'number'){
				$order = 'msg_main.main_top DESC, msg_main_model.main_down_number DESC';
			}
		}

		$where['msg_main.main_examine'] = 1;
		$where['msg_class.class_id'] = $id;
		$count = $msg->join($join)->join($join2)->where($where)->count();
		$Page = new \Think\Page($count,50);
		$show = $Page->show();

		$row = $msg->join($join)->join($join2)->where($where)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();//链表查询内容

		$this->assign('data',$row);
		$this->assign('page',$show);

		return $templet;

	}
	
	public function get_search($id=null,$search=null) {

		$msg = M('msg_main');

		if($id) {
			$join = 'left join msg_class ON msg_main.id = msg_class.msg_id';
			$where['msg_class.class_id'] = $id;
			$where['msg_main.main_examine'] = 1;
			if($search){
				$where['msg_main.main_name'] = array('like',"%".$search."%");
			}
			$count      = $msg->join($join)->where($where)->count();
			$Page       = new \Think\Page($count,50);
			$Page->url	= 2;
			$show       = $Page->show();
			$row = $msg->join($join)->where($where)->order('msg_main.main_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();//链表查询内容
		} else {
			if($search) {
				$where['main_name'] = array('like',"%".$search."%");
			}
			$where['main_examine'] = 1;
			$count      = $msg->where($where)->count();
			$Page       = new \Think\Page($count,50);
			$Page->url	= 2;
			$show       = $Page->show();
			$row = $msg->where($where)->order('main_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		}

		$head['title'] = '搜索';
		$head['name'] = $search;
		$head['keywords'] = $search;
		$head['description'] = $search;
		$this->assign('head',$head);
		$this->assign('data',$row);
		$this->assign('page',$show);
		$this->assign('position','<span>当前位置:</span><a href="/">首页</a>&nbsp;&gt;&nbsp;<a href="#">搜索“'.$search.'”</a>');

		return true;
		
	}
	
	public function get_content($id,$exp=false) {

		$the = M('msg_class');
		$msg = M('msg_main');

		$row = $the->where(array('msg_id'=>$id,'appoint'=>1))->find();
		$row1 = $msg->where(array('id'=>$id))->find();

		if(!$row1){
			$this->error('抱歉,内容不存在...','/');
		}

		$msg->where(array('id'=>$id))->save(array('main_browse'=>($row1['main_browse']+1)));
		$head['id'] = $row['id'];
		$head['title'] = $row1['main_name'];
		$head['keywords'] = $row1['main_key'];
		$head['description'] = $row1['main_msg'];
		$temp = $this->column_call($row['class_id']);
		$templet = $temp['class_templet'] ? 'Index_'.$temp['class_templet'].'_content' : '';
		$head['name'] = $temp['class_name'];
		$this->assign('head',$head);

		$sch = M('msg_main_'.$temp['class_type']);
	
		$row2 = $sch->where(array('pid'=>$id))->find();
		$data = array_merge($row1,$row2);
		$this->assign('content',$data);
		
		$this->get_position($row['class_id'],$row1['id'],$row1['main_name']);
		
		if($exp && $row2 !== false){
			return $data;
		}

		return $templet;

	}
	
	public function get_position($id,$co=false,$co_name=false) {

		$class = M('class_name');
		$str = false;

		while ($id){
			$row = $class->where(array('id'=>$id))->find();
			if(!$str){
				$str = '<a href="'.UU('column',array('id'=>$row['id'])).'">'.$row['class_name'].'</a>';
			}else{
				$str = '<a href="'.UU('column',array('id'=>$row['id'])).'">'.$row['class_name'].'</a>&nbsp;&gt;&nbsp;'.$str;
			}
			if($row['class_top'] == '-1'){
				$this->assign('column_top_id',$id);
				$id = false;
				break;				
			}
			$id = $row['class_top'];
		}

		if($str){
			$str = '<span>当前位置:</span><a href="/">首页</a>&nbsp;&gt;&nbsp;'.$str;
		};

		if($co){
			$str .= '&nbsp;&gt;&nbsp; <a href="'.UU('content',array('id'=>$co)).'">'.$co_name.'</a>';
		}
		
		$this->assign('position',$str);	

		return true;	

	}

	private function column_call($id) {

		$class = M('class_name');
		$row = $class->where(array('id'=>$id))->find();

		return $row;

	}
}