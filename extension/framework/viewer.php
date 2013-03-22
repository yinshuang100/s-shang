<?php
class viewer {
	private $_viewer;
	private $_templatePath;
	private $_ext = 'phtml';
	private static $instance = null;
	public static function getInstance() {
		if (! self::$instance) {
			self::$instance = new self ();
		}
		return self::$instance;
	}
	
	public function render($templatePath, $template, &$viewer) {
		$this->_viewer = $viewer;
		$this->_templatePath = $templatePath;
		$this->segment (  $template );
	}
	
	public function segment($template) {
		$filePath = PUBLIC_VIEWER . $this->_templatePath . $template . '.' . $this->_ext;
		if (! is_file ( $filePath )) {
			die ( "模板不存在 " . $filePath );
		}
		include $filePath;
	}
	
	public function output($param) {
		return $param;
	}

}