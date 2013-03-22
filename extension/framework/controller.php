<?php
class Controller {
	public $_viewer;
	private $_controller;
	private $_action;
	private $_templatepath;
	private $_template;
	public function execute($controller, $action) {
		self::init ( $controller, $action );
		$this->before ();
		$this->$action ();
		$this->after ();
		self::reader ();
	}
	private function init($controller, $action) {
		$this->_controller = $controller;
		$this->_action = $action;
		$this->_viewer = new stdClass ();
	}
	public function run() {
	
	}
	
	public function before() {
	
	}
	
	public function after() {
	
	}
	
	public function getAction() {
		return $this->_action;
	}
	
	private function reader() {
		require_once EXT_PATH . '/framework/viewer.php';
		viewer::getInstance ()->render ( $this->getTemplatePath (), $this->getTemplate (), $this->_viewer );
	}
	
	public function setTemplatePath($path) {
		$this->_templatepath = $path ? $path . '/' : '';
	}
	
	public function getTemplatePath() {
		return $this->_templatepath;
	}
	
	public function setTemplate($template) {
		$this->_template = $template;
	}
	
	private function getTemplate() {
		return $this->_template ? $this->_template : $this->_action;
	}
	
	public function getParam($key) {
		return (isset ( $_REQUEST [$key] )) ? $_REQUEST [$key] : '';
	}

}