<?php

/**
 * Base_DBModel基础类 提供数据库相关基本操作
 *
 * 主要目的：1，省去不必要的代码
 *        2，使逻辑清晰 提高效率
 *
 * @author kokeryang
 *
 */
class DBModel {
	const PAGE_COUNT = 10;
	/**
	 * 正式环境的DB配置key
	 *
	 * @var unknown
	 */
	const DEFAULT_DB_CONF = 'wxapp_db';
	const STATS_DB_CONF	  = 'dealer_statistics';
	const DEALER_SEARCH	  = 'dealer_search';
	
	//从库后缀
	const SLAVE_POSTFIX = '_r';
	
	/**
	 * 所有字段
	 *
	 * @var unknown
	 */
	const ALL_FIELD = '*';
	/**
	 * 无条件
	 *
	 * @var unknown
	 */
	const ALL_CONDITION = '1=1';
	
	/**
	 * 一般数据库都部署master/slave两台
	 *
	 * $_master_conf -->数组
	 * $_slave_conf -->数组
	 *
	 *
	 * master只用于写入
	 *
	 * @var unknown
	 */
	protected $_master_conf;
	/**
	 * slave只用于读取
	 *
	 * @var unknown
	 */
	protected $_slave_conf;
	
	/**
	 * 本次操作的表
	 *
	 * @var unknown
	 */
	protected $_table = '';
	
	/**
	 * logger打印对象
	 *
	 * @var unknown
	 */
	protected $_logger;

	//记录执行的SQL
	public static $SQLS = array();
	
	/**
	 * 构造器
	 *
	 * @param unknown $db_config_name        	
	 * @param unknown $table        	
	 * @throws Exception
	 */
	public function __construct($table, $db_config_name ) {
		$this->_table = $table;
		//$this->_logger = Logger::getLogger ( 'db' );
		if (empty ( $this->_table )) {
			$this->table ( ucfirst ( substr ( get_class ( $this ), 0, - 7 ) ) );
		}

		$ret = $this->initialize ( $db_config_name);
		
		if (! $ret) {
			//$this->_logger->error ( 'init db connction error!' );
			throw new Exception ( 'init db connction error!' );
		}
		
	}
	
	/**
	 * 获取数据库连接
	 *
	 * @param unknown $conf        	
	 * @return boolean PDO
	 */
	private function getDbh($conf) {
		$params = array (
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'' ,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		);
		$dsn = "mysql:dbname={$conf['dbname']};host={$conf['dbhost']};port={$conf['dbport']}";
		///echo $dsn."\n";
		try {
			$dbh = new PDO ( $dsn, $conf ['dbuser'], $conf ['dbpass'], $params );
		} catch ( PDOException $e ) {
			//$this->_logger->error($dsn);
			//$this->_logger->error ( 'Connection failed: ' . $e->getMessage () );
			echo "Connection failed";
		}
		return $dbh;
	}
	/**
	 * 获取事务连接
	 */
	public function getDbh4Transact($is_select = true){
		$conf = $this->_master_conf;
		if ($is_select) {
			$conf = $this->_slave_conf;
		}
		return $this->getDbh($conf);
	}
	
	/**
	 * 初始化数据库连接对象
	 *
	 * @param unknown $db_config_name        	
	 */
	protected function initialize($db_config_name) {
		
		$this->_master_conf = ''; //主库链接
		$this->_slave_conf  = ''; //从库链接
		
		
		$configinfo = $this->getConfig($db_config_name.'_dev');
		if(empty($configinfo))
		{
			return false;
		}
		
		$this->_master_conf = $this->_slave_conf = $this->getZkInfo ( $configinfo, false );
		
		
		return true;
	}
	
	/**
	 * 返回array
	 *
	 * @return boolean
	 */
	private function getZkInfo($configinfo, $ZK = FALSE) {
		if ($ZK) {
			$zkinfo = $this->_getZkInfo ( $configinfo['zkname'] );
			if (! $zkinfo || empty ( $zkinfo )) {
				return false;
			}
			return array (
					'dbhost' => $zkinfo ['ip'],
					'dbport' => $zkinfo ['port'],
					'dbname' => $configinfo['name'],
					'dbuser' => $configinfo['user'],
					'dbpass' => $configinfo['pass'] 
			);
		} else {
			return array (
					'dbhost' => $configinfo['host'],
					'dbport' => $configinfo['port'],
					'dbname' => $configinfo['name'],
					'dbuser' => $configinfo['user'],
					'dbpass' => $configinfo['pass'] 
			);
		}
	}
	
