<?php
class AdminController extends BaseController {
	
	private $_configInfo;
	private $_userInfo;
	private $_baseurl;
	private $_perpage;
	private $_class;
	private $_menus;
	private $_notNeedCheckUserInfo = array ('login' );
	
	public function before() {
		parent::before ();
		$this->init ();
	}
	
	private function init() {
		$baseurl = '';
		$htmPath = 'misc/';
		$templatepath = 'admin';
		$this->_perpage = 15;
		$this->_viewer->editor = $htmPath . 'kindeditor-4.1.2';
		$this->_viewer->csspath = $htmPath . 'admin/css';
		$this->_viewer->jspath = $htmPath . 'js';
		$this->_viewer->imgpath = $htmPath . 'admin/image';
		$this->_viewer->baseurl = $this->_baseurl = '?c=admin&';
		$this->_viewer->formaction = $this->buildLink ( $this->getAction () );
		$this->setTemplatePath ( $templatepath );
		if (! S::inArray ( $this->getAction (), $this->_notNeedCheckUserInfo ))
			$this->checkUserInfo ();
		$this->_viewer->userInfo = $this->_userInfo;
		$this->_viewer->configInfo = $this->_configInfo = $this->getModelFactory ()->getConfigModel ()->getGlobalConfig ();
		$this->_viewer->title = (isset ( $this->_configInfo ['webtitle'] ) ? $this->_configInfo ['webtitle'] : '') . '后台管理';
		$this->_viewer->menus = $this->_menus = array('product' => '产品');
	}
	
	private function checkUserInfo() {
		$this->_userInfo = $this->getAdminModel ()->isLogin ();
		if (! S::isArray ( $this->_userInfo )) {
			$this->getError ()->obHeader ( $this->buildLink ( 'login' ) );
			exit ();
		}
	}
	
	public function login() {
		list ( $step ) = S::gp ( array ('step' ) );
		if (intval ( $step ) == 2) {
			list ( $loginuser, $loginpassword ) = S::gp ( array ('loginuser', 'loginpassword' ) );
			$loginuser = trim ( $loginuser );
			$loginpassword = trim ( $loginpassword );
			if (! $loginuser || ! $loginpassword) {
				$this->getError ()->showError ( "信息未填写完整!" );
			}
			if ($this->getAdminModel ()->login ( $loginuser, $loginpassword )) {
				$this->getError ()->obHeader ( $this->buildLink ( 'run' ) );
			} else {
				$this->getError ()->showError ( "登录失败!" );
			}
		}
	}
	
	/**
	 * 注销登录
	 */
	public function loginout() {
		if ($this->getAdminModel ()->loginOut ()) {
			$this->getError ()->obHeader ( $this->buildLink ( 'login' ) );
		}
		$this->getError ()->showError ( "注销失败!" );
	}
	
	public function run() {
		$this->setMenuBar ( 'index' );
	}
	
	public function webconfig() {
		list ( $step ) = S::gp ( array ('step' ) );
		$step = intval ( $step );
		if ($step == 2) {
			list ( $callback ) = S::gp ( array ('callback' ) );
			$configKeys = array ('webstatus', 'webtitle', 'webdomain', 'webseokeyword', 'webseodescription', 'sns_sina_blog', 'sns_weibo', 'sns_qq', 'sns_tel', 'icp', 'copyright' );
			$configValue = S::gp ( $configKeys );
			$noTripKeys = array ('site-access-code' );
			foreach ( $noTripKeys as $v ) {
				$configKeys [] = $v;
				$configValue [] = S::getHtmlGP ( $v );
			}
			$config = array ();
			foreach ( $configKeys as $k => $v ) {
				$config [$v] = $configValue [$k];
			}
			$result = $this->getModelFactory ()->getConfigModel ()->replaceMulti ( $config );
			$msg = $result ? "配置修改成功" : "配置修改失败，请重试";
			$this->jsonOutput ( $callback, array ($result, $msg ) );
		}
		$this->setMenuBar ( 'basic' );
	}
	
