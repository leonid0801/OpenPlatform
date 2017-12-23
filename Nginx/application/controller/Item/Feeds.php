<?php

class Item_Feeds extends BaseController{
    public function _init() {
        $this->item = new Item();
        $this->user = new User();
    }

    public function index(){
        $view = $this->_getView();
        //$view->assign('retcode',$retcode);
        $view->render('index')   ;
    }

    public function detail(){
        $ret = $this->item->getItem($_GET);
        $view = $this->_getView();
        $view->assign('ret',$ret);
        $view->render('node')   ;
    }

    public function login(){


        //$this->logs->msg(json_encode($_SESSION), __FILE__, __LINE__);

        $view = $this->_getView();

        $ret = $this->user->login($_POST);
        var_dump($_POST);
        if ($ret){
            $view->render('index')   ;
        }else{
            $view->assign('ret',$ret);
            $view->render('login')   ;
        }
    }
}