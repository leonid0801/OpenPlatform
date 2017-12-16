<?php
/**
 * Created by JetBrains PhpStorm.
 * User: leo
 * Date: 17-12-17
 * Time: 上午12:38
 * To change this template use File | Settings | File Templates.
 */

class Item extends LogicBase {
    public function _init() {
        $this->itemModel = new itemModel("t_item");
    }

    public function getItem($get_info){
        $result = Array();
        if (!array_key_exists('f_id', $get_info)){
            $result["code"]=1;
            $result["msg"]="params fault";
            $result["data"]="";
            return $result;
        }

        $f_id = $get_info['f_id'];
        $db_res = $this->itemModel->getItemByFid($f_id);
        if (sizeof($db_res) < 1){
            $result["code"]=1;
            $result["msg"]="failed";
            $result["data"]="";
            return $result;
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
            "f_updated" =>  $db_res[0]['f_updated']
        );
        return $result;

    }


}