	/**
	 * 直接执行sql
	 * 默认select ，如果不是select 设置$is_select为false
	 */
	protected function execute($sql, $is_select = true) {
		try {
		    //记录执行时间 start
		    if(defined('DEBUG_SQL') && DEBUG_SQL)
		    {
		        $start_time = microtime(1);
		    }
		    
			$conf = $this->_master_conf;
			if ($is_select) {
				$conf = $this->_slave_conf;
			}
			$dbh = $this->getDbh ( $conf );
			$prepare = $dbh->prepare ( $sql );
			$res = $prepare->execute ();
			if ($is_select) {
				$res = $prepare->fetchAll ( PDO::FETCH_ASSOC );
			}
			$dbh = null;
			
			//记录执行时间 end
			if(defined('DEBUG_SQL') && DEBUG_SQL)
			{
			    $walltime = microtime(1) - $start_time;
			    self::$SQLS[] = "{$sql}|{$walltime}";
			}
			
			return $res;
		} catch ( Exception $e ) {
			$this->_logger->error ( $e->getMessage () );
			$this->_logger->error ( "BAD SQL:{$sql}" );
			return false;
		}
	}
	
	/**
	 * 查询数据 某个条件
	 *
	 * fields 传入数组
	 * where 传入数组
	 *
	 * $orders 传入字符串 比如 'a desc ,b desc '
	 *
	 * @param unknown $id        	
	 * @param string $column        	
	 * @return unknown boolean
	 */
	protected function select($fields = '*', $where = '1=1', $groups = null, $orders = null, $start = null, $limit = null) {
		try {
			if (self::ALL_FIELD !== $fields) {
				$nfields = implode ( ", ", array_values ( $fields ) );
			} else {
				$nfields = self::ALL_FIELD;
			}
			
			$sql = "SELECT {$nfields} FROM $this->_table where {$where}";
			
			if ($groups !== null) {
				$sql .= " group by {$groups}";
			}
			
			if ($orders !== null) {
				$sql .= " order by {$orders}";
			}
			
			if ($start !== null && $limit !== null) {
				$sql .= " limit {$start},{$limit}";
			}
			
			//记录执行时间 start
		    if(defined('DEBUG_SQL') && DEBUG_SQL)
		    {
		        $start_time = microtime(1);
		    }
		    
			$dbh = $this->getDbh ( $this->_slave_conf );
			$prepare = $dbh->prepare ( $sql );
			$prepare->execute ();//var_dump($where."****".$sql);
			$res = $prepare->fetchAll ( PDO::FETCH_ASSOC );
			$dbh = null;
			
			//记录执行时间 end
			if(defined('DEBUG_SQL') && DEBUG_SQL)
			{
			    $walltime = microtime(1) - $start_time;
			    self::$SQLS[] = "{$sql}|{$walltime}";
			}
			
			return $res;
		} catch ( Exception $e ) {
			$this->_logger->error ( $e->getMessage () );
			$this->_logger->error ( "BAD SQL:{$sql}" );
			return false;
		}
	}
	
	/**
	 * insert 数据
	 *
	 * 传入数组形式， key:列名，value:数据值
	 *
	 * 比如 array('x'=>'y','z'=>'d');
	 *
	 * @param unknown $info        	
	 * @return boolean
	 */
	protected function insert($info) {
		try {
			$fields = '(' . implode ( ",", array_keys ( $info ) ) . ')';
			$values = '(' . str_repeat ( "?,", count ( $info ) - 1 ) . '?)';
			$sql = "INSERT INTO $this->_table $fields values $values ";
			
			//记录执行时间 start
			if(defined('DEBUG_SQL') && DEBUG_SQL)
			{
			    $start_time = microtime(1);
			}
			
			$dbh = $this->getDbh ( $this->_master_conf );
			///echo $sql;
			$prepare = $dbh->prepare ( $sql );
			$res = $prepare->execute ( array_values ( $info ) );
			$dbh = null;
			
			//记录执行时间 end
			if(defined('DEBUG_SQL') && DEBUG_SQL)
			{
			    $walltime = microtime(1) - $start_time;
			    self::$SQLS[] = "{$sql}|{$walltime}";
			}
			
			return $res;
		} catch ( Exception $e ) {
			$this->_logger->error ( $e->getMessage () );
			$this->_logger->error ( "BAD SQL:{$sql}" );
			return false;
		}
	}
	
	/**
	 * insert 数据 并返回insert_id
	 *
	 * 传入数组形式， key:列名，value:数据值
	 *
	 * 比如 array('x'=>'y','z'=>'d');
	 *
	 * @param unknown $info        	
	 * @return boolean
	 */
	protected function insertBackLastID($info) {
		try {
			$fields = '(' . implode ( ",", array_keys ( $info ) ) . ')';
			$values = '(' . str_repeat ( "?,", count ( $info ) - 1 ) . '?)';
			$sql = "INSERT INTO $this->_table $fields values $values ";
			
			//记录执行时间 start
			if(defined('DEBUG_SQL') && DEBUG_SQL)
			{
			    $start_time = microtime(1);
			}
			
			$dbh = $this->getDbh ( $this->_master_conf );
			$prepare = $dbh->prepare ( $sql );
			$res = $prepare->execute ( array_values ( $info ) );
			$insert_id = $dbh->lastInsertId ();
			$dbh = null;
			
			//记录执行时间 end
			if(defined('DEBUG_SQL') && DEBUG_SQL)
			{
			    $walltime = microtime(1) - $start_time;
			    self::$SQLS[] = "{$sql}|{$walltime}";
			}
			
			return $insert_id;
		} catch ( Exception $e ) {
			$this->_logger->error ( $e->getMessage () );
			$this->_logger->error ( "BAD SQL:{$sql}" );
			return false;
		}
	}
	
