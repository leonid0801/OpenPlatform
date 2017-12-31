<?php
//require dirname(__FILE__) . '/Wx.conf.php';
//define your token
define("TOKEN", "weixin");



class File_Image extends BaseController{

    public function _init() {
        $this->image = new Image();
    }


	public function upload(){
        $this->logs->msg(print_r($_COOKIE,1), __FILE__, __LINE__);

		$view = $this->_getView();
		//$view->assign('retcode',$retcode);
		$view->render('upload')   ;

	}

    public function save(){
        $this->logs->msg(print_r($_COOKIE,1), __FILE__, __LINE__);
        $ret=$this->image->imageSave($_COOKIE);
        echo urldecode($ret);

    }

}



?>