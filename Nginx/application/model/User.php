<?php
/**
 * Created by JetBrains PhpStorm.
 * User: leo
 * Date: 17-11-25
 * Time: 下午12:19
 * To change this template use File | Settings | File Templates.
 */

class User {

    public function __construct( ) {
        $this->_init();
    }

    public function _init() {
        //$this->wxAppModel = new wxAppModel("t_client");
        //$this->itemModel = new itemModel("t_item");
        $this->userModel = new userModel("t_user");
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


}