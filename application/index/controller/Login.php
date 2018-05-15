<?php
namespace app\index\controller;
use think\Controller;
use think\request;
use think\captcha\Captcha;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use think\Db;
use think\Cookie;

class Login extends Controller
{
    /**
     * [login description]
     * @return [type] [description]
     */
    public function login()
    {
        if(request()->isPost()){
            $data = request()->param();
            $code = $data['code'];
            //验证码验证
            if(!captcha_check($code)) {
                $res['result'] = 'codeerror';
            }else{
                $u_name = $data['u_name'];
                $u_pwd = $data['u_pwd'];
                $arr = Db("user")->where("`u_name` = '$u_name' and `u_pwd` = '$u_pwd'")->find();
                if($arr){
                    Cookie::set('user',$data);
                    $res['result'] = 'success';
                }else{
                    $res['result'] = 'usererror';
                }

            }
            return json($res);
        }else{
            return view();
        }
    }

    /**
     * [registerAdd description]
     * @return [type] [description]
     */
    public function registerAdd()
    {
        $data=$_POST;
        $data['u_pwd'] = md5($data['u_pwd']);
        $u_name = $data['u_name'];
        $u_email = $data['u_email'];
        $real_name = $data['real_name'];
        //验证码验证
        if(!captcha_check($data['rcode'])) {
            // 校验失败
            $res['result'] = 06;
        }else{
            $arr = Db("user")->where("u_name='$u_name'")->select();
            if($arr){
                $res['result'] = 04;
            }else{
                // $code = rand(1000,9999);
                // $email = $data['u_email'];
                // $u_name = $data['u_name'];
                // $subject='QQ邮件发送测试';
                // $content = "<b>这是你的验证码：</b>".$code;
                // if(send_mail($email,$u_name,$subject,$content)){
                //     echo 1;die;
                // }
                // die;
                $reg_time = $data['reg_time'] = time();
                $active_code = md5($u_name+$reg_time);
                unset($data['rcode']);
                $brr = Db("user")->insert($data);
                if($brr){
                    // //邮件标题
                    // $subject='请激活您的账户';

                    // //邮件内容
                    // $content="<h1>恭喜你，注册成功。</h1><a href='http://localhost/public/index/active/active/activecode/".$active_code."'>点击激活</a>";
                    // // print_r($content);
                    // //$content = '';
                    // //如果页面打印bool(true)则发送成功
                    // if(send_mail($u_email,$real_name,$subject,$content)){
                    //     // echo 1;
                    //     $res['result'] = 07;
                    // }
                    $res['result'] = 00;
                }
            }
        }
        return json($res);
    }

    //发送邮箱验证码
    public function email($toemail)
    {
        // $toemail = 'x@qq.com';//定义收件人的邮箱

            $mail = new PHPMailer();

            $mail->isSMTP();// 使用SMTP服务
            $mail->CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
            $mail->Host = "smtp.163.com";// 发送方的SMTP服务器地址
            $mail->SMTPAuth = true;// 是否使用身份验证
            $mail->Username = "l763784406@163.com";// 发送方的163邮箱用户名，就是你申请163的SMTP服务使用的163邮箱</span><span style="color:#333333;">
            $mail->Password = "763784406l";// 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！</span><span style="color:#333333;">
            $mail->SMTPSecure = "ssl";// 使用ssl协议方式</span><span style="color:#333333;">
            $mail->Port = 994;// 163邮箱的ssl协议方式端口号是465/994

            $mail->setFrom("l763784406@163.com","李阳");// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示
            $mail->addAddress($toemail,'Wang');// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
            $mail->addReplyTo("l763784406@163.com","Reply");// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址
            //$mail->addCC("xxx@163.com");// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址(这个人也能收到邮件)
            //$mail->addBCC("xxx@163.com");// 设置秘密抄送人(这个人也能收到邮件)
            //$mail->addAttachment("bug0.jpg");// 添加附件


            $mail->Subject = "这是一个测试邮件";// 邮件标题
            $mail->Body = "邮件内容是 <b>您的验证码是：123456</b>，哈哈哈！";// 邮件正文
            //$mail->AltBody = "This is the plain text纯文本";// 这个是设置纯文本方式显示的正文内容，如果不支持Html方式，就会用到这个，基本无用

            if(!$mail->send()){// 发送邮件
                echo "Message could not be sent.";
                echo "Mailer Error: ".$mail->ErrorInfo;// 输出错误信息
            }else{
                echo '发送成功';
            }
        }

 }