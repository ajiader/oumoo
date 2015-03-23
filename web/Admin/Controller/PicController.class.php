<?php
namespace Admin\Controller;
use Admin\Action\AdminAction;
class PicController extends AdminAction{
	public function pic(){
		header('Content-type: text/html; charset=utf-8');
			if(IS_POST){
				$upload = new \Think\Upload();// 实例化上传类
			    $upload->maxSize   =     3145728 ;// 设置附件上传大小
			    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			    $upload->rootPath  =      '/Uploads/'; // 设置附件上传根目录
			    $info   =   $upload->uploadOne($_FILES['photo']);
				if($info) {
					echo("<script type='text/javascript'>window.parent.".I('param.name')."('/".$info['savepath'].$info['savename']."');</script>");
				}else{
					echo("<script type='text/javascript'>window.parent.".I('param.name')."('上传失败:".$upload->getError()."');</script>");
				}
			}
		$this->display();
	}
	
}