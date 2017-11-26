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

        $arr_tmp = Array(
            "f_avatar_url"=>$post_info['avatar_url'],
            "f_nickname"=>$post_info['nickname'],
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
            $arr_ret[$key]['f_textarea'] = $value['f_textarea'];
            $arr_ret[$key]['f_created'] = $value['f_created'];
            $arr_ret[$key]['f_nickname'] = $user_info[$value['f_uid']]['f_nickname'];
            $arr_ret[$key]['f_avatar_url'] = $user_info[$value['f_uid']]['f_avatar_url'];
        }

        return $arr_ret;
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

        $arr_uid = Array();
        // get the newest item index
        $res_max_item_index = 0;
        if (sizeof($results)>0){
            $res_max_item_index = $results[0]["f_id"];
            // get user id array
            foreach ($results as $key => $value){
                array_push($arr_uid, $value["f_uid"]);
            }
        }

        if (sizeof($arr_uid) < 1){
            $ret["code"]=1;
            $ret["msg"]="failed";
            $ret["data"]="user info none";
            return $ret;
        }

        $user_info = $this->userModel->getUserInfo($arr_uid);
        $ext_res = $this->extract($results, $user_info);

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

        $ret["code"]=0;
        $ret["msg"]="success";
        $ret["data"]=$results;
        return $ret;

    }


}