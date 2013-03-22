<?php
class Router {
	private static $instance = null;
	private $extname = '.html';
	public static function getInstance() {
		if (! self::$instance) {
			self::$instance = new self ();
		}
		return self::$instance;
	}
	
	public function dispatch() {
		list ( $controller, $className, $actionName, $path ) = $this->init ();
		if (! is_file ( $path )) {
			die ( "路径不存在" . $path );
		}
		require_once $path;
		if (! class_exists ( $className, true )) {
			die ( "方法名不存在" . $className );
		}
		$obj = new $className ();
		if ($actionName && ! is_callable ( array ($obj, $actionName ) )) {
			die ( "action不存在" . $actionName );
		}
		if (in_array ( $actionName, array ($className, "execute", "__construct", "init", "before", "after" ) )) {
			die ( "非法action" . $className );
		}
		$obj->execute ( $controller, $actionName );
	}
	
	private function init() {
		$this->_check ();
		$controller = (isset ( $_GET ['c'] ) && ctype_alpha ( $_GET ['c'] )) ? strtolower ( trim ( $_GET ['c'] ) ) : "index";
		$className = $controller . "Controller";
		$actionName = $this->getActionName ();
		$path = PUBLIC_CONTROLLER . $className . ".php";
		return array ($controller, $className, $actionName, $path );
	}
	
	private function getActionName() {
		$action = isset ( $_GET ['a'] ) ? $_GET ['a'] : ('run' . $this->extname);
		$action = str_replace($this->extname, "", $action);
		return ctype_alpha ( $action ) ? strtolower ( trim ( $action ) ) : "run";
	}
	
	private function _check() {
		if (! defined ( 'PUBLIC_CONTROLLER' ) || ! defined ( 'PUBLIC_VIEWER' )) {
			die ( "you shoule config PUBLIC_CONTROLLER|PUBLIC_VIEWER" );
		}
	}

}