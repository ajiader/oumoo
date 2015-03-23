<?php 

function get_sliders($id){
	$class = M('msg_main');
	$row = $class->join('left join msg_main_pic ON msg_main.id = msg_main_pic.pid')->where('msg_main.main_slider > 0')->order('msg_main.main_slider_time DESC')->select();
	return $row;
}

function get_model_rand($id,$limit=10,$new=false){//随机推荐!$id=栏目id,$limit=取多少个,$new最新多少个中取
	$data = M('msg_class');
	$join = 'left join msg_main ON msg_main.id = msg_class.msg_id';
	$temp = $data->join($join)->where(array('msg_class.class_id'=>$id))->order('msg_class.msg_id desc')->find();
	$max = $temp['msg_id'];
	$min = $new ? $max-$new:1;
	$record = array();	//生成的id记录,不重复去数据库查询该id
	$arr = array();		//查询mysql数据,查询出一行记录下来满足条件并返回
	$time = time();		//时间限制,超出时间直接返回出去!指定时间内找不到足够的数据
	$count = $data->join($join)->where(array('msg_class.class_id'=>$id,'msg_main.main_examine'=>1))->count();
	
	if($limit*2 > $count){//如果返回的数量超过数据库的一半,那么没必要再取随机,直接返回最新
		$row = $data->join($join)->where(array('msg_class.class_id'=>array('eq',$id),'msg_main.main_examine'=>array('eq',1)))->order('msg_class.msg_id desc')->limit($limit)->select();
		return $row;		
	}
	
	/*
	 * 该代码经测试!mysql的数据再大,远远快于 ORDER BY RAND()!
	 * 该代码需要随机多少条则去数据库查询多少次即可
	 * 
	 * $min=默认最大id减去$new等于最小id,在rand(最大id,最小id)中找
	 * 那么$min-$max没有数据,则搜索数据库比$min小的id(那么这样产生的就不是随机数)
	 * 如果不设置$new那么会去数据库全部搜索,则是随机的
	 * 把查询的id放入数组做键值,相当于放在索引上!遍历不重复随机数速度大大提升
	 * 
	 * 每次生成的id以及查询到id和这之间的id将被存放起来,不再重复生成
	 * */
	while ($max){
		if(time() - $time > 1){//1秒
			return $arr;//超出时间限制返回数据
		}
		$rand = rand($max,$min);
		if($record[$rand]){
			if($max == $min){
				return $arr;//存在该id且min和max一样!数据库内容已搜寻完,不再循环!这里搜寻完毕条件成立 limit条件必会成立
			}
			continue;//已有记录,重新获取
		}
 		$row = $data->join($join)->where(array('msg_class.msg_id'=>$rand,'msg_class.class_id'=>$id,'msg_main.main_examine'=>1))->limit(1)->find();
//  	echo($data->getLastSql());
 		if($row){//mysql中有记录则插入!
 			array_push($arr,$row);
 		}else if($min > 1){//没有找到记录,扩大搜索范围
 			$min --;
 		}
 		$record[$rand] = 'ok';
		/*以上代码查询mysql快,但是产生的随机数id查询mysql不存在,那么将再产生随机数再查询!缺点就是查询mysql次数多*/
 		
 		
 		/*下面代码完善,逻辑来说处理速度更快!查询mysql更少,但是 因为 <= MYSQL执行非常慢,不采用*/
// 		$row = $data->join($join)->where(array('msg_main.id'=>array('elt',$rand),'msg_class.class_id'=>$id,'msg_main.main_examine'=>1))->order('msg_class.msg_id desc')->limit(1)->find();
// 		if($row){//mysql中有记录则插入!
// 			//(因为查询的是elt <= id,这样将会产生相同的数据,如果相同继续执行缩小范围到当前继续查找)
// 			array_push($arr,$row);
// 		}else if($min > 1){//没有找到比$rand还小的id那么缩小搜索范围!如果数据库为空,那么程序将一直缩小范围到最大id上并结束
// 			$min --;
// 		}
// 		for ($i=$row['id'] ? $row['id'] : $rand; $i<=$rand; $i++){//如果mysql中没有找到$row,那么当前id就是1;小于$rand的id不再取数据
// 			$record[$i] = 'ok';
// 		}
		if(count($arr) >= $limit){
			return $arr;//返回数据
		}
	}
}

