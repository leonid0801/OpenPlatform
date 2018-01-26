<?php
/**
 * Created by JetBrains PhpStorm.
 * User: leo
 * Date: 17-11-25
 * Time: 下午12:19
 * To change this template use File | Settings | File Templates.
 */

class Action{

    public function __construct( ) {
        $this->logs = LOGS::getInstance();
        $this->_init();
    }

    public function _init() {
        $this->userModel = new userModel("t_user");
        $this->utils=new Utils();

    }

    private  function slatAdded($password) {
        $slat = "YdN8";
        return ( $password . $slat);
    }

    public function login($get_info){
        $this->logs->msg(json_encode($get_info), __FILE__, __LINE__);
        if (!array_key_exists('usermobile', $get_info)
            or !array_key_exists('password', $get_info)
            or !array_key_exists('uid', $get_info)
        ){
            return False;
        }else{
            //session_start();
            $uid=$get_info['uid'];

            $usermobile=$get_info['usermobile'];
            $password=$get_info['password'];

            $useRet = $this->userModel->getUserByMobile($usermobile);
            $use_id=$useRet[0]['f_uid'];

            if (array_key_exists('remember', $get_info)){
                $remember=$get_info['remember'];
                if ($remember=='on'){
                    //var_dump(session_id());
                    //$sessionpath = session_save_path();
                    //echo $sessionpath;

                    //$password=$this->slatAdded($password);
                    //$_SESSION['usermob']=$usermobile;
                    setcookie("uid",$use_id,time()+90*3600*24,'/');
                    setcookie("usermobile",$usermobile,time()+90*3600*24,'/');
                    setcookie("password",$password,time()+90*3600*24,'/');
                    //var_dump($_COOKIE);
                    //var_dump($_SESSION);
                }
                return True;
            }else{

                setcookie("uid",$use_id,time()-1);
                setcookie("usermobile",$usermobile,time()-1);
                setcookie("password",$password,time()-1);
                return False;

            }

        }

    }

    public function sign_up($get_info){
        //$this->logs->msg(json_encode($get_info), __FILE__, __LINE__);
        $ret = Array();
        if (!array_key_exists('usermobile', $get_info)
            or !array_key_exists('username', $get_info)
            or !array_key_exists('password', $get_info)
            or !array_key_exists('rpassword', $get_info)
        ){
            $ret['code'] = 1;
            $ret['msg'] = 'failed';
            $ret['data'] = '';
            return $ret;
        }

        $username = $get_info['username'];
        $mobile = $get_info['usermobile'];
        $passwd = $get_info['password'];
        $rpasswd = $get_info['rpassword'];

        if ($passwd != $rpasswd){
            $ret['code'] = 1;
            $ret['msg'] = 'failed';
            $ret['data'] = '';
            return $ret;
        }

        $userArr = Array(
          'f_nickname' => $username,
          'f_tel_num' => $mobile,
          'f_openid' => $mobile, // because of duplicate key： f_openid
          'f_passwd' => $passwd
        );

        $dbRet = $this->userModel->insertInfo($userArr);
        $useRet = $this->userModel->getUserByMobile($mobile);
        if (false==$dbRet){
            $ret['code'] = 1;
            $ret['msg'] = 'failed';
            $ret['data'] = '';
        }else{

            if (count($useRet)<1){
                $ret['code'] = 1;
                $ret['msg'] = 'Get uid failed';
                $ret['data'] = '';
                return $ret;
            }else{
                $ret['code'] = 0;
                $ret['msg'] = 'add successed';
                $ret['data'] = Array('usermobile' => $mobile, 'password'=>$passwd, 'uid'=>$useRet[0]['f_uid']);

            }

        }
        return $ret;

    }





}