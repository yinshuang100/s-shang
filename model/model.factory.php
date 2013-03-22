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
	
	public function getArticlesCategoryModel() {
		if (! isset ( self::$model ['ArticlesCategoryModel'] ) || ! self::$model ['ArticlesCategoryModel']) {
			require_once MODEL_PATH . '/articles.category.model.php';
			self::$model ['ArticlesCategoryModel'] = new ArticlesCategoryModel ();
		}
		return self::$model ['ArticlesCategoryModel'];
	}
	
	public function getArticlesModel() {
		if (! isset ( self::$model ['ArticlesModel'] ) || ! self::$model ['ArticlesModel']) {
			require_once MODEL_PATH . '/articles.model.php';
			self::$model ['ArticlesModel'] = new ArticlesModel ();
		}
		return self::$model ['ArticlesModel'];
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
	
	public function getCaseCategoryModel() {
		if (! isset ( self::$model ['CaseCategoryModel'] ) || ! self::$model ['CaseCategoryModel']) {
			require_once MODEL_PATH . '/case.category.model.php';
			self::$model ['CaseCategoryModel'] = new CaseCategoryModel ();
		}
		return self::$model ['CaseCategoryModel'];
	}
	
	public function getCaseModel() {
		if (! isset ( self::$model ['CaseModel'] ) || ! self::$model ['CaseModel']) {
			require_once MODEL_PATH . '/case.model.php';
			self::$model ['CaseModel'] = new CaseModel ();
		}
		return self::$model ['CaseModel'];
	}
	
	public function getEcmmCategoryModel() {
		if (! isset ( self::$model ['EcmmCategoryModel'] ) || ! self::$model ['EcmmCategoryModel']) {
			require_once MODEL_PATH . '/ecmm.category.model.php';
			self::$model ['EcmmCategoryModel'] = new EcmmCategoryModel ();
		}
		return self::$model ['EcmmCategoryModel'];
	}
	
	public function getEcmmModel() {
		if (! isset ( self::$model ['EcmmModel'] ) || ! self::$model ['EcmmModel']) {
			require_once MODEL_PATH . '/ecmm.model.php';
			self::$model ['EcmmModel'] = new EcmmModel ();
		}
		return self::$model ['EcmmModel'];
	}

}