	public function dataconfig() {
		list ( $step ) = S::gp ( array ('step' ) );
		$step = intval ( $step );
		if ($step == 2) {
			list ( $callback ) = S::gp ( array ('callback' ) );
			$configKeys = array ('data-ecmm-pdf-downurl', 'data-case-pdf-downurl', 'data-contact-address', 'data-contact-address-en', 'data-contact-tel', 'data-contact-fax', 'data-contact-email' );
			$configValue = S::gp ( $configKeys );
			$config = array ();
			foreach ( $configKeys as $k => $v ) {
				$config [$v] = $configValue [$k];
			}
			$result = $this->getModelFactory ()->getConfigModel ()->replaceMulti ( $config );
			$msg = $result ? "配置修改成功" : "配置修改失败，请重试";
			$this->jsonOutput ( $callback, array ($result, $msg ) );
		}
		$this->setMenuBar ( 'basic' );
	}
	
	
	//====================Product Start================
	

	/**
	 * product类别管理
	 */
	public function productcategory() {
		list ( $step ) = S::gp ( array ('step' ) );
		if (intval ( $step ) == 2) {
			list ( $callback, $seq ) = S::gp ( array ('callback', 'classseq' ) );
			if (! S::isArray ( $seq ))
				$this->jsonOutput ( $callback, array (false, '信息未填写完整' ) );
			foreach ( $seq as $id => $v ) {
				$this->getModelFactory ()->getProductCategoryModel ()->updateCategory ( $id, array ('seq' => intval ( $v ) ) );
			}
			$this->jsonOutput ( $callback, array (true, '顺序修改成功' ) );
		}
		$this->_viewer->class = $category = $this->getModelFactory ()->getProductCategoryModel ()->getAllCategory ();
		$this->_viewer->count = $this->getModelFactory ()->getProductModel ()->countByCategoryIds ( array_keys ( $category ) );
		$this->setMenuBar ( 'product', 'productcategory' );
		$this->setTemplate ( 'product.category' );
	}
	
	/**
	 * product类别添加
	 */
	public function productcategoryadd() {
		list ( $step ) = S::gp ( array ('step' ) );
		if (intval ( $step ) == 2) {
			list ( $callback, $pid, $name, $name_en, $seq ) = S::gp ( array ('callback', 'pid', 'classname', 'classname_en', 'classseq' ) );
			list ( $name, $name_en, $seq ) = array (trim ( $name ), trim ( $name_en ), intval ( $seq ) );
			if ($name == '')
				$this->jsonOutput ( $callback, array (false, '类别名称不能为空' ) );
			$fields = array ('pid' => intval ( $pid ), 'name' => $name, 'name_en' => $name_en, 'seq' => $seq );
			$result = $this->getModelFactory ()->getProductCategoryModel ()->addCategory ( $fields );
			$this->jsonOutput ( $callback, $result ? array (true, '类别添加成功' ) : array (true, '类别添加失败，请重试' ) );
		}
		$this->_viewer->classInfo = array();
		$this->_viewer->class = $this->getModelFactory ()->getProductCategoryModel ()->getAllCategory ();
		$this->setMenuBar ( 'product', 'productcategory' );
		$this->setTemplate ( 'product.category.modify' );
	}
	
	/**
	 * 类别修改
	 */
	public function productcategorymodify() {
		list ( $step, $id ) = S::gp ( array ('step', 'id' ) );
		if (intval ( $step ) == 2) {
			list ( $callback, $pid, $name, $name_en, $seq ) = S::gp ( array ('callback', 'pid', 'classname', 'classname_en', 'classseq' ) );
			list ( $name, $name_en, $seq ) = array (trim ( $name ), trim ( $name_en ), intval ( $seq ) );
			if ($name == '')
				$this->jsonOutput ( $callback, array (false, '类别名称不能为空' ) );
			$fields = array ('pid' => intval ( $pid ), 'name' => $name, 'name_en' => $name_en, 'seq' => $seq );
			$result = $this->getModelFactory ()->getProductCategoryModel ()->updateCategory ( $id, $fields );
			$this->jsonOutput ( $callback, $result ? array (true, '类别修改成功' ) : array (true, '类别修改失败，请重试' ) );
		}
		$this->_viewer->class = $this->getModelFactory ()->getProductCategoryModel ()->getAllCategory ();
		$this->_viewer->classInfo = $this->getModelFactory ()->getProductCategoryModel ()->get ( intval ( $id ) );
		$this->setMenuBar ( 'product', 'productcategory', 'productcategoryadd' );
		$this->setTemplate ( 'product.category.modify' );
	}
	
