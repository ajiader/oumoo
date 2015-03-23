<?php
namespace Admin\Acquisition;
use Think\Controller;
class Acquisition extends Controller {
	
	public function dis($name,$i=0){
		echo $name.'<br />';
		if(I('param.display')){
			ob_flush();
			flush();
		};
		sleep($i);
	}
	
	public function file($route,$type,$pic_is=false,$pic_w =190,$pic_h=220 ){
		$type = '.'.$type;
		if(!is_dir(BASE_PATH)){
			return false;
		};
		$pic_route = '/Uploads/'.date('Y');
		if(!is_dir(BASE_PATH.$pic_route)){
			mkdir(BASE_PATH.$pic_route);
		};
		
		$pic_route .='/'. date('m');
		if(!is_dir(BASE_PATH.$pic_route)){
			mkdir(BASE_PATH.$pic_route);
		};
		
		$pic_route .= '/'.date('d');
		if(!is_dir(BASE_PATH.$pic_route)){
			mkdir(BASE_PATH.$pic_route);
		};
		
		$pic_file = time().rand(100,9999999999).$type;//生成文件名
		$file = BASE_PATH.$pic_route;//生成路径
		while (file_exists($file.'/'.$pic_file)){//判断该文件夹下的文件是否存在
			$pic_file = time().rand(100,9999999999).$type;//生成文件名
		}
		
		if(copy($route , $file.'/'.$pic_file)){//原始路径,移动到路径!原始文件我们不要去碰它
			
			if($pic_is){//判断是否需要缩小图片
				
				$image = new \Think\Image();
				$image->open($file.'/'.$pic_file);
				// 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
				$image->thumb($pic_w, $pic_h)->save($file.'/'.$pic_file);
				
// 				import("@.Action.Thumbnails");
// 				$thumbnails = new \Thumbnails($file.'/'.$pic_file , 190 , 220, $file);//原始图片,图片宽度,图片高度,保存路径
// 				$thumbnails->produce();
			}
			
			//要判断文件的大小,所以返回数组!0为移动后路径,1为web路径~!
			return array($file.'/'.$pic_file , $pic_route.'/'.$pic_file);
		}else{
			return false;
		}
		
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function code($code='UTF-8'){
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset='.$code.'" />
<title>admin</title>
<style type="text/css">
*{
	color:#0094FF;
	font-size:12px;
	line-height:20px;
}
span{
	color:#3D9E3E;	
}
body{
	margin:20px 10px;
}
</style>
</head>
<body>';
	}
};