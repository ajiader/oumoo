<?php
namespace Home\Controller;
use Home\Action\UserAction;
class UpController extends UserAction{
	public function pic(){//缩略图上载
		header('Content-type: text/html; charset=utf-8');
		if(IS_POST){
			$upload = new \Think\Upload();// 实例化上传类
		    $upload->maxSize   =     3145728 ;// 设置附件上传大小
		    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		    $upload->rootPath  =      '/Uploads/'; // 设置附件上传根目录
		    $info   =   $upload->uploadOne($_FILES['photo']);
			if($info) {
				
				if(I('param.Thumbnails')){//生成缩略图
					$image = new \Think\Image();
					$image->open('.'.$upload->rootPath.$info['savepath'].$info['savename']);
					// 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
					$image->thumb(190, 220)->save('.'.$upload->rootPath.$info['savepath'].$info['savename']);
				};
					
				echo("<script type='text/javascript'>window.parent.".I('param.name')."('".$upload->rootPath.$info['savepath'].$info['savename']."');</script>");
			}else{
				echo("<script type='text/javascript'>window.parent.".I('param.name')."('上传失败:".$upload->getError()."');</script>");
			}
		}
		$this->display();
	}

	public function file(){//单个文件上载
		header('Content-type: text/html; charset=utf-8');
		if(IS_POST){
			$upload = new \Think\Upload();// 实例化上传类
		    $upload->maxSize   =     157286400 ;// 设置附件上传大小
		    $upload->exts      =     array('rar', 'zip', 'tar', 'cab','uue','jar','iso','z');// 设置附件上传类型
		    $upload->rootPath  =      '/Uploads/'; // 设置附件上传根目录
		    $info   =   $upload->uploadOne($_FILES['file']);
			if($info) {
				$temp = "'".$upload->rootPath.$info['savepath'].$info['savename']."','".sprintf("%.2f",$info['size']/1024/1024)."MB'";
				echo("<script type='text/javascript'>window.parent.".I('get.name')."(".$temp.");</script>");
			}else{
				$temp = "'上传失败:".$upload->getError()."','0kb'";
				echo("<script type='text/javascript'>window.parent.".I('get.name')."(".$temp.");</script>");
			}
		}
		$this->display();
	}
	
}