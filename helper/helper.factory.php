<?php
require_once ROOT . '/helper/base.helper.php';
class HelperFactory {
	private static $helper = array ();
	private static $instance = null;
	
	public static function getInstance() {
		if (! self::$instance) {
			self::$instance = new self ();
		}
		return self::$instance;
	}
	
	public function getIndexHelper() {
		if (! isset ( self::$helper ['IndexHelper'] ) || ! self::$helper ['IndexHelper']) {
			require_once ROOT . '/helper/index.helper.php';
			self::$helper ['IndexHelper'] = new IndexHelper ();
		}
		return self::$helper ['IndexHelper'];
	}
	
	public function getAdminHelper() {
		if (! isset ( self::$helper ['AdminHelper'] ) || ! self::$helper ['AdminHelper']) {
			require_once ROOT . '/helper/admin.helper.php';
			self::$helper ['AdminHelper'] = new AdminHelper ();
		}
		return self::$helper ['AdminHelper'];
	}

}