	/**
	 * 类别删除
	 */
	public function productcategorydelete() {
		list ( $callback, $id ) = S::gp ( array ('callback', 'id' ) );
		$classInfo = $this->getModelFactory ()->getProductCategoryModel ()->get ( intval ( $id ) );
		if (! S::isArray ( $classInfo ))
			$this->jsonOutput ( $callback, array (false, '操作异常' ) );
		$this->getModelFactory ()->getProductModel ()->deleteByCategory ( $id );
		$result = $this->getModelFactory ()->getProductCategoryModel ()->deleteCategory ( $id );
		$this->jsonOutput ( $callback, array (true, '类别删除成功' ) );
	}
	
	/**
	 * products列表管理
	 */
	public function product() {
		list ( $page, $step, $category ) = S::gp ( array ('page', 'step', 'category' ) );
		list ( $page, $category, $fields ) = array (intval ( $page ), intval ( $category ), array () );
		$page < 1 && $page = 1;
		$category && $fields ['category'] = $category;
		$count = $this->getModelFactory ()->getProductModel ()->count ( $fields );
		$this->_viewer->list = ! $count ? array () : $this->getModelFactory ()->getProductModel ()->getList ( $fields, $page, $this->_perpage );
		$this->_viewer->pageNav = Ext_Static_Common_Service::getPageNav ( $count, $page, ceil ( $count / $this->_perpage ), $this->buildLink ( 'product', $fields ) );
		$this->_viewer->class = $this->getModelFactory ()->getProductCategoryModel ()->getAllCategory ();
		$this->setMenuBar ( 'product', 'product' );
	}
	
	/**
	 * 上下线
	 */
	public function productpass() {
		list ( $step, $callback ) = S::gp ( array ('step', 'callback' ) );
		if (intval ( $step ) == 2) {
			list ( $aid, $callback, $stat ) = S::gp ( array ('aid', 'callback', 'stat' ) );
			if ($aid == '')
				$this->jsonOutput ( $callback, array (false, '请选择欲操作对象' ) );
			$bool = $this->getModelFactory ()->getProductModel ()->update ( $aid, array ('status' => intval ( $stat ) ) );
			$this->jsonOutput ( $callback, $bool ? array (true, '' ) : array (false, '操作失败，请重试' ) );
		}
		$this->jsonOutput ( $callback, array (false, '异常操作' ) );
	}
	
	/**
	 * 类别移动
	 */
	public function productmove() {
		list ( $step, $callback ) = S::gp ( array ('step', 'callback' ) );
		if (intval ( $step ) == 2) {
			list ( $aid, $callback, $category ) = S::gp ( array ('aid', 'callback', 'newclass' ) );
			if ($aid == '')
				$this->jsonOutput ( $callback, array (false, '请选择欲操作对象' ) );
			$bool = $this->getModelFactory ()->getProductModel ()->update ( $aid, array ('category' => intval ( $category ) ) );
			$this->jsonOutput ( $callback, $bool ? array (true, '' ) : array (false, '操作失败，请重试' ) );
		}
		$this->jsonOutput ( $callback, array (false, '异常操作' ) );
	}
	
	/**
	 * 删除
	 */
	public function productdelete() {
		list ( $step, $callback ) = S::gp ( array ('step', 'callback' ) );
		if (intval ( $step ) == 2) {
			list ( $aid, $callback ) = S::gp ( array ('aid', 'callback' ) );
			if ($aid == '')
				$this->jsonOutput ( $callback, array (false, '请选择欲操作对象' ) );
			$bool = $this->getModelFactory ()->getProductModel ()->delete ( $aid );
			$this->jsonOutput ( $callback, $bool ? array (true, '' ) : array (false, '操作失败，请重试' ) );
		}
		$this->jsonOutput ( $callback, array (false, '异常操作' ) );
	}
	
	/**
	 * 新增
	 */
	public function productadd() {
		list ( $step ) = S::gp ( array ('step' ) );
		if (intval ( $step ) == 2) {
			list ( $callback, $pid, $markno, $title, $link, $status, $thumbpic, $summary ) = S::gp ( array ('callback', 'pid', 'markno', 'title', 'link', 'status', 'thumbpic', 'summary' ) );
			list ( $pid, $markno, $title, $link, $status, $thumbpic, $summary ) = array (intval ( $pid ), trim ( $markno ), trim ( $title ), Ext_Static_Common_Service::parseDomainName ( trim ( $link ) ), intval ( $status ), trim ( $thumbpic ), trim ( $summary ) );
			if ($title == '')
				$this->jsonOutput ( $callback, array (false, '名称不能为空' ) );
			$fields = array ('category' => intval ( $pid ), 'title' => $title, 'link' => ($link ? 'http://' . $link : ''), 'status' => $status, 'thumbpic' => $thumbpic, 'summary' => $summary );
			$result = $this->getModelFactory ()->getProductModel ()->add ( $fields );
			$this->jsonOutput ( $callback, $result ? array (true, '添加成功!' ) : array (false, '添加失败，请重试' ) );
		}
		$this->_viewer->class = $this->getModelFactory ()->getProductCategoryModel ()->getAllCategory ();
		$this->setMenuBar ( 'product', 'productadd' );
		$this->setTemplate ( 'product.modify' );
	}
	
