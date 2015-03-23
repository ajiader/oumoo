<?php
// 本类由系统自动生成，仅供测试用途
namespace Verify\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function index(){
		$Verify =     new \Think\Verify();
		$Verify->fontSize = 13;
		$Verify->length   = 4;
		$Verify->useNoise = false;
		$Verify->useCurve = false;
		$Verify->imageW = 96;
		$Verify->imageH = 30;
		$Verify->entry();
	}
     
}


