<?php
//require dirname(__FILE__) . '/Wx.conf.php';
//define your token
define("TOKEN", "weixin");



class File_Image extends BaseController{

    public function _init() {
        $this->image = new Image();
    }


	public function upload(){

		$view = $this->_getView();
		//$view->assign('retcode',$retcode);
		$view->render('upload')   ;

	}

    public function save(){
        $ret=$this->image->imageSave();
        echo urldecode($ret);

    }

}



?>