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
	
	public function getProductHelper() {
		if (! isset ( self::$helper ['ProductHelper'] ) || ! self::$helper ['ProductHelper']) {
			require_once ROOT . '/helper/product.helper.php';
			self::$helper ['ProductHelper'] = new ProductHelper ();
		}
		return self::$helper ['ProductHelper'];
	}

}