<?php
namespace app\admin\controller;
use think\Controller;
use think\request;
use think\Db;
use think\Cookie;
use extend\phpmail\Phpmail;

class Index extends Controller
{
    public function index()
    {
        return view();
    }

    public function register()
    {
        if(request()->isPost()){
            $data = request()->param();
            $arr = Db("register")->where("u_name = {$data['u_name']}")->select();
            if($arr){
                return $this->error("该用户已注册");
            }else{
                $code = rand(1000,9999);
                $email = $data['email'];
                $u_name = $data['u_name'];
                $subject='QQ邮件发送测试';
                $content = "<b>这是你的验证码：</b>".$code;
                if(send_mail($email,$u_name,$subject,$content)){
                    echo 1;die;
                }

                    // $res = Db("register")->insert($data);

            }
        }else{
            return view();
        }
    }
}
