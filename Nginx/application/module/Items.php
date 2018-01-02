<?php
/**
 * Created by JetBrains PhpStorm.
 * User: leo
 * Date: 17-12-31
 * Time: 下午9:01
 * To change this template use File | Settings | File Templates.
 */


class Items{

    public function __construct( ) {
        $this->logs = LOGS::getInstance();
        $this->_init();
    }

    public function _init() {
        $this->itemModel = new itemNewModel("t_item_new");
        $this->imageModel = new imageModel("t_image");
        $this->utils=new Utils();

    }

    private  function  retArray($code, $msg, $data=''){
        $ret = Array();
        $ret['code'] = $code;
        $ret['msg'] = $msg;
        $ret['data'] = $data;
        return $ret;
    }

    private  function  getCurTime(){
        return date("Y-m-d H:i:s",time());
    }

    public function newItem($info){

        $this->logs->msg(print_r($info,1), __FILE__, __LINE__);

        if (!array_key_exists('images', $info)
            or !array_key_exists('text', $info)){
            return $this->retArray(1, 'params lacked', '');
        }
        $images=$info['images'];
        $text=$info['text'];
        $uid=$this->utils->getUserIdInCookie();

        $imageFocus=(count($images)>0)?basename($images[0]):'';
        $cur_time=$this->getCurTime();
        $newItem=Array(
            'f_uid'=>$uid ,
            'f_textarea'=>$text,
            'f_focus'=>$imageFocus,
            'f_created' => $cur_time,
            'f_updated' => $cur_time);
        $insertRes=$this->itemModel->insertWithIdRes($newItem);
        $this->logs->msg(print_r($insertRes,1), __FILE__, __LINE__);
        if($insertRes){
            $newImages=Array();
            foreach ($images as $key => $imageUrl){
                $cur_time=$this->getCurTime();
                $imageName=basename($imageUrl);
                $infoImages=Array();
                $infoImages['f_uid']=$uid;
                $infoImages['f_itemid']=$insertRes;
                $infoImages['f_imagename']=$imageName;
                $infoImages['f_created']=$cur_time;
                $infoImages['f_updated']=$cur_time;
                $this->imageModel->insertInfo($infoImages);
                array_push($newImages, $infoImages);
            }
            $this->logs->msg(print_r($newImages,1), __FILE__, __LINE__);
            $ret=$this->retArray(0,'success');
            return $ret;
        }

    }



}