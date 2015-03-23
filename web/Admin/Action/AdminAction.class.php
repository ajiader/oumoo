<?php
namespace Admin\Action;
use Think\Controller;
class AdminAction extends Controller {
	public function _initialize(){
		if(!session('login') && ACTION_NAME != 'index' && ACTION_NAME != 'verification'){
			$this->error("请先登录后操作",U("Index/index"));
			exit;
		}else if(session('login') && ACTION_NAME == 'index'){
			$this->success("您已登录",U("Index/main"));
			exit;
		}
	}
	public function update_config($config, $config_file = ''){
		!is_file($config_file) && $config_file = APP_PATH . '/Common/Conf/front.php';
		if (is_writable($config_file)) {
			file_put_contents($config_file, "<?php \nreturn " . stripslashes(var_export($config, true)) . ";", LOCK_EX);
			return true;
		} else {
			return false;
		}
	}

}