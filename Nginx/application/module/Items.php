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
        $this->itemView = new itemView("t_view");
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

        // inserting view info
        $infoView=Array();
        $infoView['f_itemid']=$insertRes;
        $infoView['f_count']=0;
        $this->itemView->insertInfo($infoView);

        //$this->logs->msg(print_r($insertRes,1), __FILE__, __LINE__);
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

    private  function  genImageUrl($uid,$imageName){
        return FULL_ITEM_DIR_DOMAIN.$uid.'/'.$imageName;
    }

    private function  resProcess($results){
        $ret=Array();
        foreach ($results as $key => $result){
            $item=Array();
            $item['f_itemid']=$result['f_itemid'];
            //$item['f_focus']=FULL_ITEM_DIR_DOMAIN.$result['f_uid'].'/'.$result['f_focus'];
            $item['f_focus']=$this->genImageUrl($result['f_uid'], $result['f_focus']);
            $item['f_textarea']=$result['f_textarea'];
            array_push($ret,$item);
        }
        return $ret;
    }

    public function getBottom($info){

        if (!array_key_exists('page', $info) or !array_key_exists('page_size', $info)){
            return $this->retArray(1,'failed');
        }

        $page_index = (int)$info["page"];
        $page_size = (int)$info["page_size"];
        $results = $this->itemModel->getMoreItemList("1=1", $page_index*$page_size, $page_size);

        $ret=$this->resProcess($results);
        /*
        $ext_res = $this->joinUserInfo($results);
        if (false == $ext_res){
            $ret["code"]=1;
            $ret["msg"]="user info none";
            return $ret;
        }*/

        return $this->retArray(0,'success',$ret);
    }

    public function getUserPubs($info){
        $uid=$this->utils->getUserIdInCookie();

        if (!array_key_exists('page', $info) or !array_key_exists('page_size', $info)){
            return $this->retArray(1,'failed');
        }

        $page_index = (int)$info["page"];
        $page_size = (int)$info["page_size"];
        $results = $this->itemModel->getMoreItemList("f_uid=$uid", $page_index*$page_size, $page_size);

        $ret=$this->resProcess($results);
        /*
        $ext_res = $this->joinUserInfo($results);
        if (false == $ext_res){
            $ret["code"]=1;
            $ret["msg"]="user info none";
            return $ret;
        }*/

        return $this->retArray(0,'success',$ret);

    }

    public function delItem($info){
        $uid=$this->utils->getUserIdInCookie();

        if (!array_key_exists('itemId', $info)){
            return $this->retArray(1,'failed');
        }
        $itemId = $info["itemId"];

        $itemUids = $this->itemModel->getUidByItemid($itemId);
        if (count($itemUids)<1){
            return $this->retArray(1,'failed');
        }
        $itemUid=$itemUids[0]['f_uid'];

        if ($uid!=$itemUid){
            if($uid!=SUPER_ADMIN){
                return $this->retArray(1,'failed');
            }
        }

        $res_del = $this->itemModel->del("f_itemid='$itemId'");
        $results = $this->imageModel->del("f_itemid='$itemId'");

        return $this->retArray(0,'success');

    }


    private function  resImagesProcess($results){
        $ret=Array();
        foreach ($results as $key => $result){
            $item=Array();
            $item['f_itemid']=$result['f_itemid'];
            $item['f_imagename']=$this->genImageUrl($result['f_uid'], $result['f_imagename']);
            array_push($ret,$item);
        }
        return $ret;
    }

    public function getImages($info){
        if (!array_key_exists('itemId', $info)){
            return $this->retArray(1,'failed');
        }
        $itemId=$info['itemId'];
        $results = $this->imageModel->getImagesItemId($itemId);
        $ret=$this->resImagesProcess($results);
        return $this->retArray(0,'success',$ret);
    }

    public  function  getDesc($info){
        if (!array_key_exists('itemId', $info)){
            return $this->retArray(1,'failed');
        }
        $itemId=$info['itemId'];
        $descRes = $this->itemModel->getDescByFid($itemId);
        $countRes = $this->itemView->getCountByFid($itemId);
        $this->itemView->increaseView($itemId);
        if (count($descRes)<1 or count($countRes)<1){
            return $this->retArray(1,'failed');
        }

        $count=DEFAULT_VIEW_COUNT;
        if (array_key_exists('f_count', $countRes[0]) && false!=$countRes[0]['f_count']){
            $count=$countRes[0]['f_count'];
        }
        $resArray=array('f_textarea'=>$descRes[0]['f_textarea'], 'f_count'=>$count);
        return $this->retArray(0,'success',$resArray);
    }



}