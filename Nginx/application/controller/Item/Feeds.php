<?php

class Item_Feeds extends BaseController{
    public function index(){
        $view = $this->_getView();
        //$view->assign('retcode',$retcode);
        $view->render('index')   ;
    }

    public function test(){
        $view = $this->_getView();
        //$view->assign('retcode',$retcode);
        $view->render('node')   ;
    }
}