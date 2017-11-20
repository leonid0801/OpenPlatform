<?php
class Application {
	private $_module = null;
	private $_controller = null;
	private $_action = null;
	private $_classname = null;
	public function __construct() {
		$this->_init ();
	}
	private function _init() {
		
		/*
		$path = APP_PATH . '/library' . PATH_SEPARATOR;
		$path .= APP_PATH . '/library/framework' . PATH_SEPARATOR;
		$path .= APPLICATION_PATH . '/model' . PATH_SEPARATOR;
		$path .= APPLICATION_PATH . '/controller';
		set_include_path ( get_include_path () . PATH_SEPARATOR . $path );
		
		$this->_initAutoload ();
		
		*/
	}
	private function _initAutoload() {
		spl_autoload_register ( array ( 
				$this,
				'_appAutoloader' 
		) );
	}
	private function _appAutoloader($class) {
		$filename = str_replace('_','/',$class);
		$filepath = $filename . '.php';
		include $filepath;
	}
	private function _parseRequest() {
	
		///$wx = new WxController();
		///$view = $wx->test();
	
		
		
		$requestPath = preg_replace ( '/\?.*$/', '', $_SERVER ['REQUEST_URI'] );
		//$pathArr = explode ( '/', preg_replace ( '/\/+/', '/', trim ( $requestPath, '/' ) ) );
		$pathArr = explode ( '/', trim ( $requestPath, '/' ) );

		/**
		 * 如果小于3 则转默认首页
		 */
		if (count ( $pathArr ) < 3) {
			$filePath = APP_PATH . '/controller/Index/Index.php';
			$this->_classname = 'Index_Index';
			$this->_action = 'index';
		} else {
			//
			$module = $this->_module = ucwords ( trim ( $pathArr [1] ) );
			$controller = $this->_controller = ucwords ( trim ( $pathArr [2] ) );
			$action = $this->_action = trim ( $pathArr [3] );
			$this->_classname  = $module . '_' . $controller;
			$filePath = APP_PATH . '/application/controller/' . $module . '/' . $controller.'.php';
			
			//echo APP_PATH;  # /data1/www/htdocs/111/leonid/1
			
		}

		
		/**
		 * 解析除了以上模块的其他参数
		 * 加入_GET参数组
		 */
		
		$count = count ( $pathArr );
		for($i = 3; $i < $count; $i ++) {
			$_GET [rawurldecode ( $pathArr [$i] )] = isset ( $pathArr [++ $i] ) ? rawurldecode ( $pathArr [$i] ) : '';
		}

		if (file_exists ( $filePath )) {
			require $filePath;
		} else {
			//$this->page_404 ();
			echo "file not found!";
			die ();
		}
		
	}
	
	//  路由测试 https://wtmb.online/index.php/api/wx/test
	public function run() {
		$this->_parseRequest ();
		global $_CONTEXT;
		$module = strtolower ( $this->_module );
		$controller = strtolower ( $this->_controller );
		$action = strtolower ( $this->_action );
		$_CONTEXT ['module'] = $module;
		$_CONTEXT ['controller'] = $controller;
		$_CONTEXT ['action'] = $action;
		$app = new $this->_classname ();
		$app->{$this->_action} ();
	}

}