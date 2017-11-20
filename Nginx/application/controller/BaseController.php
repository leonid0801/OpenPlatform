<?php
/**
 * Base Controller
 *        
 */
class BaseController {
	
    //jsloader返回变量名
    protected $jsloader_var = '';
    
	public function __construct() {
		/**
		 * 增加鉴权模块  @todo
		 */
		$this->_init ();
	}
	
	/**
	 * 子类无需定义__construct 只需实现此方法
	 * 控制器初始化接口，由子类实现
	 */
	protected function _init() {
	}
	
	
	/**
	 * View 实例初始化
	 */
	protected function _getView() {
		global $_CONTEXT;
		$path = APP_PATH . '/application/view/templates/';
		
		$view = new View ();
		$view->setFilePath ( $path );
		$view->web_url = 'http://'.$_SERVER['HTTP_HOST'];
		$view->server_host= $_SERVER['HTTP_HOST'];
		//$view->module=$_CONTEXT['module'];
		//$view->controller=$_CONTEXT['controller'];
		//$view->action=$_CONTEXT['action'];
		
		
		
		//设置静态资源的版本号
        $view->static_version=STATIC_VERSION;
		return $view;
	}
	
	/**
	 * 获取配置数据
	 * @param string $section
	 * @return boolean|multitype:|Ambigous <>
	 */
	protected function _getConfig($section = '') {
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
	
	
	/**
	 * 访问接口收集返回数据
	 *
	 * @param unknown $url
	 * @param string $files
	 * @return mixed
	 */
	public function curl($url, $files = "") {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $files );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_COOKIEFILE, "" );
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, true );
		$result = curl_exec ( $ch );
		curl_close ( $ch );
		return $result;
	}
	
	
	public function buildQuery($queryData)
	{
		$r = array();
		
		foreach ($queryData as $k => $v) {
			$ke = rawurlencode($k);
			$ve = rawurlencode($v);
			
			array_push($r, "$ke=$ve");
		}
		
		return implode("&", $r);
	}
	
	public function buildUrl($baseUrl, $queryData, $fragment = NULL)
	{
		$qs = $this->buildQuery($queryData);
		
		$url = "$baseUrl?$qs";
		
		if ($fragment !== NULL) {
			$url = "$url#$fragment";
		}
		
		return $url;
	}
}
