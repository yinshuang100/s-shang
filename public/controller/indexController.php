<?php
class IndexController extends BaseController {
	private $_baseurl;
	private $_config;
	private $_pageTitle;
	private $_allClass;
	
	public function before() {
		parent::before ();
		$this->init ();
	}
	
	public function after() {
		parent::after ();
		$title = $this->_pageTitle ? sprintf ( '%s%s', $this->_pageTitle, $this->_config ['webtitle'] ) : $this->_config ['webtitle'];
		$this->_viewer->title = $title;
	}
	
	private function init() {
		$htmPath = 'misc/';
		$this->_perpage = 20;
		$this->setTemplatePath ( 'index' );
		$this->_viewer->csspath = $htmPath . 'css';
		$this->_viewer->jspath = $htmPath . 'js';
		$this->_viewer->imgpath = $htmPath . 'images';
		$this->_viewer->config = $this->_config = $this->getModelFactory ()->getConfigModel ()->getGlobalConfig ();
		$this->_viewer->baseurl = $this->_baseurl = BASEURL;
		$this->checkSiteStatus ( $this->_config );
		$this->_viewer->action = $this->getAction ();
		$this->_viewer->friendlinks = $this->getModelFactory ()->getConfigModel ()->getByKey ( 'friendlink-list' );
	
	}
	
	public function checkSiteStatus($configInfo) {
		if (! isset ( $configInfo ['webstatus'] ) || ! $configInfo ['webstatus']) {
			exit ( "站点维护中，请稍候访问..." );
		}
	}
	
	/**
	 * 首页
	 */
	public function run() {
	
	}
	
	/**
	 * 关于我们
	 */
	public function about() {
		$this->setTitle ( '关于我们' );
		$this->_viewer->rightmenu = 'about';
	}
	
	/**
	 * 产品设计
	 */
	public function design() {
		list ( $cid, $page ) = S::gp ( array ('cid', 'page' ) );
		list ( $cid, $page, $perpage ) = array (intval ( $cid ), (intval ( $page ) ? intval ( $page ) : 1), 10 );
		$fields = array ();
		$cid && $fields ['category'] = $cid;
		$count = $this->getModelFactory ()->getProductModel ()->count ( $fields );
		$this->_viewer->list = ! $count ? array () : $this->getModelFactory ()->getProductModel ()->getList ( $fields, $page, $perpage );
		$this->_viewer->pageNav = Ext_Static_Common_Service::getPageNav ( $count, $page, ceil ( $count / $perpage ), $this->buildLink ( 'design', $fields ) . '&' );
		
		$this->_viewer->leftClass = $this->getModelFactory ()->getProductCategoryModel ()->getAllCategory ();
		$this->_viewer->classId = $cid;
		
		$this->setTitle ( '产品设计' );
		$this->_viewer->rightmenu = 'design';
	}
	
	/**
	 * 客户案例
	 */
	public function cases() {
		list ( $cid, $page ) = S::gp ( array ('cid', 'page' ) );
		list ( $cid, $page, $perpage ) = array (intval ( $cid ), (intval ( $page ) ? intval ( $page ) : 1), 10 );
		$fields = array ();
		$cid && $fields ['category'] = $cid;
		$count = $this->getModelFactory ()->getCaseModel ()->count ( $fields );
		$this->_viewer->list = ! $count ? array () : $this->getModelFactory ()->getCaseModel ()->getList ( $fields, $page, $perpage );
		$this->_viewer->pageNav = Ext_Static_Common_Service::getPageNav ( $count, $page, ceil ( $count / $perpage ), $this->buildLink ( 'cases', $fields ) . '&' );
		
		$this->_viewer->leftClass = $this->getModelFactory ()->getCaseCategoryModel ()->getAllCategory ();
		$this->_viewer->classId = $cid;
		$this->setTitle ( '客户案例' );
		$this->_viewer->rightmenu = 'cases';
	}
	
	public function casedetail() {
		list ( $id ) = S::gp ( array ('id' ) );
		$id = intval ( $id );
		$detail = $id ? $this->getModelFactory ()->getCaseModel ()->get ( $id ) : array ();
		$this->_viewer->detail = $detail;
		$this->_viewer->leftClass = $this->getModelFactory ()->getCaseCategoryModel ()->getAllCategory ();
		$this->_viewer->classId = isset ( $detail ['category'] ) ? intval ( $detail ['category'] ) : 0;
		isset ( $detail ['title'] ) && $this->setTitle ( $detail ['title'] );
		$this->setTitle ( '客户案例' );
		$this->_viewer->rightmenu = 'cases';
	}
	
