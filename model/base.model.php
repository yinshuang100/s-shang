<?php
! defined ( 'WEBNAME' ) && exit ( 'Forbidden' );
require_once EXT_PATH . '/framework/model.php';
require_once EXT_PATH . '/ext.static.common.service.php';
class BaseModel extends Model {

	public function getModelFactory() {
		require_once MODEL_PATH . '/model.factory.php';
		return ModelFactory::getInstance ();
	}
}