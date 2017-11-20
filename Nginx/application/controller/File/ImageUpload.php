<?php
//require dirname(__FILE__) . '/Wx.conf.php';
//define your token
define("TOKEN", "weixin");



class File_ImageUpload extends BaseController{


	public function upload(){


		$view = $this->_getView();
		//$view->assign('retcode',$retcode);
		$view->render('upload')   ;

	
	}

	public function test(){

		//$s2 = new SaeStorage();  
		//$name =$_FILES['imagefile']['name'];  
		$name = md5_file($_FILES['imagefile']['tmp_name']);
		
		//$s2->upload('image',$name,$_FILES['imagefile']['tmp_name']);//把用户传到SAE的文件转存到名为test的storage  ,$_FILES["file"]["tmp_name"] - 存储在服务器的文件的临时副本的名称
		//$imageUrl = $s2->getUrl("image",$name);//输出文件在storage的访问路径  
		//echo '<br/>';  
		//echo $s2->errmsg(); //输出storage的返回信息   

		$fileSize = filesize($_FILES['imagefile']['tmp_name']);
		move_uploaded_file($_FILES["imagefile"]["tmp_name"], './ugc/' . $name);
		$imageUrl = 'http://120.27.236.213/ugc/' . $name;
		//$ret = array('0' => array('path'=>$imageUrl));
		$ret = array();
		$ret[] = array(
			'path'=>$imageUrl,
			'size'=>$fileSize,
			'name'=>'预览',
		);

		$ret = json_encode($ret);
		echo urldecode($ret); 

		//var_dump($_FILES);
		//echo "Here!".SAE_TMP_PATH; # /saetmp/111/leonid/1464489410_3940689762/
	}





}



?>