<?php

/**
 *
 * @ 
 *
 */
class ItemNewModel extends DBModel {

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
	
	public function getUserByOpenid($wxopenid){
    	return $this->select(parent::ALL_FIELD, "Fopenid='{$wxopenid}'");
    }

	public function insertInfo($params){
		return $this->insert($params);
	}

    public function insertWithIdRes($params){
        return $this->insertBackLastID($params);
    }
	//获取订单
	public function getItemList($where,$start=null,$offset=null){
		return $this->select('*',$where,null,' f_created DESC limit 3',$start,$offset);
	}

    public function getMoreItemList($where,$start,$offset){
        return $this->select('*',$where,null,' f_created DESC ',$start,$offset);
    }

    public function getItemByFid($item_id){
        return $this->select(parent::ALL_FIELD, "f_itemid='{$item_id}'");
    }

    public function getDescByFid($item_id){
        return $this->select(Array("f_textarea"), "f_itemid='{$item_id}'");
    }

    public function del($where){
        return $this->delete($where);
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
