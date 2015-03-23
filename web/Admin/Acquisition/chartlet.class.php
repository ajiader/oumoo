<?php
/**
 *��������
 *������windows�²��Ա����������ļ��Լ��ļ�����GBK����
 *������linux�±���������ȴ��UTF-8
 *
 *Ϊ�˱����ļ����ļ���,�������Ҫ��!���������������UTF-8����ѱ������ΪUTF-8��Ŷ
 *ע����ʾ���û���ҲҪע�����
 *
 *����mysqlǰӦע�����
 *��ʾ���û���Ӧע�����
 * 
 */
namespace Admin\Acquisition;
use Admin\Acquisition\Acquisition;
class Model extends Acquisition{
	public function init($class_type){
		$msg = M('msg_main');				//����
		$sch = M('msg_main_'.$class_type);	//����
		$the = M('msg_class');				//��ϵ��
		$class = M('class_name');				//��Ŀ��
		$pic = M('msg_main_pic');			//����ֱ��ͼƬ����
		
		ignore_user_abort();
		set_time_limit(0);
		
		header("Content-type: text/html; charset=GBK");
		$route = iconv('UTF-8', 'GBK', I('param.route'));
		$class_id = I('param.class_id');
		$main_style = I('param.main_style');
		
		$this->code('GBK');
		$this->dis('����������,���Ժ�!�����ɼ�������ˢ�»�ر�,��ʹ�Ͽ�����Ҳ��ɼ����....');
		
// 		if(file_exists($route."\\Acquisition.txt")){
// 			exit('<span>��ģ���ѱ��ɼ�,�绹Ҫ�ɼ�!����ɾ��'.$route."\\Acquisition.txt"."���ٽ��вɼ�</span>");
// 		}
		
		if(file_exists($route."\\Acquisition.txt")){
			exit('<span style="color:Red;">�Ѿ��ɼ���,�ظ��ɼ�����ɾ��Ŀ¼�µ�Acquisition.txt�ļ�...</span>');
		}
		$open=fopen($route."\\Acquisition.txt","a" );
		if(!$open){
			exit('<span style="color:Red;">PHPû��Ȩ�޶Ը��ļ����в���,������ȨPHP���Ȩ��...</span>');
		}
		fwrite($open,'�Ѳɼ�,�绹Ҫ�����²ɼ�����ɾ�����ļ�...');
		fclose($open);
		
		$User = M('user');
		$row = $User->where(array('u_name'=>I('param.u_name')))->find();
		if(!row || !I('param.u_name')){
			exit('<span style="color:Red;">û���ҵ��û�Ա,�޷��ɼ���ģ��...</span>');
		}
		$uid = $row['id'];
		
		$this->dis('���ڱ����ļ���....<br />');
		foreach(scandir($route) as $file){//����
			$temp = $route."\\".$file;//����·��
			if(file_exists($temp) && $file!=".." && $file!="." && $file!="\\"){ //ֻ�ɼ���ǰĿ¼�µ�����ͼƬ
				$file_msg = $this->is_type($temp,array('jpeg','jpg','png','gif'));//��ȡͼƬ������
				if(!$file_msg){
					continue;
				}
				$suffix = $file_msg['extension'];
				$this->dis('�ҵ���ͼ:<span>'.$file.'</span>');
				//��ȡһ������ͼ
				$pic_url = $this->file($temp,$suffix,true);//��ȡ����ͼ�����ȼ�class����Ĭ�����Կ���Լ��߶�
				if(!$pic_url){
					$this->dis('<span style="color:Red;">��ͼ:����ͼ�ɼ�ʧ��,����ϵ����...</span><BR />');
					continue;
				}
				
				$pic_url_the = $this->file($temp,$suffix,true,800,800);//��ͼ��ҳ����ͼ
				if(!$pic_url_the){
					$this->dis('<span style="color:Red;">��ͼ:��ҳ����ͼ�ɼ�ʧ��,�ļ�����ʧ��,����ϵ����...</span><BR />');
					continue;
				}
				
				$rar_url = $this->file($temp,$suffix);//��ͼ����,����Ҳ��ͼƬ,����������Ĭ��Ϊfalse����ȡ����ͼ
				if(!$rar_url){
					$this->dis('<span style="color:Red;">��ͼ:ͼƬ�ɼ�ʧ��,�ļ�����ʧ��,����ϵ����...</span><BR />');
					continue;
				}
				
				//������GBK����,�������ݿ�ע�����
				//����MYSQL����
				$msg->startTrans();
				$name = iconv('GBK', 'utf-8', $file);
				$data = array(
					'main_name'		=> iconv('GBK', 'utf-8',$file_msg['filename']),
					'main_key'		=> iconv('GBK', 'utf-8',$file_msg['filename']),
					'main_msg'		=> iconv('GBK', 'utf-8',$file_msg['filename']),
					'main_time'		=> time(),
					'uid'			=> $uid,//��Ա
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
				while ($data['class_id']){//�������ݹ�ϵ�����ϻ�ȡ��Ŀ�ٲ����ϵ
					$row3 = $the->add($data);
					$data['appoint'] = '0';//��Ŀ������׼�ֻ��1��,�����Ӧɾ��
					if(!$row3){
						$msg->rollback();//ʧ�ܲ��ع�
						$this->dis('<span style="color:Red;">ģ��:'.$file.'�ɼ�ʧ��</span><BR />');
						break;
					}
					$row = $class->where(array('id'=>$data['class_id']))->find();
					//var_dump($row);exit;
					if($row && $row['class_top'] != '-1'){
						$data['class_id'] = $row['class_top'];
					}else{//����ѭ��
						break;
					}
				}
				if($row1 !== false && $row2 !== false && $row3 !== false && $row4 !== false){
					$msg->commit();//�ɹ����ύ
					$this->dis('ģ��:<span>'.$file.'�ɼ����</span><BR />');
				}else{
					$msg->rollback();//ʧ�ܲ��ع�
					$this->dis('<span style="color:Red;">ģ��:'.$file.'�ɼ�ʧ��</span><BR />');
				}
			}
		}
		$this->dis('��ǰ�ɼ����!��лʹ�ó�ǿ�ɼ���,������������ϵ����...');
	}
	
	public function is_type($temp,$type){//�����ļ���׺,�����׺���Ϸ��򷵻ؿ�
		$info = pathinfo($temp);
		foreach ($type as $a){
			if($info['extension'] == $a){
				return $info;
			}
		}
		return false;
	}
}