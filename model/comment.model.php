<?php
! defined ( 'WEBNAME' ) && exit ( 'Forbidden' );
class CommentModel extends BaseModel {
	public $_tableName = 'shows_comment';
	public $_primarykey = 'id';
	
	
	public function updateComment($id, $fields) {
		$result = $this->update ( $id, $fields );
		if ($result) {
			//@todo update show num
		}
		return $result;
	}
	
	public function deleteComment($id) {
		$result = $this->update ( $id );
		if ($result) {
			//@todo update show num | delete relation
		}
		return $result;
	}
	
	public function getComment($id) {
		$id = intval ( $id );
		if ($id < 1)
			return false;
		return $this->get ( $id );
	}
	
	public function getCommentByIds($ids) {
		$ids = ( array ) ($ids);
		if (! S::isArray ( $ids ))
			return false;
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), 'id in (:id)', array ($ids ), array (PW_ORDERBY => array ('created_time' => PW_DESC ) ) );
		return $this->fetchArray ( $this->query ( $sql ), $this->_primarykey );
	}
	
	public function countByShowId($showId) {
		$showId = intval ( $showId );
		if ($showId < 1)
			return 0;
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), 'showid=:showid', array ($showId ), array (PW_EXPR => array ('count(*) as c' ) ) );
		return $this->getField ( $this->query ( $sql ) );
	}
	
	public function getListByShowId($showId, $page, $perpage) {
		$showId = intval ( $showId );
		if ($showId < 1)
			return array ();
		list ( $start, $perpage ) = Ext_Static_Common_Service::filterPage ( $page, $perpage );
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), 'showid=:showid', array ($showId ), array (PW_LIMIT => array ($start, $perpage ), PW_ORDERBY => array ('created_time' => PW_DESC ) ) );
		return $this->fetchArray ( $this->query ( $sql ), $this->_primarykey );
	}
	
	public function countByUid($uid) {
		$uid = intval ( $uid );
		if ($uid < 1)
			return 0;
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), 'uid=:uid', array ($uid ), array (PW_EXPR => array ('count(*) as c' ) ) );
		return $this->getField ( $this->query ( $sql ) );
	}
	
	public function getListByUid($uid, $page, $perpage) {
		$uid = intval ( $uid );
		if ($uid < 1)
			return array ();
		list ( $start, $perpage ) = Ext_Static_Common_Service::filterPage ( $page, $perpage );
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), 'uid=:uid', array ($uid ), array (PW_LIMIT => array ($start, $perpage ), PW_ORDERBY => array ('created_time' => PW_DESC ) ) );
		return $this->fetchArray ( $this->query ( $sql ), $this->_primarykey );
	}
	
	public function count($fields = array()) {
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), '', array (), array (PW_EXPR => array ('count(*) as c' ) ) );
		return $this->getField ( $this->query ( $sql ) );
	}
	
	public function getList($page, $perpage) {
		list ( $start, $perpage ) = Ext_Static_Common_Service::filterPage ( $page, $perpage );
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), '', array (), array (PW_LIMIT => array ($start, $perpage ), PW_ORDERBY => array ('created_time' => PW_DESC ) ) );
		return $this->fetchArray ( $this->query ( $sql ), $this->_primarykey );
	}
	
	//*****************************************************
	

	public function add($fields) {
		$fields = $this->checkFields ( $fields );
		if (! S::isArray ( $fields ))
			return false;
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
	
	private function checkFields($fields) {
		if (! S::isArray ( $fields ))
			return false;
		$data = array ();
		isset ( $fields ['showid'] ) && $data ['showid'] = intval ( $fields ['showid'] );
		isset ( $fields ['uid'] ) && $data ['uid'] = intval ( $fields ['uid'] );
		isset ( $fields ['description'] ) && $data ['description'] = trim ( $fields ['description'] );
		isset ( $fields ['created_time'] ) && $data ['created_time'] = intval ( $fields ['created_time'] );
		isset ( $fields ['modified_time'] ) && $data ['modified_time'] = intval ( $fields ['modified_time'] );
		return $data;
	}
}