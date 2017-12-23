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
        $this->logs = LOGS::getInstance();
    }

    public function _init() {

    }

    public function imageSave(){

        $name = md5_file($_FILES['imagefile']['tmp_name']);

        $fileSize = filesize($_FILES['imagefile']['tmp_name']);
        $uploadRes = move_uploaded_file($_FILES["imagefile"]["tmp_name"], '/usr/share/nginx/html/ugc/' . $name.'.jpg');
        $this->logs->msg($uploadRes, __FILE__, __LINE__);
        $this->logs->msg(json_encode($_FILES["imagefile"]), __FILE__, __LINE__);

        $imageUrl = 'https://bjwob.top/ugc/' . $name.'.jpg';
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