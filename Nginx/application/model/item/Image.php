<?php
/**
 * Created by JetBrains PhpStorm.
 * User: leo
 * Date: 17-11-25
 * Time: 下午12:19
 * To change this template use File | Settings | File Templates.
 */

class Image {

    public function __construct( ) {
        $this->itemNewModel = new itemNewModel("t_item_new");
        $this->logs = LOGS::getInstance();
    }

    public function _init() {

    }

    public function getImageName($imageInfo) {
        if (!array_key_exists('tmp_name', $imageInfo)or!array_key_exists('tmp_name', $imageInfo)){
            return false;
        }
        $imageFile = $imageInfo['tmp_name'];
        $imageTypeInfo = explode('/',$imageInfo['type'])[1];

        $this->logs->msg($imageTypeInfo, __FILE__, __LINE__);

        $imageName = md5_file($imageFile);
        return $imageName.'.'.$imageTypeInfo;
    }

    public function getImageDir($uid) {
        $newDir=FULL_ITEM_DIR.$uid.'/';
        if(!is_dir($newDir)){
            mkdir($newDir,0777,true);
        }
        return $newDir;
    }

    public function imageSave($localInfo){

        $uid=$localInfo['uid'];
        $imageName=$this->getImageName($_FILES['imagefile']);
        $imageDir=$this->getImageDir($uid);

        $fileSize = filesize($_FILES['imagefile']['tmp_name']);
        $uploadRes = move_uploaded_file($_FILES["imagefile"]["tmp_name"], $imageDir .'/'. $imageName);
        $this->logs->msg($uploadRes, __FILE__, __LINE__);
        //$this->logs->msg(json_encode($_FILES["imagefile"]), __FILE__, __LINE__);

        $imageUrl = FULL_ITEM_DIR_DOMAIN . $uid.'/'.$imageName;
        //$ret = array('0' => array('path'=>$imageUrl));
        $ret = array();
        $ret[] = array(
            'path'=>$imageUrl,
            'size'=>$fileSize,
            'name'=>'预览',
        );

        $ret = json_encode($ret);
        return $ret;

    }




}