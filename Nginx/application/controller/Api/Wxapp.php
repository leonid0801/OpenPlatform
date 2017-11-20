<?php
require dirname(__FILE__) . '/Wx.conf.php';
//define your token
define("TOKEN", "weixin");
define ( "APP_ID", "wx4b3c39b8af0088fc" ) ;
define ( "APP_SECRET", "6c173d534ed260058b376508a33be1d5" ) ;


class Api_Wxapp extends BaseController{
	private $wxAppModel ;
	public function _init() {
        $this->wxAppModel = new wxAppModel("t_client");
        $this->itemModel = new itemModel("t_item");
    }
	
	//  ·�ɲ��ԣ�$HOST/index.php/api/wxapp/test
	public function test(){
		var_dump($_GET);
		echo "Here!";
	}
	
	public function get_seq(){
		$code = "";
		if(isset($_GET['code'])){
			$code = $_GET['code'];
		}
		$url='https://api.weixin.qq.com/sns/jscode2session?appid='.APP_ID.'&secret='.APP_SECRET.'&js_code='.$code.'&grant_type=authorization_code';

		$ret = $this->curl($url);
		$session_key = json_decode($ret)->session_key ;
		$expires_in = json_decode($ret)->expires_in ;
		$openid = json_decode($ret)->openid ;
		
		$session_id=`head -n 80 /dev/urandom | tr -dc A-Za-z0-9 | head -c 16`;   //���3rd_session
		//echo $session_id;
		$client_info = array(
			'f_openid' => $openid,
			'f_session_key' => $session_key,
			'f_expires_in' => $expires_in,
			'f_client_session' => $session_id,
			'f_created' => date("Y-m-d H:i:s",time() ),
			'f_updated' => date("Y-m-d H:i:s",time() ) 
		);
		$db_ret = $this->wxAppModel->addClient($client_info);

        //{"code":"0","msg":"success","data":""}
        $result = array();
		if ($db_ret){
            $result["code"]=0;
            $result["msg"]="success";
            $result["data"]["client_session"]=$session_id;
			echo json_encode($result);
		}
	}

    public function add_item(){

        $code = "";
        header("Content-type:text/html;charset=utf-8");

        if (!array_key_exists('client_session', $_POST)){
            echo "error";
            return;
        }

        $open_id = $this->wxAppModel->getOpenidByClientSess($_POST['client_session']);


        if (array_key_exists('textarea', $_POST)
            && array_key_exists('main_type_id', $_POST)
            && array_key_exists('sub_type_id', $_POST)
            && array_key_exists('date', $_POST)
            && array_key_exists('latitude', $_POST)
            && array_key_exists('longitude', $_POST)
        ){
            $client_info = array(
                'f_openid' => $open_id[0]['f_openid'],
                'f_textarea' => $_POST['textarea'],
                'f_main_type_id' => $_POST['main_type_id'],
                'f_sub_type_id' => $_POST['sub_type_id'],
                'f_start_date' => $_POST['date'],
                'f_latitude' => $_POST['latitude'],
                'f_longitude' => $_POST['longitude'],
                'f_created' => date("Y-m-d H:i:s",time() ),
                'f_updated' => date("Y-m-d H:i:s",time() )
            );
            $db_ret = $this->itemModel->addClient($client_info);
            echo $_POST['textarea']."123";
        }

    }

    public function get_items(){

        $results = $this->itemModel->getItemList("1=1");
        echo json_encode($results);
    }

    public function get_item_detail(){

        $result = array();

        if (array_key_exists('f_id', $_GET)
            && array_key_exists('client_session', $_GET)
        ){
            $f_id = $_GET['f_id'];
            $client_session = $_GET['client_session'];

            $db_res = $this->itemModel->getItemByFid($f_id);
            if (sizeof($db_res) < 1){
                $result["code"]=1;
                $result["msg"]="failed";
                $result["data"]="";
                echo json_encode($result);
                return;
            }

            $db_openid = $this->wxAppModel->getOpenidByClientSess($client_session);
            if (sizeof($db_openid) < 1){
                $result["code"]=1;
                $result["msg"]="failed";
                $result["data"]="";
                echo json_encode($result);
                return;
            }
            $client_openid = $db_openid[0]['f_openid'];

            $openid = $db_res[0]['f_openid'];
            $can_modify = false;
            if ($client_openid==$openid){
                $can_modify=true;
            }

            $result["code"]=0;
            $result["msg"]="success";
            $result["data"]=array(
                "f_textarea" =>  $db_res[0]['f_textarea'],
                "f_main_type_id" =>  $db_res[0]['f_main_type_id'],
                "f_sub_type_id" =>  $db_res[0]['f_sub_type_id'],
                "f_start_date" =>  $db_res[0]['f_start_date'],
                "f_finish_date" =>  $db_res[0]['f_finish_date'],
                "f_latitude" =>  $db_res[0]['f_latitude'],
                "f_longitude" =>  $db_res[0]['f_longitude'],
                "f_location" =>  $db_res[0]['f_location'],
                "f_url" =>  $db_res[0]['f_url'],
                "f_created" =>  $db_res[0]['f_created'],
                "f_updated" =>  $db_res[0]['f_updated'],
                "can_modify" =>  $can_modify
            );
            echo json_encode($result);
        }
    }

    public function get_more(){
        $ret = array();

        if (!array_key_exists('page', $_GET) or !array_key_exists('page_size', $_GET) or !array_key_exists('max_item_index', $_GET)){
            $ret["code"]=1;
            $ret["msg"]="failed";
            $ret["data"]="";
            echo json_encode($ret);
            return;
        }
        $page_index = (int)$_GET["page"];
        $page_size = (int)$_GET["page_size"];
        $max_item_index = (int)$_GET["max_item_index"];
        $results = $this->itemModel->getMoreItemList("1=1", $page_index*$page_size, $page_size);

        // get the newest item index
        $res_max_item_index = 0;
        if (sizeof($results)>0){
            $res_max_item_index = $results[0]["f_id"];
        }

        if($res_max_item_index <= $max_item_index){
            $ret["code"]=1;
            $ret["msg"]="failed";
            $ret["data"]="no update";
            echo json_encode($ret);
            return;
        }

        $ret["code"]=0;
        $ret["msg"]="success";
        $ret["data"]=$results;
        $ret["max_item_index"]=$res_max_item_index;
        echo json_encode($ret);
    }


    public function get_bottom_data(){
        $ret = array();
        if (!array_key_exists('page', $_GET) or !array_key_exists('page_size', $_GET)){
            $ret["code"]=1;
            $ret["msg"]="failed";
            $ret["data"]="";
            echo json_encode($ret);
            return;
        }

        $page_index = (int)$_GET["page"];
        $page_size = (int)$_GET["page_size"];
        $results = $this->itemModel->getMoreItemList("1=1", $page_index*$page_size, $page_size);

        $ret["code"]=0;
        $ret["msg"]="success";
        $ret["data"]=$results;
        echo json_encode($ret);

    }

}



?>
