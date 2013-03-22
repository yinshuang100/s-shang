<?php
! defined ( 'WEBNAME' ) && exit ( 'Forbidden' );
class UserModel extends BaseModel {
	public $_tableName = 'users';
	public $_primarykey = 'id';
	
	/**
	 * 插入数据
	 *
	 * @param array $params
	 * @return integer 返回用户id 
	 */
	public function insertUser($data) {
		$data = $this->checkFields ( $data );
		if (! is_array ( $data ) || ! isset ( $data ['username'] ))
			return false;
		$data ['reg_date'] = $data ['created_time'] = time ();
		return $this->_add ( $data );
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
		if (is_array ( $userInfo ) && count ( $userInfo ) > 0 && $this->getPassword ( $password ) === $userInfo ['userpsw']) {
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
		$dbcookiename = $this->getCookieName ( $dbCookiePre, $dbcookiename );
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
		list ( $username, $password ) = array (trim ( $username ), trim ( $password ) );
		if (! $username || ! $password)
			return array (false, '昵称|密码填写错误' );
		$userInfo = $this->check ( $username, $password );
		if (! S::isArray ( $userInfo ))
			return array (false, '登录名或密码错误，请确认后重新输入。' );
		list ( $dbsitehash, $dbCookiePre, $dbcookiename, $dbcookieDate ) = $this->getGeneralConfig ();
		list ( $timemap, $dbcookiename ) = array (time (), $this->getCookieName ( $dbCookiePre, $dbcookiename ) );
		$cookieValue = $timemap . "\t" . md5 ( $timemap . $dbsitehash ) . "\t" . Ext_Static_Common_Service::StrCode ( $username . "\t" . $password );
		$result = Ext_Static_Common_Service::Cookie ( $dbcookiename, $cookieValue, $timemap + intval ( $dbcookieDate ) );
		if ($result) {
			$data = array ();
			$data ['last_login_ip'] = Ext_Static_Remote_Service::getRealIp ();
			$data ['last_login_date'] = $timemap;
			$data ['login_total_num'] = intval ( $userInfo ['login_total_num'] ) + 1;
			$this->update ( $data, $userInfo ['id'] );
		}
		return $result ? array (true, $userInfo ) : array (false, '登录失败' );
	}
	
	public function loginOut() {
		list ( $dbsitehash, $dbCookiePre, $dbcookiename, $dbcookieDate ) = $this->getGeneralConfig ();
		$timemap = time ();
		$dbcookiename = $this->getCookieName ( $dbCookiePre, $dbcookiename );
		return Ext_Static_Common_Service::Cookie ( $dbcookiename, '', 0 );
	}
	
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
		return $this->getOne ( $this->query ( $sql ) );
	}
	
	public function getByUids($uids) {
		if (! S::isArray ( $uids )) {
			return array ();
		}
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), 'id in (:id)', array ($uids ) );
		return $this->fetchArray ( $this->query ( $sql ), $this->_primarykey );
	}
	
	public function update($fields, $uid) {
		$uid = intval ( $uid );
		$data = $this->checkFields ( $fields );
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
	
	private function getCookieName($dbCookiePre, $dbcookiename) {
		return sprintf ( '%s%s%s', $dbCookiePre, 'index', $dbcookiename );
	}
	
	public function getPassword($str) {
		return md5 ( $str );
	}
	
	private function getGeneralConfig() {
		require_once ROOT . '/config/general.php';
		$config = General::getHashConfig ();
		return array ($config ['dbsitehash'], $config ['dbcookiepre'], $config ['dbcookiename'], $config ['dbcookiedate'] );
	}
	
	private function checkFields($fields) {
		if (! S::isArray ( $fields ))
			return false;
		$data = array ();
		isset ( $fields ['username'] ) && $data ['username'] = trim ( $fields ['username'] );
		isset ( $fields ['userpsw'] ) && $data ['userpsw'] = trim ( $fields ['userpsw'] );
		isset ( $fields ['email'] ) && $data ['email'] = trim ( $fields ['email'] );
		isset ( $fields ['mobile'] ) && $data ['mobile'] = trim ( $fields ['mobile'] );
		isset ( $fields ['head'] ) && $data ['head'] = trim ( $fields ['head'] );
		isset ( $fields ['head_thumb'] ) && $data ['head'] = trim ( $fields ['head_thumb'] );
		isset ( $fields ['shownum'] ) && $data ['shownum'] = intval ( $fields ['shownum'] );
		isset ( $fields ['allowpost'] ) && $data ['allowpost'] = intval ( $fields ['allowpost'] );
		isset ( $fields ['reg_date'] ) && $data ['reg_date'] = intval ( $fields ['reg_date'] );
		isset ( $fields ['last_login_ip'] ) && $data ['last_login_ip'] = trim ( $fields ['last_login_ip'] );
		isset ( $fields ['last_login_date'] ) && $data ['last_login_date'] = intval ( $fields ['last_login_date'] );
		isset ( $fields ['created_time'] ) && $data ['created_time'] = intval ( $fields ['created_time'] );
		isset ( $fields ['modified_time'] ) && $data ['modified_time'] = intval ( $fields ['modified_time'] );
		return $data;
	}
}