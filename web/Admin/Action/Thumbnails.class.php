<?php
class Thumbnails{
	private $imgSrc; //图片的路径
	private $saveSrc; //图片的保存路径，默认为空
	private $canvasWidth; //画布的宽度
	private $canvasHeight; //画布的高度
	private $im; //画布资源
	private $dm; //复制图片返回的资源

	/**
	 * 初始化类，加载相关设置
	 *
	 * @param $imgSrc 需要缩略的图片的路径
	 * @param $canvasWidth 缩略图的宽度
	 * @param $canvasHeight 缩略图的高度
	 */
	public function __construct($imgSrc,$canvasWidth,$canvasHeight,$saveSrc=null)
	{
		$this->imgSrc = $imgSrc;
		$this->canvasWidth = $canvasWidth;
		$this->canvasHeight = $canvasHeight;
		$this->saveSrc = $saveSrc;
	}

	/**
	 * 生成缩略图
	 */
	public function produce()
	{
		$this->createCanvas();
		$this->judgeImage();
		$this->copyImage();
		$this->headerImage();
	}

	/**
	 * 获取载入图片的信息
	 *
	 * 包含长度、宽度、图片类型
	 *
	 * @return array 包含图片长度、宽度、mime的数组
	 */
	private function getImageInfo()
	{
		return getimagesize($this->imgSrc);
	}

	/**
	 * 获取图片的长度
	 *
	 * @return int 图片的宽度
	 */
	public function getImageWidth()
	{
		$imageInfo = $this->getImageInfo();
		return $imageInfo['0'];
	}

	/**
	 * 获取图片高度
	 *
	 * @return int 图片的高度
	 */
	public function getImageHeight()
	{
		$imageInfo = $this->getImageInfo();
		return $imageInfo['1'];
	}

	/**
	 * 获取图片的类型
	 *
	 * @return 图片的mime值
	 */
	public function getImageMime()
	{
		$imageInfo = $this->getImageInfo();
		return $imageInfo['mime'];
	}

	/**
	 * 创建画布
	 *
	 * 同时将创建的画布资源放入属性$this->im中
	 */
	private function createCanvas()
	{
		$size = $this->trueSize();
		$this->im = imagecreatetruecolor($size['width'],$size['height']);
	}

	/**
	 * 判断图片的mime值，确定使用的函数
	 *
	 * 同时将创建的图片资源放入$this->dm中
	 */
	private function judgeImage()
	{
		$mime = $this->getImageMime();
		switch ($mime)
		{
			case 'image/png':$dm = imagecreatefrompng($this->imgSrc);
			break;

			case 'image/gif':$dm = imagecreatefromgif($this->imgSrc);
			break;

			case 'image/jpg':$dm = imagecreatefromjpeg($this->imgSrc);
			break;

			case 'image/jpeg':$dm = imagecreatefromjpeg($this->imgSrc);//imagecreatefromgjpeg
			break;
		}
		$this->dm = $dm;
	}

	/**
	 * 判断图片缩略后的宽度和高度
	 *
	 * 此宽度和高度也作为画布的尺寸
	 *
	 * @return array 图片经过等比例缩略之后的尺寸
	 */
	public function trueSize()
	{
		$proportionW = $this->getImageWidth() / $this->canvasWidth;
		$proportionH = $this->getImageHeight() / $this->canvasHeight;

		if( ($this->getImageWidth() < $this->canvasWidth) && ($this->getImageHeight() < $this->canvasHeight) )
		{
			$trueSize = array('width'=>$this->getImageWidth(),'height'=>$this->getImageHeight());
		}
		elseif($proportionW >= $proportionH)
		{
			$trueSize = array('width'=>$this->canvasWidth,'height'=>$this->getImageHeight() / $proportionW);
		}
		else
		{
			$trueSize = array('width'=>$this->getImageWidth() / $proportionH,'height'=>$this->canvasHeight);
		}
		return $trueSize;
	}

	/**
	 * 将图片复制到新的画布上面
	 *
	 * 图片会被等比例的缩放，不会变形
	 */
	private function copyImage()
	{
		$size = $this->trueSize();
		imagecopyresized($this->im, $this->dm , 0 , 0 , 0 , 0 , $size['width'] , $size['height'] , $this->getImageWidth() , $this->getImageheight());
	}

	/**
	 * 将图片输出
	 *
	 * 图片的名称默认和原图片名称相同
	 *
	 */
	public function headerImage()
	{
		$position = strrpos($this->imgSrc,'/');
		$imageName = substr($this->imgSrc,($position + 1));
		if($this->saveSrc)
		{
			$imageFlode = $this->saveSrc.'/';
		}
		else
		{
			$imageFlode = substr($this->imgSrc,0,$position).'/small/';
		}
		if(!file_exists($imageFlode))
		{
			mkdir($imageFlode);
		}
		$saveSrc = $imageFlode.$imageName;
		imagejpeg($this->im,$saveSrc,100);
	}
}