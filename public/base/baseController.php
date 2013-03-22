<?php
require_once EXT_PATH . '/framework/controller.php';
class BaseController extends Controller {
	
	public function before() {
	}
	
	public function after() {
	
	}
	
	public function getError() {
		require_once EXT_PATH . '/ext.error.service.php';
		return new errorService ();
	}
	
	public function getModelFactory() {
		require_once MODEL_PATH . '/model.factory.php';
		return ModelFactory::getInstance ();
	}
	
	public function getHelperFactory() {
		require_once HELPER_PATH . '/helper.factory.php';
		return HelperFactory::getInstance ();
	}

}


