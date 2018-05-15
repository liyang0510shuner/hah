<?php
/**
 * @Author: Marte
 * @Date:   2018-05-11 19:41:54
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-05-11 19:49:05
 */
namespace app\index\controller;
use think\request;
use app\index\model\User;
class Active
{

    function active(){
        //此加密串为user_id+reg_time的md5
        //获取邮件请求中的激活加密串
        $active_code =  request()->param('activecode');

        if (!($user = User::get(['active_code' => $active_code]))) {
            echo "用户不存在！";
            exit;
        }else {
            //检查用户激活时间，把用户表中注册字段与当前时间进行对比，超过30分钟即为过期
            if (((time()-$user->reg_time)/60)>30) {
                echo "激活邮件已经过期！";
            }
            //检查用户是否已经激活，用户表设置一个字段标识是1否0已经激活
            if ($user->status == 1) {
                echo "该用户已激活！";
                exit;
            }
            //把此加密串对应的user的user_id与reg_time进行md5并与此加密串进行对比
            if ($active_code == $user->active_code) {
                $user->active = 1;
                if ($user->save()) {

                    echo "激活成功！";
                }
            }
        }
    }
}