function get_url() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

function get_model($id,$c,$order='msg_main.main_time desc'){//获取该栏目下指定的条数据并返回
	$class = M('class_name');			//栏目表
	$msg = M('msg_main');				//主表
	$row = $class->where(array('id'=>$id))->find();
	if(!$row){
		return false;
	}
	$join = 'left join msg_class ON msg_class.msg_id = msg_main.id ';
	$join2 = 'left join msg_main_'.$row['class_type'].' ON msg_main.id = msg_main_'.$row['class_type'].'.pid';
	$where['msg_class.class_id'] = $id;
	$row = $msg->join($join)->join($join2)->where($where)->order($order)->limit(0,$c)->select();//链表查询内容
	return $row;
}



function get_user_msg($name){//获取当前会员信息
	$temp = session('user');
	return $temp[$name];
}

function save_user_session(){//会员信息变动,例如金币!需要更新session
	if(session('uid')){
		session('user',M('user')->where(array('id'=>session('uid')))->find());
	}
}

function get_user_name($name,$is=false){//通过u_name获取会员信息  $is=true返回会员id
	$user = M('user');
	$row = $user->where(array('u_name'=>$name))->find();
	if($is){
		return $row['id'];
	}
	return $row;
}
function get_user_id($id,$is=false){//通过id获取会员信息	$is=true返回会员名
	$user = M('user');
	$row = $user->where(array('id'=>$id))->find();
	if($is){
		return $row['u_name'];
	}
	return $row;
}


/*
 * 模型model 扩展风格
 * */
function get_model_style($id){
	foreach (C('MODEL_EXP') as $a){
		if($a['id'] == $id){
			return $a['name'];
		}
	}
	return '没有找到';
}


function get_pic($id){
	$pic_model = M('msg_main_pic');
	$row = $pic_model->where(array('pid'=>$id))->order('oid asc')->select();
	return array_slice($row,0,10);
}



function get_all_column($id){//获取所有栏目 -1获取所有顶级栏目  1获取栏目id1的所有下级栏目
	$class = M('class_name');
	$row = $class->where(array('class_top'=>$id))->order('c_order asc')->select();
	return $row;
}

function get_number_random($i,$i1){
	return rand($i,$i1);
}

function detection($data='none'){//删除字符串 ,';
	return preg_replace("/([,]|[']|[;])/","",$data);
}

function numeric($i){
	if(is_numeric($i) && $i > 0){
		return $i;
	};
	return 1;
};

function check_verify($code, $id = ''){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/*function admin_login(){//后台是否登录,暂时不用了
	if(session('admin_login')!=cookie('admin_login')||session('admin_login')==""){
		if(ACTION_NAME != "index" && ACTION_NAME != "verification"){
			return -1;//跳转到登录页面
		};
	}else if(ACTION_NAME == "index"){
		return 1;//跳转到登录页面
	};
	return 0;//不执行任何操作
}*/

function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true)
{
	if(function_exists("mb_substr")){
		if ($suffix && strlen($str)>$length)
			return mb_substr($str, $start, $length, $charset)."...";
		else
			return mb_substr($str, $start, $length, $charset);
	}
	elseif(function_exists('iconv_substr')) {
		if ($suffix && strlen($str)>$length)
			return iconv_substr($str,$start,$length,$charset)."...";
		else
			return iconv_substr($str,$start,$length,$charset);
	}
	$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	preg_match_all($re[$charset], $str, $match);
	$slice = join("",array_slice($match[0], $start, $length));
	if($suffix) return $slice."…";
	return $slice;
}

