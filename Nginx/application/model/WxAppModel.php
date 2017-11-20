<?php

/**
 *
 * @ 
 *
 */
class WxAppModel extends DBModel {

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

    public function getOpenidByClientSess($f_client_session){
        return $this->select(parent::ALL_FIELD, "f_client_session='{$f_client_session}'");
    }
	
	//添加订单
	public function insertInfo($params){
		$this->insert($params);
	}
	//获取订单
	public function getOrderList($where,$start=null,$offset=null){
		return $this->select('*',$where,null,' insert_time DESC limit 100',$start,$offset);
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
