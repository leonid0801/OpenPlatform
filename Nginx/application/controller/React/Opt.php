<?php
/**
 * Created by JetBrains PhpStorm.
 * User: leo
 * Date: 17-12-23
 * Time: 下午1:40
 * To change this template use File | Settings | File Templates.
 */

class React_Opt extends BaseController{
    public function _init() {
        $this->action= new Action();
        $this->items=new Items();
    }



    public function login(){

        //$this->logs->msg(json_encode($_SESSION), __FILE__, __LINE__);

        $view = $this->_getView();
        $ret = $this->action->login($_POST);
        if ($ret){
            $view->render('index')   ;
        }else{
            $view->assign('ret',$ret);
            $view->render('login')   ;
        }
    }

    public function sign_up(){
        //$this->logs->msg(json_encode($_SESSION), __FILE__, __LINE__);
        $view = $this->_getView();
        $ret = $this->action->sign_up($_POST);

        if ($ret['code']==0){
            $uid=$ret['data']['uid'];
            $usermobile=$ret['data']['usermobile'];
            $passwd=$ret['data']['password'];
            setcookie("uid",$ret['data']['uid'],time()+3600);
            setcookie("usermobile",$ret['data']['usermobile'],time()+3600);
            setcookie("password",$ret['data']['password'],time()+3600);

            //$uid = isset($_COOKIE['uid'])?$_COOKIE['uid']:'';
            //$usermobile = isset($_COOKIE['usermobile'])?$_COOKIE['usermobile']:'';
            //$passwd = isset($_COOKIE['password'])?$_COOKIE['password']:'';
            $userArr = Array('uid'=>$uid,'usermobile'=>$usermobile,'password'=>$passwd);
            $view->assign('userArr',$userArr);
            $view->render('login')   ;
        }else{
            $view->assign('ret',$ret);
            $view->render('signup');
        }
    }

    public function newitem(){
        $ret=$this->items->newItem($_POST);
        echo json_encode($ret);
    }
}