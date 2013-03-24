<?php
! defined ( 'WEBNAME' ) && exit ( 'Forbidden' );
class AdminModel extends BaseModel {
	
	public $_tableName = 'admins';
	public $_primarykey = 'id';
	
	public function getByUid($uid) {
		$uid = intval ( $uid );
		if ($uid < 1)
			return false;
		return $this->_get ( $uid );
	}
	
	public function count() {
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), '', array (), array (PW_EXPR => array ('count(*) as c' ) ) );
		return $this->getField ( $this->query ( $sql ) );
	}
	
	public function getList($page, $perpage) {
		list ( $start, $perpage ) = Ext_Static_Common_Service::filterPage ( $page, $perpage );
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), '', array (), array (PW_LIMIT => array ($start, $perpage ), PW_ORDERBY => array ('created_time' => PW_DESC ) ) );
		return $this->fetchArray ( $this->query ( $sql ), $this->_primarykey );
	}
	
	public function getByUserName($username) {
		$username = ( string ) trim ( $username );
		if (empty ( $username ))
			return false;
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), "username=:username", array ($username ) );
		$result = $this->query ( $sql );
		return $this->getOne ( $result );
	}
	
	public function update($fields, $uid) {
		$uid = intval ( $uid );
		if ($uid < 1 && ! S::isArray ( $fields ))
			return false;
		return $this->_update ( $fields, ( array ) $uid );
	}
	
	public function delete($uid) {
		$uid = intval ( $uid );
		if ($uid < 1)
			return false;
		return $this->_delete ( array ($uid ) );
	}
	
	/**
	 * 插入数据
	 *
	 * @param array $params
	 * @return integer 返回用户id 
	 */
	public function insertUser($params) {
		if (! is_array ( $params ) || ! isset ( $params ['username'] ))
			return false;
		if ($this->getByUserName ( $params ['username'] ))
			return false;
		return $this->_add ( $params );
	}
	
	/**
	 * 对用户进行认证
	 *
	 * @param string $username
	 * @param string $password
	 * @return 登录成功返回用户信息，失败返回false
	 */
	public function check($username, $password) {
		$userInfo = $this->getByUserName ( $username );
		if (is_array ( $userInfo ) && count ( $userInfo ) > 0 && md5 ( $password ) === $userInfo ['userpsw']) {
			return $userInfo;
		} else {
			return false;
		}
	}
	
	/**
	 * 判断用户是否登录
	 *
	 * @return 已经登录返回用户信息，否则返回false
	 */
	public function isLogin() {
		list ( $dbsitehash, $dbCookiePre, $dbcookiename, $dbcookieDate ) = $this->getGeneralConfig ();
		$dbcookiename = $dbCookiePre . $dbcookiename;
		$auth = Ext_Static_Common_Service::GetCookie ( $dbcookiename );
		if (! $auth)
			return false;
		$auth = explode ( "\t", $auth );
		if (is_array ( $auth ) && count ( $auth ) == 3) {
			$timemap = time ();
			if ($timemap < $auth [0] + intval ( $dbcookieDate ) && md5 ( $auth [0] . $dbsitehash ) == $auth [1]) {
				$nameAndPwd = Ext_Static_Common_Service::StrCode ( $auth [2], 'DECODE' );
				$nameAndPwd = explode ( "\t", $nameAndPwd );
				if (count ( $nameAndPwd ) != 2)
					return false;
				return $this->check ( $nameAndPwd [0], $nameAndPwd [1] );
			}
		}
		return false;
	}
	
	/**
	 * 登录认证
	 *
	 * @param string $username
	 * @param string $password
	 * @return boolean
	 */
	public function login($username, $password) {
		if (! $username || ! $password)
			return false;
		$userInfo = $this->check ( $username, $password );
		if (! $userInfo)
			return false;
		list ( $dbsitehash, $dbCookiePre, $dbcookiename, $dbcookieDate ) = $this->getGeneralConfig ();
		$timemap = time ();
		$dbcookiename = $dbCookiePre . $dbcookiename;
		$cookieValue = $timemap . "\t" . md5 ( $timemap . $dbsitehash ) . "\t" . Ext_Static_Common_Service::StrCode ( $username . "\t" . $password );
		$result = Ext_Static_Common_Service::Cookie ( $dbcookiename, $cookieValue, $timemap + intval ( $dbcookieDate ) );
		if ($result) {
			$data = array ();
			$data ['last_ip'] = Ext_Static_Remote_Service::getRealIp ();
			$data ['last_date'] = $timemap;
			$data ['total_num'] = intval ( $userInfo ['total_num'] ) + 1;
			$this->update ( $data, $userInfo ['id'] );
		}
		return $result;
	}
	
	public function loginOut() {
		list ( $dbsitehash, $dbCookiePre, $dbcookiename, $dbcookieDate ) = $this->getGeneralConfig ();
		$timemap = time ();
		$dbcookiename = $dbCookiePre . $dbcookiename;
		return Ext_Static_Common_Service::Cookie ( $dbcookiename, '', 0 );
	}
	
	private function getGeneralConfig() {
		require_once ROOT . '/config/general.php';
		$config = General::getHashConfig ();
		return array ($config ['dbsitehash'], $config ['dbcookiepre'], $config ['dbcookiename'], $config ['dbcookiedate'] );
	}
}