function paiming($i){
	$i = $i/10;
	if($i> 80){
		return '极好';
	}else if($i >=40 && $i <=80){
		return '一般';
	}
	return '较差';
};

function pingfen($i,$i2){//新分,总分
	if($i == 3){
		return $i2;
	};
	$i2 += ($i - 3);
	if($i2 > 1000){
		$i2 = 1000;
	}else if($i2 < 1){
		$i2 = 1;
	};
	return $i2;
};

function top_down($is,$time,$m,$t,$pid){//传入id，传入查询表，传入返回字段
	$data = M($m);
	if($is){
		$is = 'gt';
		$isi = 'asc';
	}else{
		$is = 'lt';
		$isi = 'desc';
	}
	$row = $data->where(array('pid'=>$pid,'time'=>array($is,$time)))->order('time '.$isi)->find();
	if(!$row){
		return '没有了';
	};
	return '<a href="'.U(newsmsg,array('id'=>$row['id'])).'">'.$row[$t].'</a>';	
}

function rc4 ($data){
	$key[] ="";
	$box[] ="";
	$ste=false;
	$str = C('str_you');

	$pwd_length = strlen($str);
	$data_length = strlen($data);

	for ($i = 0; $i < 256; $i++)
	{
	$key[$i] = ord($str[$i % $pwd_length]);
	$box[$i] = $i;
	}

	for ($j = $i = 0; $i < 256; $i++)
	{
	$j = ($j + $box[$i] + $key[$i]) % 256;
	$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for ($a = $j = $i = 0; $i < $data_length; $i++)
	{
	$a = ($a + 1) % 256;
	$j = ($j + $box[$a]) % 256;

	$tmp = $box[$a];
	$box[$a] = $box[$j];
	$box[$j] = $tmp;

	$k = $box[(($box[$a] + $box[$j]) % 256)];
	$ste .= chr(ord($data[$i]) ^ $k);
	}
	 
	return $ste;
}

function trimall($str){//删除空格
	$qian=array(" ","　","\t","\n","\r");
	$hou=array("","","","","");
	return str_replace($qian,$hou,$str);
}

function get_column_type($id=false){//栏目ID获取栏目名称
	$class = M('class_name');
	$row = $class->where(array('id'=>$id))->find();
	return $row['class_name'];
}

function get_class_type($model){//获取栏目类型名称
	/*
	 * 模版中这样调用
	 * {:get_class_type($vo['class_type'])}
	 * */
	foreach (C('CLASS_NAME') as $a){
		if($a['model'] == $model){
			return $a['name'];
		}
	}
}

function t_time($time,$is=true){//$is=true返回年月日 时分秒，否者返回年月日
	if(!$time || $time == 0){
// 		return date('Y:m:d H:i:s');
		return $is ? date('Y:m:d H:i:s') : date('Y-m-d');
	}
	return $is ? date('Y:m:d H:i:s',$time) : date('Y-m-d',$time);
}

function get_c_n($name){
	return C($name);
}

function get_c($name){
	return get_html_str(C($name));
}

function get_html_str($text){
	return  str_replace(array('&lt;', '&gt;', 'amp;','&quot;'), array('<', '>', '','"'), $text);
}

function test_char($var,$count){//检测字符串长度
	if(strlen($var) < $count){
		return false;
	};
	return true;
}

function get_md5_password($var){//MD5加密密码,该规则不可随便更改!否者用户密码将失效
	return MD5(MD5($var).'qiwen');
}

function class_save($type=false,$class_id=0,$msg_id=0){
	
	/*
	 * 该函数成功返回true,失败返回错误代码
	* */
	
	
	//更新栏目,type=指定后!我们要查看栏目的类型是否属于type类型!前台增加内容更新栏目调用
	//非管理后台调用(后台强命名类型不需要再调用,不需要走更多弯路)
	//调用此函数时应先启动MYSQL事物,否者执行了本函数你的数据逻辑更新失败则数据丢失或错误
	//当你开启了MYSQL事物并你的逻辑代码出现错误时则可以使用MYSQL回滚操作
	//本函数只作为更新栏目与数据的关系表的方便之用
	
	if(!$class_id || !$msg_id || !is_numeric($class_id) || !is_numeric($msg_id)){
		return 1;//栏目id或内容id不正确
	}
	$the = M('msg_class');
	$class = M('class_name');
	$row = $class->where(array('id'=>$class_id))->find();
	if(!$row){
		return 2;//栏目不存在
	}
	if($type && $row['class_type'] != $type){//更新的内容和目前插入关系的栏目类型不符合
		return 3;//栏目类型不匹配
	}
	$the->where(array('msg_id'=>$msg_id))->delete();	//删除关系
	$data['msg_id'] = $msg_id;
	$data['class_id'] = $class_id;
	$data['appoint'] = 1;
	while ($data['class_id']){//插入数据关系表并向上获取栏目再插入关系
		$row = $the->add($data);
		$data['appoint'] = '0';//栏目所属最底级只有1个,插入后应删除
		if(!$row){
			return 4;//保存失败
		}
		$row = $class->where(array('id'=>$data['class_id']))->find();
		//var_dump($row);exit;
		if($row && $row['class_top'] != '-1'){
			$data['class_id'] = $row['class_top'];
		}else{//跳出循环
			break;
		}
	}
	return true;
}

function delete_msg($id,$s){//要删除的内容id,指定附表
	if(!$id){
		return false;
	}
	
	$the = M('msg_class');
	$msg = M('msg_main');
	$pic = M('msg_main_pic');
	$sch = M($s);//附表
	
	$row1 = $msg->where(array('id'=>array('in',$id),'uid'=>session('uid')))->delete();
	$row2 = $the->where(array('msg_id'=>array('in',$id)))->delete();
	$row3 = $sch->where(array('pid'=>array('in',$id)))->delete();
	$row4 = $pic->where(array('pid'=>array('in',$id)))->delete();
	
	if($row1 !== false && $row2 !== false && $row3 != false && $row4 !== false){
		$msg->commit();
		return true;
	};
	$msg->rollback();
	return false;
}


function iconvstr($str){//未知编码转换
	$encode = mb_detect_encoding($str, array('ASCII','UTF-8', 'GB2312', 'GBK','BIG5'));
	if ($encode != "UTF-8") { 
		$str = iconv($encode,"UTF-8",$str);
	}
	return $str;
}

function save_money($uid,$money,$name,$msg){//更新会员金钱，$uid=会员id,$money=金钱,$name=更新名称,$msg=具体信息
	
	/*
	 * 该函数成功返回true,失败返回错误代码
	 * */
	
	//调用此函数时应先启动MYSQL事物,否者执行了本函数你的数据逻辑更新失败则数据丢失或错误
	//当你开启了MYSQL事物并你的逻辑代码出现错误时则可以使用MYSQL回滚操作
	//本函数只作为更新会员金币与资金变动记录之方便
	
	
	/*更新会员金币*/
	$user = M('user');
	$row = $user->where(array('id'=>$uid))->find();
	if(!$row){
		return 1;//没找到会员
	}
	if($money == false || !is_numeric($money)){
		return 2;//没有资金变动
	}
	if( ($row['money']+$money) < 0 ){ 
		return 3;//会员支出一定检测是否超金币上限
	}
	if(($user->where(array('id'=>$uid))->save(array('money'=>($row['money']+$money)))) === false){
		return 4;//金币扣除失败
	}
	
	/*更新会员资金变动信息*/
	$user_msg = M('user_msg');
	if(!($user_msg->add(array('uid'=>$uid,'time'=>time(),'money'=>$money,'name'=>$name,'msg'=>$msg)))){
		return 5;//更新资金变动信息失败
	}
	
	return true;//更新成功
}













