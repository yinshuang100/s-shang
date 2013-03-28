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
	 * 客户案例
	 */
	public function cases() {
		list ( $cid, $page ) = S::gp ( array ('cid', 'page' ) );
		list ( $cid, $page, $perpage ) = array (intval ( $cid ), (intval ( $page ) ? intval ( $page ) : 1), 10 );
		$allClass = $this->getModelFactory ()->getProductCategoryModel ()->getAllCategory ();
		$fields = array ();
		$cid = isset($allClass[$cid]) ? $cid : key($allClass);
		$cid && $fields ['category'] = $cid;
		$count = $this->getModelFactory ()->getProductModel ()->count ( $fields );
		$this->_viewer->list = ! $count ? array () : $this->getModelFactory ()->getProductModel ()->getList ( $fields, $page, $perpage );
		$this->_viewer->pageNav = Ext_Static_Common_Service::getPageNav ( $count, $page, ceil ( $count / $perpage ), $this->buildLink ( 'cases', $fields ) . '&' );
		
		$this->_viewer->allclass = $allClass;
		$this->_viewer->classId = $cid;
		$this->setTitle ( $allClass[$cid]['name'] );
	}
	
	public function design() {
		list ( $id ) = S::gp ( array ('id' ) );
		$id = intval ( $id );
		$detail = $id ? $this->getModelFactory ()->getProductModel ()->get ( $id ) : array ();
		$this->_viewer->detail = $detail;
		$this->_viewer->smallImgs = $this->getModelFactory ()->getProductImgModel ()->getByPid ($id);
		$this->_viewer->allclass = $allClass = $this->getModelFactory ()->getProductCategoryModel ()->getAllCategory ();
		$this->_viewer->classId = $cid = isset ( $detail ['category'] ) ? intval ( $detail ['category'] ) : key($allClass);
		isset ( $detail ['title'] ) && $this->setTitle ( $detail ['title'] );
		$this->setTitle ( $allClass[$cid]['name'] );
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