	/**
	 * 电子商务
	 */
	public function ecmm() {
		list ( $cid, $page ) = S::gp ( array ('cid', 'page' ) );
		list ( $cid, $page, $perpage ) = array (intval ( $cid ), (intval ( $page ) ? intval ( $page ) : 1), 10 );
		$fields = array ();
		$cid && $fields ['category'] = $cid;
		$count = $this->getModelFactory ()->getEcmmModel ()->count ( $fields );
		$this->_viewer->list = ! $count ? array () : $this->getModelFactory ()->getEcmmModel ()->getList ( $fields, $page, $perpage );
		$this->_viewer->pageNav = Ext_Static_Common_Service::getPageNav ( $count, $page, ceil ( $count / $perpage ), $this->buildLink ( 'ecmm', $fields ) . '&' );
		
		$this->_viewer->leftClass = $this->getModelFactory ()->getEcmmCategoryModel ()->getAllCategory ();
		$this->_viewer->classId = $cid;
		
		$this->setTitle ( '电子商务' );
		$this->_viewer->rightmenu = 'ecmm';
	}
	
	/**
	 * 服务项目
	 */
	public function service() {
		$this->setTitle ( '服务项目' );
		$this->_viewer->rightmenu = 'service';
	}
	
	/**
	 * 合作模式
	 */
	public function partner() {
		$this->setTitle ( '合作模式' );
		$this->_viewer->rightmenu = 'partner';
	}
	
	/**
	 * 新闻动态
	 */
	public function news() {
		list ( $cid, $page ) = S::gp ( array ('cid', 'page' ) );
		list ( $cid, $page, $perpage ) = array (intval ( $cid ), (intval ( $page ) ? intval ( $page ) : 1), 20 );
		$fields = array ();
		$cid && $fields ['category'] = $cid;
		$count = $this->getModelFactory ()->getArticlesModel ()->count ( $fields );
		$this->_viewer->list = ! $count ? array () : $this->getModelFactory ()->getArticlesModel ()->getList ( $fields, $page, $perpage );
		$this->_viewer->pageNav = Ext_Static_Common_Service::getPageNav ( $count, $page, ceil ( $count / $perpage ), $this->buildLink ( 'news', $fields ) . '&' );
		
		$this->_viewer->leftClass = $this->getModelFactory ()->getArticlesCategoryModel ()->getAllCategory ();
		$this->_viewer->classId = $cid;
		
		$this->setTitle ( '新闻动态' );
		$this->_viewer->rightmenu = 'news';
	}
	
	public function newsinfo() {
		list ( $id ) = S::gp ( array ('id' ) );
		$id = intval ( $id );
		$detail = $id ? $this->getModelFactory ()->getArticlesModel ()->get ( $id ) : array ();
		$this->_viewer->detail = $detail;
		$this->_viewer->leftClass = $this->getModelFactory ()->getArticlesCategoryModel ()->getAllCategory ();
		$this->_viewer->classId = isset ( $detail ['category'] ) ? intval ( $detail ['category'] ) : 0;
		isset ( $detail ['title'] ) && $this->setTitle ( $detail ['title'] );
		$this->setTitle ( '新闻动态' );
		$this->_viewer->rightmenu = 'news';
	}
	
	/**
	 * 联系我们
	 */
	public function contact() {
		$this->setTitle ( '联系我们' );
		$this->_viewer->rightmenu = 'contact';
	}
	
	private function setTitle($data) {
		$this->_pageTitle .= sprintf ( '%s-', S::isArray ( $data ) ? implode ( '-', $data ) : $data );
	}
	
	private function jsonOutput($callback, $data) {
		@header ( "Content-Type:text/html; charset=utf-8" );
		$result = json_encode ( array ('data' => $data ) );
		if ($callback)
			print_r ( sprintf ( '%s(%s)', $callback, $result ) );
		else
			print_r ( $result );
		exit ();
	}
	
	private function buildLink($action, $params = array()) {
		return $this->_baseurl . '?' . http_build_query ( $params ) . '&' . 'a=' . $action . '.html';
	}

}