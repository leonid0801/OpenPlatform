<?php

class Item_Feeds extends BaseController{
    public function _init() {
        $this->item = new Item();
        $this->user = new User();
    }


    public function detail(){
        $ret = $this->item->getItem($_GET);
        $view = $this->_getView();
        $view->assign('ret',$ret);
        $view->render('node')   ;
    }


}