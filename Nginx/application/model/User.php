<?php
/**
 * Created by JetBrains PhpStorm.
 * User: leo
 * Date: 17-11-25
 * Time: 下午12:19
 * To change this template use File | Settings | File Templates.
 */

class User extends LogicBase {

    public function _init() {
        //$this->wxAppModel = new wxAppModel("t_client");
        $this->itemModel = new itemModel("t_item");
        $this->userModel = new userModel("t_user");

        $this->logs->msg("AAAA", __FILE__, __LINE__);
        $this->logs->debug("YRT", __FILE__, __LINE__);
    }

    /**
     * 对象 转 数组
     *
     * @param object $obj 对象
     * @return array
     */
    function object2array($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)object2array($v);
            }
        }

        return $obj;
    }

    public function getClientSession($get_info){

        $code = "";
        if(isset($get_info['code'])){
            $code = $get_info['code'];
        }
        $url='https://api.weixin.qq.com/sns/jscode2session?appid='.APP_ID.'&secret='.APP_SECRET.'&js_code='.$code.'&grant_type=authorization_code';

        $ret = $this->curl($url);
        $session_key = json_decode($ret)->session_key ;
        $expires_in = json_decode($ret)->expires_in ;
        $openid = json_decode($ret)->openid ;

        $session_id=`head -n 80 /dev/urandom | tr -dc A-Za-z0-9 | head -c 16`;   //���3rd_session
        //echo $session_id;
        $cur_time = date("Y-m-d H:i:s",time());
        $client_info = array(
            'f_openid' => $openid,
            'f_session_key' => $session_key,
            'f_expires_in' => $expires_in,
            'f_client_session' => $session_id,
            'f_created' => $cur_time,
            'f_updated' => $cur_time
        );
        //$db_ret = $this->wxAppModel->addClient($client_info);
        $upt_fields = "f_session_key='{$session_key}',f_updated='{$cur_time}',f_expires_in='{$expires_in}',f_client_session='{$session_id}'";
        $db_ret = $this->userModel->dupKeyUpdate($client_info, $upt_fields);

        //{"code":"0","msg":"success","data":""}
        $result = array();
        if ($db_ret){
            $result["code"]=0;
            $result["msg"]="success";
            $result["data"]["client_session"]=$session_id;
            return $result;

        }


    }


    public function uptUserInfo($post_info){
        $ret = array();
        if (
            !array_key_exists('client_session', $post_info)
            or !array_key_exists('avatar_url', $post_info)
            or !array_key_exists('nickname', $post_info)
            or !array_key_exists('city', $post_info)
            or !array_key_exists('gender', $post_info)
            or !array_key_exists('language', $post_info)
            or !array_key_exists('province', $post_info)
            or !array_key_exists('country', $post_info)
        ){
            $ret["code"]=1;
            $ret["msg"]="lack of params";
            return json_encode($ret);
        }

        $this->logs->debug("Nickname: ".$post_info['nickname'], __FILE__, __LINE__);

        $arr_tmp = Array(
            "f_avatar_url"=>$post_info['avatar_url'],
            "f_nickname"=>$this->filter($post_info['nickname']),
            "f_city"=>$post_info['city'],
            "f_gender"=>$post_info['gender'],
            "f_language"=>$post_info['language'],
            "f_province"=>$post_info['province'],
            "f_country"=>$post_info['country'],
        );
        $res = $this->userModel->uptUserInfo($arr_tmp, " f_client_session='{$post_info['client_session']}'");

        if (false == $post_info['client_session']){
            $ret["code"]=1;
            $ret["msg"]="client_session none";
            return json_encode($ret);
        }

        if (false == $res){
            $ret["code"]=1;
            $ret["msg"]="db upt failed";
            return json_encode($ret);
        }
        $ret["code"]=0;
        $ret["msg"]="upt user info success";
        return json_encode($ret);
    }

    private  function get_front_text($text, $length = 60){
        if (mb_strlen($text, 'UTF8') < 60){
            return $text;
        }else{
            return mb_substr($text,0,60,'UTF8').'…';
        }
    }

    private  function  extract($arr_item, $arr_user){

        $arr_ret = Array();
        $user_info = Array();
        foreach ($arr_user as $key => $value){
            $f_uid = $value['f_uid'];
            $user_info[$f_uid] = Array('f_nickname'=>$value['f_nickname'], 'f_avatar_url'=>$value['f_avatar_url']);
        }

        foreach ($arr_item as $key => $value){
            $arr_ret[$key]['f_id'] = $value['f_id'];
            $arr_ret[$key]['f_uid'] = $value['f_uid'];
            $arr_ret[$key]['f_textarea'] = $this->get_front_text($value['f_textarea']);
            $arr_ret[$key]['f_created'] = $value['f_created'];
            $arr_ret[$key]['f_nickname'] = $user_info[$value['f_uid']]['f_nickname'];
            $arr_ret[$key]['f_avatar_url'] = $user_info[$value['f_uid']]['f_avatar_url'];
        }

        return $arr_ret;
    }

    private  function  joinUserInfo($results){
        $arr_uid = Array();
        if (sizeof($results)>0){
            // get user id array
            foreach ($results as $key => $value){
                array_push($arr_uid, $value["f_uid"]);
            }
        }

        if (sizeof($arr_uid) < 1){
            return false;
        }

        $user_info = $this->userModel->getUserInfo($arr_uid);
        $ext_res = $this->extract($results, $user_info);

        return $ext_res;

    }

    public function getTopMore($get_info){
        $ret = array();
        if (!array_key_exists('page', $get_info) or !array_key_exists('page_size', $get_info) or !array_key_exists('max_item_index', $get_info)){
            $ret["code"]=1;
            $ret["msg"]="failed";
            $ret["data"]="";
            return $ret;
        }
        $page_index = (int)$get_info["page"];
        $page_size = (int)$get_info["page_size"];
        $max_item_index = (int)$get_info["max_item_index"];
        $results = $this->itemModel->getMoreItemList("1=1", $page_index*$page_size, $page_size);


        // get the newest item index
        $res_max_item_index = 0;
        if (sizeof($results)>0){
            $res_max_item_index = $results[0]["f_id"];
        }

        $ext_res = $this->joinUserInfo($results);
        if (false == $ext_res){
            $ret["code"]=1;
            $ret["msg"]="user info none";
            return $ret;
        }

        if($res_max_item_index <= $max_item_index){
            $ret["code"]=1;
            $ret["msg"]="failed";
            $ret["data"]="no update";
            return $ret;
        }

        $ret["code"]=0;
        $ret["msg"]="success";
        $ret["data"]=$ext_res;
        $ret["max_item_index"]=$res_max_item_index;
        return $ret;
    }

    public function items_filter($result){
        $arr_ret = Array();
        foreach ($result as $key => $value){
            $arr_ret[$key]["f_id"] = $value["f_id"];
            $arr_ret[$key]['f_textarea'] = $this->get_front_text($value['f_textarea']);
        }
        return $arr_ret;

    }

    public function getUserItems($get_info){
        $ret = array();
        if (!array_key_exists('page', $get_info) or !array_key_exists('page_size', $get_info) or !array_key_exists('client_session', $get_info)){
            $ret["code"]=1;
            $ret["msg"]="failed";
            $ret["data"]="";
            return $ret;
        }


        $page_index = (int)$get_info["page"];
        $page_size = (int)$get_info["page_size"];
        $client_session = $get_info["client_session"];

        $db_res = $this->userModel->getUidByClientSess($client_session);
        if (sizeof($db_res) < 1){
            $ret["code"]=1;
            $ret["msg"]="failed";
            $ret["data"]="";
            return $ret;
        }
        $f_uid = $db_res[0]["f_uid"];
        $results = $this->itemModel->getMoreItemList("f_uid='$f_uid'", $page_index*$page_size, $page_size);

        $filter_ret = $this->items_filter($results);
        $ret["code"]=0;
        $ret["msg"]="success";
        $ret["data"]=$filter_ret;
        return $ret;

    }

    public function getBottomData($get_info){

        $ret = array();
        if (!array_key_exists('page', $get_info) or !array_key_exists('page_size', $get_info)){
            $ret["code"]=1;
            $ret["msg"]="failed";
            $ret["data"]="";
            return $ret;
        }

        $page_index = (int)$get_info["page"];
        $page_size = (int)$get_info["page_size"];
        $results = $this->itemModel->getMoreItemList("1=1", $page_index*$page_size, $page_size);

        $ext_res = $this->joinUserInfo($results);
        if (false == $ext_res){
            $ret["code"]=1;
            $ret["msg"]="user info none";
            return $ret;
        }

        $ret["code"]=0;
        $ret["msg"]="success";
        $ret["data"]=$ext_res;
        return $ret;

    }


    public function delItem($get_info){
        $ret = array();
        if (!array_key_exists('f_id', $get_info) or !array_key_exists('client_session', $get_info)){
            $ret["code"]=1;
            $ret["msg"]="params fault";
            $ret["data"]="";
            return $ret;
        }

        // get user id
        $client_session = $get_info["client_session"];
        $db_res = $this->userModel->getUidByClientSess($client_session);
        if (sizeof($db_res) < 1){
            $ret["code"]=1;
            $ret["msg"]="failed";
            $ret["data"]="";
            return $ret;
        }
        $f_uid = $db_res[0]["f_uid"];

        // get user id with item id
        $f_id = $get_info["f_id"];
        $db_item = $this->itemModel->getItemByFid($f_id);
        if (sizeof($db_item) < 1){
            $ret["code"]=1;
            $ret["msg"]="db error";
            $ret["data"]="";
            return $ret;
        }
        $db_f_uid = $db_item[0]["f_uid"];

        // user check
        if ($f_uid != $db_f_uid){
            $ret["code"]=1;
            $ret["msg"]="user check faied";
            $ret["data"]="";
            return $ret;
        }

        $res = $this->itemModel->del("f_id='$f_id'");
        if($res){
            $ret["code"]=0;
            $ret["msg"]="success";
            $ret["data"]="";
            return $ret;
        }






    }


}