	/**
	 * 修改
	 */
	public function productmodify() {
		list ( $step, $id ) = S::gp ( array ('step', 'id' ) );
		$info = $this->getModelFactory ()->getProductModel ()->get ( $id );
		if (intval ( $step ) == 2) {
			list ( $callback, $pid, $markno, $title, $link, $status, $thumbpic, $summary ) = S::gp ( array ('callback', 'pid', 'markno', 'title', 'link', 'status', 'thumbpic', 'summary' ) );
			list ( $pid, $markno, $title, $link, $status, $thumbpic, $summary ) = array (intval ( $pid ), trim ( $markno ), trim ( $title ), Ext_Static_Common_Service::parseDomainName ( trim ( $link ) ), intval ( $status ), trim ( $thumbpic ), trim ( $summary ) );
			if ($title == '')
				$this->jsonOutput ( $callback, array (false, '名称不能为空' ) );
			$fields = array ('category' => intval ( $pid ), 'title' => $title, 'link' => ($link ? 'http://' . $link : ''), 'status' => $status, 'thumbpic' => $thumbpic, 'summary' => $summary );
			$result = $this->getModelFactory ()->getProductModel ()->update ( $id, $fields );
			$this->jsonOutput ( $callback, $result ? array (true, '修改成功!' ) : array (false, '修改失败，请重试-4' ) );
		}
		if (! S::isArray ( $info ))
			$this->getError ()->showError ( "信息不存在!" );
		$this->_viewer->class = $this->getModelFactory ()->getProductCategoryModel ()->getAllCategory ();
		$this->_viewer->info = $info;
		$this->setMenuBar ( 'product', 'productadd' );
		$this->setTemplate ( 'product.modify' );
	}
	
	//====================Product End================
	
	

	#=================================帐号管理=====================================
	public function manager() {
		list ( $page ) = S::gp ( array ('page' ) );
		list ( $page ) = array (intval ( $page ) );
		$page < 1 && $page = 1;
		$count = $this->getModelFactory ()->getAdminModel ()->count ();
		$this->_viewer->list = ! $count ? array () : $this->getModelFactory ()->getAdminModel ()->getList ( $page, $this->_perpage );
		$this->_viewer->pageNav = Ext_Static_Common_Service::getPageNav ( $count, $page, ceil ( $count / $this->_perpage ), $this->buildLink ( 'manager', array () ) );
		$this->setMenuBar ( 'manager', 'manager' );
		$this->setTemplate ( 'manager' );
	}
	
	public function managermodify() {
		list ( $step, $id ) = S::gp ( array ('step', 'id' ) );
		$info = $this->getModelFactory ()->getAdminModel ()->getByUid ( $id );
		if (intval ( $step ) == 2) {
			list ( $callback, $username, $password, $password2 ) = S::gp ( array ('callback', 'username', 'password', 'password2' ) );
			if (! S::isArray ( $info ))
				$this->jsonOutput ( $callback, array (false, '帐号不存在' ) );
			list ( $username, $password, $password2 ) = array (trim ( $username ), trim ( $password ), trim ( $password2 ) );
			if (! $username)
				$this->jsonOutput ( $callback, array (false, '用户名不能为空' ) );
			if (($password != '' || $password2 != '') && ($password != $password2))
				$this->jsonOutput ( $callback, array (false, '两次密码输入不一致' ) );
			$userInfo = $this->getModelFactory ()->getAdminModel ()->getByUserName ( $username );
			if (S::isArray ( $userInfo ) && ($userInfo ['id'] != $this->_userInfo ['id']))
				$this->jsonOutput ( $callback, array (false, '用户名已被使用' ) );
			$fields = array ('username' => $username );
			($password && $password2) && $fields ['password'] = md5 ( $password2 );
			$result = $this->getModelFactory ()->getAdminModel ()->update ( $fields, $id );
			$this->jsonOutput ( $callback, $result ? array (true, '修改成功' ) : array (false, '操作失败，请重试' ) );
		}
		if (! S::isArray ( $info ))
			$this->getError ()->showError ( "帐号不存在!" );
		list ( $this->_viewer->id, $this->_viewer->info ) = array ($id, $info );
		$this->setMenuBar ( 'manager', 'managermodify' );
		$this->setTemplate ( 'manager.modify' );
	}
	
