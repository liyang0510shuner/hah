<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Cookie;
use think\cache\driver\Redis;
class Index extends Controller
{
    public function index()
    {
	$re=Db::query('select * from vio');
     // $this->view->engine->layout(true);
      return $this->fetch('index',['re'=>$re]);

    }
    //古北水镇图片更多展示页面
    public function poto()
    {
    	 return view('poto');
    }
    //美食展示页面
    public function potot()
    {
    	return view('potot');
    }
    //景区简介
    public function about()
    {
        $arr = Db("scenic_introduce")->select();
    	return view('about',['arr'=>$arr]);
    }
     //景区风光
    public function about2()
    {
    	return view('about2');
    }
     //景区攻略
    public function about3()
    {
        $arr = Db("scenic_guide")->select();
        // print_r($arr);die;
    	return view('about3',['arr'=>$arr]);
    }
    //酒店预订页面
    public function cases()
    {
    	return view('cases');
    }
    //最新资讯
    public function caseshow()
    {
    	return view('caseshow');
    }
    //联系我们
    public function contact()
    {
    	return view('contact');
    }
    //近期活动
    public function gonglue()
    {
    	return view('gonglue');
    }
    //近期活动详情页面
    public function gonglueshow()
    {
    	return view('gonglueshow');
    }
    //关于我们
    public function huanjing()
    {
    	return view('huanjing');
    }
    //友情链接
    public function lianjie()
    {
    	return view('lianjie');
    }
    //门票预订页
    public function taocan()
    {

        $arr = Db("ticket")->paginate(4);
        // print_r($arr);
        $page = $arr->render();
    	return view('taocan',['arr'=>$arr,'page'=>$page]);
    }
    //套餐详情页
    public function taocanshow()
    {
        $t_id = input('t_id');
        //计算网页访问量
        $u_name = cookie('user');
        $data['i_ip'] = $_SERVER['REMOTE_ADDR'];
        $data['i_time'] = time();
        $ip = Db("ip")->where("i_ip",$data['i_ip'])->find();
        $res = Db("user")->where("u_name",$u_name['u_name'])->value("u_id");
        $data['u_id']  = $res;
        $data['t_id'] = $t_id;
        if($ip['i_ip'] != $data['i_ip']){
            Db("ip")->insert($data);
        }

        // $write = $remote . '|' . date('Y-m-d H:i:s');
        // $str = file_get_contents("record.txt");
        // $clickcount = 1;
        // if($str){
        //     $rows = explode("\r\n",$str);
        //     $count = count($rows) + 1;
        //     foreach($rows as $value){
        //         $ip = explode("|",$value);
        //         if($ip[0] != $remote){
        //             $clickcount++;
        //         }
        //     }
        //     $write = "\r\n" . $write;
        // }else{
        //     $count = 1;
        // }
        // $a = file_put_contents('record.txt',$write,FILE_APPEND);

        $ip = Db("ip")->where("t_id",$t_id)->count();
        $arr = Db("ticket")->where("t_id",$t_id)->find();
        $brr = Db::table('ticket')->where('t_id','<',$t_id)->where('t_status',1)->order('t_id desc')->limit(1)->find();
        if(empty($brr)){
            $brr['t_id'] = '';
            $brr['t_name'] = '已经是第一篇啦';
        }

        $crr = Db::table('ticket')->where('t_id','>',$t_id)->where('t_status',1)->order('t_id asc')->limit(1)->find();
        if(empty($crr)){
            $crr['t_id'] = '';
            $crr['t_name'] = '已经是最后一篇啦';
        }
    	return view('taocanshow',['arr'=>$arr,'brr'=>$brr,'crr'=>$crr,'ip'=>$ip]);
    }
    //友情赞助
    public function yangzhi()
    {
    	return view('yangzhi');
    }
    //切片上传
    public function vio()
    {
        return view('vio');
    }
      public function upload()
    {
              // Make sure file is not cached (as it happens for example on iOS devices)
        header("Access-Control-Allow-origin:*");
        //header("Access-Control-Allow-Credentials:true");
        //header('Access-Control-Allow-Headers:x-requested-with,content-type');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");


        // Support CORS
        // header("Access-Control-Allow-Origin: *");
        // other CORS headers if any...
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit; // finish preflight CORS requests here
        }


        if ( !empty($_REQUEST[ 'debug' ]) ) {
            $random = rand(0, intval($_REQUEST[ 'debug' ]) );
            if ( $random === 0 ) {
                header("HTTP/1.0 500 Internal Server Error");
                exit;
            }
        }
        //
        //var_dump($_REQUEST);
        //
        // header("HTTP/1.0 500 Internal Server Error");
        // exit;


        // 5 minutes execution time
        @set_time_limit(5 * 60);

        $targetDir = 'upload_tmp';   //切片保留路径
        $uploadDir = 'upload';       //最终上传路径

        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds


        // Create target dir
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
        }

        // Create target dir
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir);
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
        $uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;


        // Remove old temp files
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
            // echo '123';
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id","uploadPath":$uploadPath}');
            }

            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                // If temp file is current file proceed to the next
                if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
                    continue;
                }

                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }


        // Open temp file
        if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
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

        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");

        $index = 0;
        $done = true;
        for( $index = 0; $index < $chunks; $index++ ) {
            if ( !file_exists("{$filePath}_{$index}.part") ) {
                $done = false;
                break;
            }
        }
        if ( $done ) {
            if (!$out = @fopen($uploadPath, "wb")) {
              // echo '1';
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            }

            if ( flock($out, LOCK_EX) ) {
                for( $index = 0; $index < $chunks; $index++ ) {
                    if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
                        break;
                    }

                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }

                    @fclose($in);
                    @unlink("{$filePath}_{$index}.part");
                }

                flock($out, LOCK_UN);
            }
            @fclose($out);
        }

        // Return Success JSON-RPC response
        //echo ('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');


            //查询当前是否已经入库过数据
             $arr = @Db('vio')->where('vio',"$uploadPath")->find();
             // print_r($arr);
             if($arr)
             {
               echo ('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
             }
             else
             {
               $res=Db('vio')->insert(['vio'=>$uploadPath]);
             }

    }


}