	/**
	 * update数据
	 *
	 * 传入数组info 和 where
	 *
	 * @param unknown $info        	
	 * @param unknown $where        	
	 * @return unknown boolean
	 */
	protected function update($info, $where = '1=1') {
		try {
			$fields = implode ( "=?, ", array_keys ( $info ) ) . "=? ";
			$sql = "UPDATE $this->_table SET {$fields} WHERE {$where}";
			
			//记录执行时间 start
			if(defined('DEBUG_SQL') && DEBUG_SQL)
			{
			    $start_time = microtime(1);
			}
			
			$dbh = $this->getDbh ( $this->_master_conf );
			$prepare = $dbh->prepare ( $sql );
			$res = $prepare->execute ( array_values ( $info ) );
			
			//记录执行时间 end
			if(defined('DEBUG_SQL') && DEBUG_SQL)
			{
			    $walltime = microtime(1) - $start_time;
			    self::$SQLS[] = "{$sql}|{$walltime}";
			}
			
			return $res;
		} catch ( Exception $e ) {
			$this->_logger->error ( $e->getMessage () );
			$this->_logger->error ( "BAD SQL:{$sql}" );
			return false;
		}
	}
	
	/**
	 * 删除数据
	 * 传入数组 where
	 *
	 * @param unknown $where        	
	 * @return unknown boolean
	 */
	protected function delete($where = '1=1') {
		try {
		    //记录执行时间 start
		    if(defined('DEBUG_SQL') && DEBUG_SQL)
		    {
		        $start_time = microtime(1);
		    }
			$sql = "DELETE FROM $this->_table where {$where} ";
			$dbh = $this->getDbh ( $this->_master_conf );
			$prepare = $dbh->prepare ( $sql );
			$res = $prepare->execute ();
			$dbh = null;

			//记录执行时间 end
			if(defined('DEBUG_SQL') && DEBUG_SQL)
			{
			    $walltime = microtime(1) - $start_time;
			    self::$SQLS[] = "{$sql}|{$walltime}";
			}
			
			return $res;
		} catch ( Exception $e ) {
			$this->_logger->error ( $e->getMessage () );
			$this->_logger->error ( "BAD SQL:{$sql}" );
			return false;
		}
	}
	
	/**
	 * 计算数目
	 * 传入数组 where
	 *
	 * @param unknown $where        	
	 * @return unknown boolean
	 */
	protected function count($where = '1=1') {
		try {
		    
		    //记录执行时间 start
		    if(defined('DEBUG_SQL') && DEBUG_SQL)
		    {
		        $start_time = microtime(1);
		    }
		    
			$sql = "SELECT count(*) as c FROM $this->_table where {$where} ";
			$dbh = $this->getDbh ( $this->_slave_conf );
			$prepare = $dbh->prepare ( $sql );
			$prepare->execute ();
			$res = $prepare->fetchAll ( PDO::FETCH_ASSOC );
			$dbh = null;
			
			//记录执行时间 end
			if(defined('DEBUG_SQL') && DEBUG_SQL)
			{
			    $walltime = microtime(1) - $start_time;
			    self::$SQLS[] = "{$sql}|{$walltime}";
			}
			
			if ($res) {
				return $res [0] ['c'];
			} else {
				return 0;
			}
		} catch ( Exception $e ) {
			$this->_logger->error ( $e->getMessage () );
			$this->_logger->error ( "BAD SQL:{$sql}" );
			return false;
		}
	}
	
	/**
	 * 获取 host的 zkname
	 *
	 * @param unknown $name        	
	 * @return boolean array
	 */
	protected function _getZkInfo($name) {
		if (! $name) {
			return false;
		}
		require_once ('/usr/local/zk_agent/names/nameapi.php');
		$zkhost = new zkHost ();
		if (($r = getHostByKey ( $name, $zkhost )) !== 0) {
			echo "DB zkHost failed\n";
			$this->_logger->error ( 'DB zkHost failed ,name=' . $name );
			return false;
		}
		
		return ( array ) $zkhost;
	}
	
	/**
	 * 获取数据库配置信息
	 * @param unknown_type $section
	 * @return boolean|multitype:|Ambigous <>
	 */
	protected function getConfig($section = '') {
		$configFile = APP_PATH . '/config/config.ini';
	
		$configinfo = parse_ini_file ( $configFile, 1 );
		if (empty ( $configinfo )) {
			return false;
		}
	
		if ($section == '') {
			return $configinfo;
		}
	
		if (isset ( $configinfo [$section] )) {
			return $configinfo [$section];
		} else {
			return false;
		}
	}
	
}