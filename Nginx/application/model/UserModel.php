<?php

/**
 *
 * @ 
 *
 */
class UserModel extends DBModel {

	/**
	 *
	 * @param unknown $db_config_name
	 *
	 * @param unknown $table
	 *
	 * @throws Exception
	 *
	 */
	public function __construct($table) {
		parent::__construct ($table,parent::DEFAULT_DB_CONF);
	}
	
	public function  addClient($wxopenid_info){
		//$wxopenid_info["Finsert_time"] = date( "Y-m-d H:i:s",time() )  ;	
		return $this->insert($wxopenid_info);
	}

    public function  dupKeyUpdate($arr, $where){
        //$wxopenid_info["Finsert_time"] = date( "Y-m-d H:i:s",time() )  ;
        return $this->duplicate_key_update($arr, $where);
    }

    public function getUserIdByClientSess($f_client_session){
        return $this->select(parent::ALL_FIELD, "f_client_session='{$f_client_session}'");
    }
	
	public function getUserByOpenid($wxopenid){
    	return $this->select(parent::ALL_FIELD, "Fopenid='{$wxopenid}'");
    }
	
	//添加订单
	public function insertInfo($params){
		$this->insert($params);
	}
	//获取订单
	public function getItemList($where,$start=null,$offset=null){
		return $this->select('*',$where,null,' f_created DESC limit 3',$start,$offset);
	}

    public function getMoreItemList($where,$start,$offset){
        return $this->select('*',$where,null,' f_created DESC ',$start,$offset);
    }

    public function getItemByFid($item_id){
        return $this->select(parent::ALL_FIELD, "f_id='{$item_id}'");
    }


    public function getUserInfo($arr_uid){
        $nfields = implode ( ", ",  $arr_uid);
        return $this->select(parent::ALL_FIELD, "f_uid in ({$nfields})");
    }


    public function setUserInfo($info,$where){
        return $this->update($info,$where);
    }


    public function uptUserInfo($arr_info,$where){
        return $this->update($arr_info,$where);
    }

	//设置为已读
	public function setRead($info,$where){
		return $this->update($info,$where);
	}
	//获取总记录数
	public function getTotalNum($where){
		return $this->count($where);
	}
	//执行sql
	public function executeSql($sql){

		return $this->execute($sql);
	}
}

?>
