<?php

namespace Home\Controller;
use Home\Action\JuriAction;
class IndexController extends JuriAction {
	public $data;
	public function column() {

		if ($_GET['sid'] && session('uid') == 1) {
			if ($_GET['type'] == 'add') {
				$data = M("msg_main")->where('id = '.$_GET['sid'])->find();
				if ($data) {
					$slider = $data['main_slider'] == 0 ? 1 : 0;

					M()->query('UPDATE msg_main SET main_slider = '.$slider.', main_slider_time = '.time().' where id='.$_GET['sid']);
				}
			} else {
				$data = M("msg_main")->where('id = '.$_GET['sid'])->find();
				if ($data) {
					M()->query('UPDATE msg_main SET main_slider = 0 where id='.$_GET['sid']);
				}
			}

			header("location: " . '/column/'.I('param.id').'.html');

		}

		$this->assign('index',I('param.id'));
		$templet = $this->get_column(I('param.id'));
		$this->display($templet);

	}
	
	public function content() {
		$templet = $this->get_content(I('param.id'));
		$this->display($templet);
	}
	
	public function search() {
		if (!I('param.search')) {
			header("location: /");
			exit;
		}
		$this->get_search(I('param.search_type'),II('param.search'));
		$this->display();
	}
	
	public function exp() {
		$id = I('param.id');
		$data = $this->get_content($id,true);
		if (!$data['main_nnnex']) {
			$this->error('模型不存在,请联系我们....');
		}
		
		if (!$data['main_money'] || $data['main_money'] == 0) {
			$sch = M('msg_main_model');
			$sch->where(array('pid'=>$id))->save(array('main_down_number'=>($data['main_down_number']+1) ));
			$this->display();
			exit;
		};
		
		if (!session('uid')) {
			$this->error('下载会员专区模型需要先进行登录...',UU('member/login'));
		}
			
		$myModel = M('user_model');
		$user = M('user');
			
		if (!($myModel->where(array('uid'=>session('uid'),'pid'=>$id,'time'=>array('gt',(time()-2592000))))->find())) {
			
			$user->startTrans();
			
			$row1 = save_money(session('uid'),-$data['main_money'],'购买模型','购买'.$data['main_name']);
			if ($row1 === 3) {
				$user->rollback();
				$this->error('您当前的欧币不足,无法下载该附件...');
			}
			
			$row2 = save_money($data['uid'],(floor($data['main_money']/2)),'模型工资',get_user_msg('u_name').'下载购买了模型'.$data['main_name']);
			
			$row3 = $myModel->add(array('uid'=>session('uid'),'pid'=>$id,'time'=>time()));
			
			if ($row1 !== true || $row2 !== true || $row3 === false) {
				$user->rollback();
				$this->error('下载失败,请重试...');
			}
			$user->commit();
			
			save_user_session();
			$sch = M('msg_main_model');
			$sch->where(array('pid'=>$id))->save(array('main_down_number'=>($data['main_down_number']+1) ));
		}
		header("location:".$data['main_nnnex']);
		exit;
	}

	public function index() {

		if ($_GET['sid'] && session('uid') == 1) {
			if ($_GET['type'] == 'add') {
				$data = M("msg_main")->where('id = '.$_GET['sid'])->find();
				if ($data) {
					$slider = $data['main_slider'] == 0 ? 1 : 0;

					M()->query('UPDATE msg_main SET main_slider = '.$slider.', main_slider_time = '.time().' where id='.$_GET['sid']);
				}
			} else {
				$data = M("msg_main")->where('id = '.$_GET['sid'])->find();
				if ($data) {
					M()->query('UPDATE msg_main SET main_slider = 0 where id='.$_GET['sid']);
				}
			}

			header("location: " . '/column/69.html');

		}

		$templet = $this->get_column(69);
		$this->assign('index',69);
		$this->display($templet);

	}

	public function top() {

		if ($_GET['id'] && session('uid') == 1) {
			$data = M("msg_main")->where('id = '.$_GET['id'])->find();
			if ($data) {
				$top = $data['main_top'] == 0 ? 1 : 0;
				M()->query('UPDATE msg_main SET main_top = '.$top.', main_time = '. time(). ' where id='.$_GET['id']);
			}
		}

		header("location:".base64_decode($_GET['return']));

	}

	public function download()
	{
		$id = I('param.id');
		if ($id) {
			$data = $this->get_content($id,true);
			if ($data) {
				header("location: ". $data['main_nnnex']);
			} else {
				$this->error('该资源已被删除或屏蔽...', '/');
			}
		} else {
			$this->error('该资源已被删除或屏蔽...', '/');
		}
	}
}