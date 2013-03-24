<?php
! defined ( 'WEBNAME' ) && exit ( 'Forbidden' );
class Model {
	
	private static $connectionFactory = array ();
	private $_memcache = null;
	public $_tableName = null;
	public $_primarykey = '';
	public $_queryBuilder = null;
	protected $_application = 'main';
	
	public function __construct() {
		$this->getQueryService ();
	}
	
	public function getQueryService() {
		if (! $this->_queryBuilder) {
			require_once EXT_PATH . '/ext.querybuilder.service.php';
			$this->_queryBuilder = new Ext_QueryBuilder_Service ();
		}
	}
	
	public function _add($fields) {
		if (! $this->check ())
			return false;
		$sql = $this->_queryBuilder->insertClause ( $this->getTableName (), $fields );
		$this->query ( $sql );
		return $this->insertId ();
	}
	
	public function _get($id, $resultIndexKey = null) {
		if (! $this->check ())
			return false;
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), $this->_primarykey . ' =:' . $this->_primarykey, array ($id ) );
		return $this->getOne ( $this->query ( $sql ), $resultIndexKey );
	}
	
	public function _gets($ids, $resultIndexKey = null) {
		if (! $this->check ())
			return false;
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), $this->_primarykey . ' in(:' . $this->_primarykey . ')', array ($ids ) );
		return $this->fetchArray ( $this->query ( $sql ), $resultIndexKey );
	}
	
	public function _getlist($offset = 0, $limit = 20) {
		if (! $this->check ())
			return false;
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), null, null, array (PW_LIMIT => array ($offset, $limit ) ) );
		$result = $this->query ( $sql );
		return $this->fetchArray ( $result );
	}
	
	public function _getAll() {
		if (! $this->check ())
			return false;
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), null, null, array () );
		$result = $this->query ( $sql );
		return $this->fetchArray ( $result );
	}
	
	public function _update($fields, $ids) {
		if (! $this->check ())
			return false;
		$sql = $this->_queryBuilder->updateClause ( $this->getTableName (), $this->_primarykey . ' in(:' . $this->_primarykey . ')', array ($ids ), $fields );
		return $this->query ( $sql );
	}
	
	public function _delete($ids) {
		if (! $this->check ())
			return false;
		$sql = $this->_queryBuilder->deleteClause ( $this->getTableName (), $this->_primarykey . ' in(:' . $this->_primarykey . ')', array ($ids ) );
		return $this->query ( $sql );
	}
	
	public function _count() {
		if (! $this->check ())
			return false;
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), null, null, array (PW_EXPR => array ('COUNT(*)' ) ) );
		return $this->getField ( $this->query ( $sql ) );
	}
	
	public function getTableName() {
		list ( , , , , , $tablepre ) = $this->_loadConfig ( $this->_application );
		return sprintf ( '%s%s', $tablepre, $this->_tableName );
	}
	
	public function query($sql) {
		return @mysql_query ( $sql, $this->getConnection () );
	}
	
	public function fetchArray($result, $resultIndexKey = null, $type = MYSQL_ASSOC) {
		$rt = array ();
		if ($resultIndexKey) {
			while ( $row = mysql_fetch_array ( $result, $type ) ) {
				$rt [$row [$resultIndexKey]] = $row;
			}
		} else {
			while ( $row = mysql_fetch_array ( $result, $type ) ) {
				$rt [] = $row;
			}
		}
		return $rt;
	}
	
	public function getOne($result, $type = MYSQL_ASSOC) {
		return @mysql_fetch_array ( $result, MYSQL_ASSOC );
	}
	
	public function getField($result, $field = 0, $type = MYSQL_NUM) {
		$rt = mysql_fetch_array ( $result, $type );
		return (isset ( $rt [$field] )) ? $rt [$field] : false;
	}
	
	public function insertId() {
		return mysql_insert_id ( $this->getConnection () );
	}
	
	public function getAffectRows(){
		return mysql_affected_rows($this->getConnection());
	}
	
	private function check() {
		return (! $this->_tableName || ! $this->_primarykey) ? false : true;
	}
	
	public function getConnection() {
		if (! isset ( self::$connectionFactory [$this->_application] )) {
			self::$connectionFactory [$this->_application] = $this->setConnection ( $this->_application );
		}
		return self::$connectionFactory [$this->_application];
	}
	
	protected function setConnection($app) {
		list ( $dbhost, $dbname, $dbuser, $dbpass, $dbcharset ) = $this->_loadConfig ( $app );
		return $this->_setConnection ( $dbhost, $dbname, $dbuser, $dbpass, $dbcharset );
	}
	
	private function _loadConfig($app) {
		require CONFIG_PATH . "/database.php";
		$db = isset ( $database [$app] ) ? $database [$app] : $database [$this->_application];
		return array ($db ['dbhost'], $db ['dbname'], $db ['dbuser'], $db ['dbpass'], $db ['dbcharset'], $db ['tablepre'] );
	}
	
	private function _setConnection($dbhost, $dbname, $dbuser, $dbpass, $dbcharset) {
		$conn = @mysql_connect ( $dbhost, $dbuser, $dbpass );
		(! $conn) ? print_r ( mysql_error () ) : mysql_select_db ( $dbname );
		@mysql_query ( "SET NAMES '" . $dbcharset . "'", $conn );
		return $conn;
	}

}