<?php
//require dirname(__FILE__) . '/Wx.conf.php';


class User_Profile extends BaseController{
	private $imageModel ;
	
	public function _init() {

        $this->imageModel = new imageModel("t_image");    
       
    }

	public function test(){
	
		$view = $this->_getView();
		//$view->assign('retcode',$retcode);
		$view->render('enroll')   ;
	}
	
	
	public function test1(){
		
		$uid = '';
		if (array_key_exists("uid",$_GET)){
			$uid = $_GET['uid'];
			///echo $uid;
			$view = $this->_getView();
			//$view->assign('retcode',$retcode);
			$view->render('1')   ;
		}else{

		}
	}
	
	public function save(){
	
		$uid = '';
		if (array_key_exists("uid",$_POST) && array_key_exists("urls",$_POST)){
			$uid = $_POST['uid'];
			
			$urlList=split("\t",trim($_POST['urls'],"\t"));
			
			$user_images = array();
			$user_infos = $this->imageModel->getInfoByUid($uid)   ;
			foreach ($user_infos as $key => $value){
				$user_images[$value['Furl']] = 1;
			}

			$ret = '';
			for($i=0;$i<count($urlList);$i++) //把它们全部输出来
			{
				$curUrl = $urlList[$i];
				
				$image_info = array(
					'Fuid' => $uid,
					'Furl' => $curUrl,			
					'Fcreate_time' => date("Y-m-d H:i:s",time() ),
					'Fupdate_time' => date("Y-m-d H:i:s",time() ) 
				);
				if (array_key_exists($curUrl, $user_images)){
					//echo json_encode(array('code'=>101));
				}else{
					$ret = $this->imageModel->addImage($image_info);
				}
			}
			
			if (false != $ret){
				echo json_encode(array('code'=>0));
			}else{
				echo json_encode(array('code'=>101));
			}
			


		}
	}
	
	
	
}



?>