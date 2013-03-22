<?php
! defined ( 'WEBNAME' ) && exit ( 'Forbidden' );
class ConfigModel extends BaseModel {
	
	public $_tableName = 'web_config';
	public $_primarykey = 'id';
	
	public function getByKey($key) {
		$key = trim ( $key );
		if ($key == '')
			return false;
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), 'keyname=:keyname', array ($key ) );
		$result = $this->formatResult ( $rs = $this->fetchArray ( $this->query ( $sql ) ) );
		return ($result && isset ( $result [$key] )) ? $result [$key] : null;
	}
	
	public function getAllConfig($scope = '') {
		if ($scope == '') {
			$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), '', array () );
		} else {
			$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), 'scope=:scope', array (intval ( $scope ) ) );
		}
		return $this->formatResult ( $this->fetchArray ( $this->query ( $sql ) ) );
	}
	
	public function getGlobalConfig() {
		return $this->getAllConfig ( 0 );
	}
	
	public function replaceDefinedConfig($key, $value) {
		return $this->replaceConfig ( $key, $value, 1 );
	}
	
	public function replaceConfig($key, $value, $scope = '0') {
		$key = trim ( $key );
		if ($key == '')
			return false;
		$fields ['keyname'] = $key;
		$fields ['value'] = $value;
		$fields ['scope'] = intval ( $scope );
		$fields ['modified_time'] = time ();
		if (! S::isArray ( $fields = $this->checkField ( $fields ) )) {
			return false;
		}
		$sql = $this->_queryBuilder->replaceClause ( $this->getTableName (), $fields );
		return $this->query ( $sql );
	}
	
	public function replaceMulti($fields) {
		if (! S::isArray ( $fields ))
			return false;
		foreach ( $fields as $k => $v ) {
			$this->replaceConfig ( $k, $v );
		}
		return true;
	}
	
	private function checkField($fields) {
		if (! S::isArray ( $fields ))
			return false;
		if (! isset ( $fields ['value'] ))
			return false;
		$fields ['type'] = $this->getType ( 'string' );
		if (S::isArray ( $fields ['value'] )) {
			$fields ['value'] = serialize ( $fields ['value'] );
			$fields ['type'] = $this->getType ( 'array' );
		}
		return $fields;
	}
	
	private function formatResult($result) {
		if (! S::isArray ( $result ))
			return array ();
		$data = array ();
		foreach ( $result as $k => $v ) {
			$data [$v ['keyname']] = ($v ['type'] == $this->getType ( 'array' )) ? unserialize ( $v ['value'] ) : $v ['value'];
		}
		return $data;
	}
	
	private function getType($key) {
		$types = array ('string' => 1, 'array' => 2 );
		return isset ( $types [$key] ) ? $types [$key] : $types ['string'];
	}
}