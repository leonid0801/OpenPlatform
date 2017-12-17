<?php

class Item_Feeds extends BaseController{
    public function _init() {
        $this->item = new Item();
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

        $view = $this->_getView();

        $view->render('login')   ;
    }
}