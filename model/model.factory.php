<?php
! defined ( 'WEBNAME' ) && exit ( 'Forbidden' );
require_once MODEL_PATH . '/base.model.php';
class ModelFactory {
	private static $model = array ();
	private static $instance = null;
	
	public static function getInstance() {
		if (! self::$instance) {
			self::$instance = new self ();
		}
		return self::$instance;
	}
	
	public function getCommonUploadModel() {
		if (! isset ( self::$model ['CommonUploadModel'] ) || ! self::$model ['CommonUploadModel']) {
			require_once MODEL_PATH . '/common.upload.model.php';
			self::$model ['CommonUploadModel'] = new CommonUploadModel ();
		}
		return self::$model ['CommonUploadModel'];
	}
	
	public function getAdminModel() {
		if (! isset ( self::$model ['AdminModel'] ) || ! self::$model ['AdminModel']) {
			require_once MODEL_PATH . '/admin.model.php';
			self::$model ['AdminModel'] = new AdminModel ();
		}
		return self::$model ['AdminModel'];
	}
	
	public function getConfigModel() {
		if (! isset ( self::$model ['ConfigModel'] ) || ! self::$model ['ConfigModel']) {
			require_once MODEL_PATH . '/config.model.php';
			self::$model ['ConfigModel'] = new ConfigModel ();
		}
		return self::$model ['ConfigModel'];
	}
	
	
	
	public function getProductCategoryModel() {
		if (! isset ( self::$model ['ProductCategoryModel'] ) || ! self::$model ['ProductCategoryModel']) {
			require_once MODEL_PATH . '/product.category.model.php';
			self::$model ['ProductCategoryModel'] = new ProductCategoryModel ();
		}
		return self::$model ['ProductCategoryModel'];
	}
	
	public function getProductModel() {
		if (! isset ( self::$model ['ProductModel'] ) || ! self::$model ['ProductModel']) {
			require_once MODEL_PATH . '/product.model.php';
			self::$model ['ProductModel'] = new ProductModel ();
		}
		return self::$model ['ProductModel'];
	}
	
	public function getProductImgModel() {
		if (! isset ( self::$model ['ProductImgModel'] ) || ! self::$model ['ProductImgModel']) {
			require_once MODEL_PATH . '/product.img.model.php';
			self::$model ['ProductImgModel'] = new ProductImgModel ();
		}
		return self::$model ['ProductImgModel'];
	}


}