<?php
/**
 * @desc 	App view
 * @name 	View 
 * @author 	<bayliang@tencent.com>
 * @date 	2012-9-5
 */
class View {
	public $_filePath = null;
	public function __construct() {
	}
	public function assign($spec, $value) {
		if (is_string ( $spec )) {
			
			$this->$spec = $value;
		} elseif (is_array ( $spec )) {
			
			foreach ( $spec as $key => $val ) {
				$this->$key = $val;
			}
		}
		
		return $this;
	}
	public function setFilePath($path) {
		$this->_filePath = $path;
	}
	public function getFilePath() {
		if (isset ( $this->_filePath )) {
			return $this->_filePath;
		} else {
			return false;
		}
	}
	protected function _runFile($file) {
		$file = $this->getFilePath () . "/$file.html";
		
		include ($file);
	}
	public function render($file, $needReturn = false) {
		ob_start ();
		$this->_runFile ( $file );
		if ($needReturn !== false) {
			return ob_get_clean ();
		} else {
			echo ob_get_clean ();
		}
	}
	public function __set($key, $val) {
		if ('_' != substr ( $key, 0, 1 )) {
			$this->$key = $val;
			return;
		} else {
			return false;
		}
	}
}
