<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/5/17 11:27
 */
namespace admin\common\controller;

//use axios\tpr\service\RedisService;
use think\Request;
use think\Cache;
use think\Env;

class HomeLogin extends HomeBase{
    protected $user;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);

        if(!is_user_login()){
            $this->redirect("user/login/index");
        }else{
            $this->user = user_info();
            $this->assign('user',$this->user);
        }

        /***
         * redis token 单点登录
         * 因为有很多人不会用redis
         * 所以这段用redis进行token判断的代码暂时去掉
         ***/
        $token_key = "admin_login_token".$this->user['username'];
        $token = Cache::get($token_key,'');

        if($token!=$this->user['token']){
            $this->error("您的账号已在其它地方登陆",url("user/login/logout"));
        }else{
            $expire = intval(Env::get('web.token',172800));
            Cache::set($token_key,$token,$expire);
        }
    }
}