<?php
namespace Admin\Controller;
use Admin\Action\AdminAction;
class UpController extends AdminAction{
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

    public function big_file(){
        header('Content-type: text/html; charset=utf-8');
        $this->display();
    }

    public function upload()
    {

        #!! IMPORTANT:
#!! this file is just an example, it doesn't incorporate any security checks and
#!! is not recommended to be used in production environment as it is. Be sure to
#!! revise it and customize to your needs.


// Make sure file is not cached (as it happens for example on iOS devices)
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        /*
        // Support CORS
        header("Access-Control-Allow-Origin: *");
        // other CORS headers if any...
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit; // finish preflight CORS requests here
        }
        */

// 5 minutes execution time
        @set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);

// Settings
// $targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        $upFileUrl = '/Uploads/'.date('Y-m-d').'/';
        $targetDir = BASE_PATH.$upFileUrl;
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds


// Create target dir
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
        }

// Get a file name
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];

        } else {
            $fileName = uniqid("file_");
        }

        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
// Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 1;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 2;


// Remove old temp files
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
            }

            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                // If temp file is current file proceed to the next
                if ($tmpfilePath == "{$filePath}.part") {
                    continue;
                }

                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }


// Open temp file
        if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
            die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }

        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
            }

            // Read binary input stream and append it to temp file
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);

// Check if file has been uploaded
        if (!$chunks || $chunk == $chunks - 1) {
            // Strip the temp .part suffix off
            rename("{$filePath}.part", $filePath);
        }

// Return Success JSON-RPC response
        $jsonFilePath = $upFileUrl . $fileName;
        die('{"jsonrpc" : "2.0", "result" : "'.$jsonFilePath.'", "id" : "id"}');
    }
}