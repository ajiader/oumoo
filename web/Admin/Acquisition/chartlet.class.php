<?php
/**
 *声明本类
 *由于在windows下测试遍历出来的文件以及文件夹是GBK编码
 *可能在linux下遍历出来的却是UTF-8
 *
 *为了遍历文件和文件夹,必须编码要对!如果遍历出来的是UTF-8编码把本类另存为UTF-8下哦
 *注意显示给用户看也要注意编码
 *
 *插入mysql前应注意编码
 *显示给用户看应注意编码
 * 
 */
namespace Admin\Acquisition;
use Admin\Acquisition\Acquisition;
class Model extends Acquisition{
	public function init($class_type){
		$msg = M('msg_main');				//主表
		$sch = M('msg_main_'.$class_type);	//附表
		$the = M('msg_class');				//关系表
		$class = M('class_name');				//栏目表
		$pic = M('msg_main_pic');			//主表直属图片集表
		
		ignore_user_abort();
		set_time_limit(0);
		
		header("Content-type: text/html; charset=GBK");
		$route = iconv('UTF-8', 'GBK', I('param.route'));
		$class_id = I('param.class_id');
		$main_style = I('param.main_style');
		
		$this->code('GBK');
		$this->dis('启动规则中,请稍候!启动采集后请勿刷新或关闭,即使断开链接也会采集完毕....');
		
// 		if(file_exists($route."\\Acquisition.txt")){
// 			exit('<span>该模型已被采集,如还要采集!请先删除'.$route."\\Acquisition.txt"."后再进行采集</span>");
// 		}
		
		if(file_exists($route."\\Acquisition.txt")){
			exit('<span style="color:Red;">已经采集过,重复采集请先删除目录下的Acquisition.txt文件...</span>');
		}
		$open=fopen($route."\\Acquisition.txt","a" );
		if(!$open){
			exit('<span style="color:Red;">PHP没有权限对该文件进行操作,请先授权PHP最高权限...</span>');
		}
		fwrite($open,'已采集,如还要采重新采集请先删除本文件...');
		fclose($open);
		
		$User = M('user');
		$row = $User->where(array('u_name'=>I('param.u_name')))->find();
		if(!row || !I('param.u_name')){
			exit('<span style="color:Red;">没有找到该会员,无法采集该模型...</span>');
		}
		$uid = $row['id'];
		
		$this->dis('正在遍历文件中....<br />');
		foreach(scandir($route) as $file){//遍历
			$temp = $route."\\".$file;//生成路径
			if(file_exists($temp) && $file!=".." && $file!="." && $file!="\\"){ //只采集当前目录下的所有图片
				$file_msg = $this->is_type($temp,array('jpeg','jpg','png','gif'));//获取图片的类型
				if(!$file_msg){
					continue;
				}
				$suffix = $file_msg['extension'];
				$this->dis('找到贴图:<span>'.$file.'</span>');
				//提取一张缩略图
				$pic_url = $this->file($temp,$suffix,true);//提取缩略图不设宽度即class设有默认缩略宽度以及高度
				if(!$pic_url){
					$this->dis('<span style="color:Red;">贴图:缩略图采集失败,请联系管理...</span><BR />');
					continue;
				}
				
				$pic_url_the = $this->file($temp,$suffix,true,800,800);//贴图内页缩略图
				if(!$pic_url_the){
					$this->dis('<span style="color:Red;">贴图:内页缩略图采集失败,文件复制失败,请联系管理...</span><BR />');
					continue;
				}
				
				$rar_url = $this->file($temp,$suffix);//贴图附件,附件也是图片,第三个参数默认为false则不提取缩略图
				if(!$rar_url){
					$this->dis('<span style="color:Red;">贴图:图片采集失败,文件复制失败,请联系管理...</span><BR />');
					continue;
				}
				
				//本类是GBK编码,插入数据库注意编码
				//启动MYSQL事物
				$msg->startTrans();
				$name = iconv('GBK', 'utf-8', $file);
				$data = array(
					'main_name'		=> iconv('GBK', 'utf-8',$file_msg['filename']),
					'main_key'		=> iconv('GBK', 'utf-8',$file_msg['filename']),
					'main_msg'		=> iconv('GBK', 'utf-8',$file_msg['filename']),
					'main_time'		=> time(),
					'uid'			=> $uid,//会员
					'main_pic'		=> $pic_url[1],
					'main_browse'	=> rand(1000,2000),
					'main_money'	=> 0,//iconv('GBK', 'utf-8', I('param.money'))
				);
				$row1 = $msg->add($data);
				
				$data = array(
					'pid'				=> $row1,
					'main_nnnex'		=> $rar_url[1],
					'main_size'			=> sprintf("%.2f",(filesize($rar_url[0])/1024/1024)).'MB',
					'main_down_number'	=> 0,
				);
				$row2 = $sch->add($data);
				
				$data['pid'] = $row1;
				$data['pic'] = $pic_url_the[1];
				$row4 = $pic->add($data);

				$data['class_id'] = $class_id;
				$data['msg_id'] = $row1;
				$data['appoint'] = 1;
				while ($data['class_id']){//插入数据关系表并向上获取栏目再插入关系
					$row3 = $the->add($data);
					$data['appoint'] = '0';//栏目所属最底级只有1个,插入后应删除
					if(!$row3){
						$msg->rollback();//失败并回滚
						$this->dis('<span style="color:Red;">模型:'.$file.'采集失败</span><BR />');
						break;
					}
					$row = $class->where(array('id'=>$data['class_id']))->find();
					//var_dump($row);exit;
					if($row && $row['class_top'] != '-1'){
						$data['class_id'] = $row['class_top'];
					}else{//跳出循环
						break;
					}
				}
				if($row1 !== false && $row2 !== false && $row3 !== false && $row4 !== false){
					$msg->commit();//成功并提交
					$this->dis('模型:<span>'.$file.'采集完毕</span><BR />');
				}else{
					$msg->rollback();//失败并回滚
					$this->dis('<span style="color:Red;">模型:'.$file.'采集失败</span><BR />');
				}
			}
		}
		$this->dis('当前采集完毕!感谢使用超强采集器,如有疑问请联系管理...');
	}
	
	public function is_type($temp,$type){//返回文件后缀,如果后缀不合法则返回空
		$info = pathinfo($temp);
		foreach ($type as $a){
			if($info['extension'] == $a){
				return $info;
			}
		}
		return false;
	}
}