	public function manageradd() {
		list ( $step ) = S::gp ( array ('step' ) );
		if (intval ( $step ) == 2) {
			list ( $callback, $username, $password, $password2 ) = S::gp ( array ('callback', 'username', 'password', 'password2' ) );
			list ( $username, $password, $password2 ) = array (trim ( $username ), trim ( $password ), trim ( $password2 ) );
			if (! $username)
				$this->jsonOutput ( $callback, array (false, '用户名不能为空' ) );
			if ($password == '' || $password2 == '' || $password != $password2)
				$this->jsonOutput ( $callback, array (false, '密码不能为空，且两次密码输入必须一致' ) );
			$userInfo = $this->getModelFactory ()->getAdminModel ()->getByUserName ( $username );
			if (S::isArray ( $userInfo ))
				$this->jsonOutput ( $callback, array (false, '用户名已被使用' ) );
			$fields = array ('username' => $username, 'userpsw' => md5 ( $password2 ) );
			$result = $this->getModelFactory ()->getAdminModel ()->insertUser ( $fields );
			$this->jsonOutput ( $callback, $result ? array (true, '添加成功' ) : array (false, '操作失败，请重试' ) );
		}
		$this->setMenuBar ( 'manager', 'manageradd' );
		$this->setTemplate ( 'manager.modify' );
	}
	
	public function managerdelete() {
		list ( $step ) = S::gp ( array ('step' ) );
		if (intval ( $step ) == 2) {
			list ( $id, $callback ) = S::gp ( array ('aid', 'callback' ) );
			if ($id == '')
				$this->jsonOutput ( $callback, array (false, '请选择欲操作对象' ) );
			if ($id == $this->_userInfo ['id'])
				$this->jsonOutput ( $callback, array (false, '不能删除当前帐号' ) );
			$info = $this->getModelFactory ()->getAdminModel ()->getByUid ( $id );
			if (! S::isArray ( $info ))
				$this->jsonOutput ( $callback, array (false, '帐号不存在' ) );
			$result = $this->getModelFactory ()->getAdminModel ()->delete ( $id );
			$this->jsonOutput ( $callback, $result ? array (true, '删除成功' ) : array (false, '操作失败，请重试' ) );
		}
		$this->jsonOutput ( $callback, array (false, '异常操作' ) );
	}
	
	#===========================================================================
	/**
	 * 上传图片
	 * Enter description here ...
	 */
	public function upload() {
		list ( $sign, $type, $field ) = S::gp ( array ('sign', 'dir', 'field' ) );
		$this->_userInfo = $this->getAdminModel ()->isLogin ();
		if (! S::isArray ( $this->_userInfo )) {
			print_r ( json_encode ( array ('error' => 1, 'message' => '请先登录' ) ) );
			exit ();
		}
		if (! $sign) {
			print_r ( json_encode ( array ('error' => 1, 'message' => '参数错误' ) ) );
			exit ();
		}
		list ( $type, $field ) = array (($type ? $type : 'image'), $field ? $field : 'imgFile' );
		list ( $bool, $msg, $fileName ) = $this->getModelFactory ()->getCommonUploadModel ()->commonUpload ( $_FILES [$field], $sign, $type );
		$data = $bool ? array ('error' => 0, 'url' => $fileName ) : array ('error' => 1, 'message' => $msg );
		print_r ( json_encode ( $data ) );
		exit ();
	}
	
	private function setMenuBar($parent = '', $child = '', $tab = '') {
		$this->_viewer->menuparent = $parent ? $parent : '';
		$this->_viewer->menuname = isset ( $this->_menus [$parent] ) ? $this->_menus [$parent] : '';
		$this->_viewer->menuchild = $child ? $child : $this->getAction ();
		$this->_viewer->currenttab = $tab ? $tab : $this->getAction ();
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
	
	private function getAdminModel() {
		return $this->getModelFactory ()->getAdminModel ();
	}
	
	private function buildLink($action, $params = array()) {
		return $this->_baseurl . 'a=' . $action . '&' . http_build_query ( $params );
	}

}