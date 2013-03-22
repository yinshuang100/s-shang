<?php
! defined ( 'WEBNAME' ) && exit ( 'Forbidden' );
class ShowsModel extends BaseModel {
	public $_tableName = 'shows';
	public $_primarykey = 'id';
	
	public function count($fields = array()) {
		list ( $condition, $params ) = $this->buildConditionSql ( $fields );
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), $condition, $params, array (PW_EXPR => array ('count(*) as c' ) ) );
		return $this->getField ( $this->query ( $sql ) );
	}
	
	public function getList($fields = array(), $page, $perpage, $order = '') {
		list ( $start, $perpage ) = Ext_Static_Common_Service::filterPage ( $page, $perpage );
		$order = S::isArray ( $order ) ? $order : array ('created_time' => PW_DESC );
		list ( $condition, $params ) = $this->buildConditionSql ( $fields );
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), $condition, $params, array (PW_LIMIT => array ($start, $perpage ), PW_ORDERBY => $order ) );
		return $this->fetchArray ( $this->query ( $sql ), $this->_primarykey );
	}
	
	/**
	 * 所有未删除的数量
	 * Enter description here ...
	 * @param unknown_type $fields
	 */
	public function unDeletedCount($fields = array()) {
		list ( $condition, $params ) = $this->buildConditionSql ( $fields );
		$condition .= ($condition ? ' AND ' : '') . 'status in (:status)';
		$params [] = array (0, 1 );
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), $condition, $params, array (PW_EXPR => array ('count(*) as c' ) ) );
		return $this->getField ( $this->query ( $sql ) );
	}
	
	/**
	 * 所有未删除的列表
	 * Enter description here ...
	 * @param unknown_type $fields
	 * @param unknown_type $page
	 * @param unknown_type $perpage
	 * @param unknown_type $order
	 */
	public function getUnDeletedList($fields = array(), $page, $perpage, $order = '') {
		list ( $start, $perpage ) = Ext_Static_Common_Service::filterPage ( $page, $perpage );
		$order = S::isArray ( $order ) ? $order : array ('created_time' => PW_DESC );
		list ( $condition, $params ) = $this->buildConditionSql ( $fields );
		$condition .= ($condition ? ' AND ' : '') . 'status in (:status)';
		$params [] = array (0, 1 );
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), $condition, $params, array (PW_LIMIT => array ($start, $perpage ), PW_ORDERBY => $order ) );
		return $this->fetchArray ( $this->query ( $sql ), $this->_primarykey );
	}
	
	public function deleteByUid($uid) {
		$uid = intval ( $uid );
		if ($uid < 1)
			return false;
		$sql = $this->_queryBuilder->deleteClause ( $this->getTableName (), 'uid=:uid', array ($uid ) );
		$this->query ( $sql );
		return $this->getAffectRows;
	}
	
	//*****************************************************
	

	public function add($fields) {
		$fields = $this->checkFields ( $fields );
		if (! S::isArray ( $fields ))
			return false;
		$fields ['status'] = 1;
		$fields ['created_time'] = time ();
		return $this->_add ( $fields );
	}
	
	public function update($id, $fields) {
		$id = ( array ) $id;
		$fields = $this->checkFields ( $fields );
		if (! S::isArray ( $id ) || ! S::isArray ( $fields ))
			return false;
		$fields ['modified_time'] = time ();
		return $this->_update ( $fields, $id );
	}
	
	public function delete($id) {
		$id = ( array ) $id;
		if (! S::isArray ( $id ))
			return false;
		return $this->_delete ( $id );
	}
	
	public function get($id) {
		$id = intval ( $id );
		if ($id < 1)
			return false;
		return $this->_get ( $id );
	}
	
	public function buildConditionSql($fields) {
		if (! S::isArray ( $fields ))
			return array ('', array () );
		$params = $condition = array ();
		if (isset ( $fields ['uid'] )) {
			$condition [] = 'uid=:uid';
			$params [] = intval ( $fields ['uid'] );
		}
		if (isset ( $fields ['type'] )) {
			$condition [] = 'type=:type';
			$params [] = intval ( $fields ['type'] );
		}
		if (isset ( $fields ['status'] )) {
			$condition [] = 'status=:status';
			$params [] = intval ( $fields ['status'] );
		}
		return array (implode ( ' AND ', $condition ), $params );
	}
	
	public function checkFields($fields) {
		if (! S::isArray ( $fields ))
			return false;
		$data = array ();
		isset ( $fields ['uid'] ) && $data ['uid'] = intval ( $fields ['uid'] );
		isset ( $fields ['type'] ) && $data ['type'] = intval ( $fields ['type'] );
		isset ( $fields ['pic'] ) && $data ['pic'] = trim ( $fields ['pic'] );
		isset ( $fields ['pic_thumb'] ) && $data ['pic_thumb'] = trim ( $fields ['pic_thumb'] );
		isset ( $fields ['url'] ) && $data ['url'] = trim ( $fields ['url'] );
		isset ( $fields ['hold'] ) && $data ['hold'] = intval ( $fields ['hold'] );
		isset ( $fields ['share'] ) && $data ['share'] = intval ( $fields ['share'] );
		isset ( $fields ['comment'] ) && $data ['comment'] = intval ( $fields ['comment'] );
		isset ( $fields ['ucomment'] ) && $data ['ucomment'] = intval ( $fields ['ucomment'] );
		isset ( $fields ['hit'] ) && $data ['hit'] = intval ( $fields ['hit'] );
		isset ( $fields ['title'] ) && $data ['title'] = trim ( $fields ['title'] );
		isset ( $fields ['description'] ) && $data ['description'] = trim ( $fields ['description'] );
		isset ( $fields ['status'] ) && $data ['status'] = intval ( $fields ['status'] );
		isset ( $fields ['created_time'] ) && $data ['created_time'] = intval ( $fields ['created_time'] );
		isset ( $fields ['modified_time'] ) && $data ['modified_time'] = intval ( $fields ['modified_time'] );
		return $data;
	}
}