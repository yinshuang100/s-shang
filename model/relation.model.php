<?php
! defined ( 'WEBNAME' ) && exit ( 'Forbidden' );
class RelationModel extends BaseModel {
	public $_tableName = 'users_shows_relation';
	public $_primarykey = 'id';
	
	public function addHold($uid, $showId) {
		list ( $uid, $showId ) = array (intval ( $uid ), intval ( $showId ) );
		if ($uid < 1 || $showId < 1)
			return false;
		$data = array ();
		$data ['uid'] = $uid;
		$data ['showid'] = $showId;
		$data ['type'] = SystemConst::ACTION_TYPE_HOLD;
		return $this->add ( $data );
	}
	
	public function addShare($uid, $showId) {
		list ( $uid, $showId ) = array (intval ( $uid ), intval ( $showId ) );
		if ($uid < 1 || $showId < 1)
			return false;
		$data = array ();
		$data ['uid'] = $uid;
		$data ['showid'] = $showId;
		$data ['type'] = SystemConst::ACTION_TYPE_SHARE;
		return $this->add ( $data );
	}
	
	public function addComment($uid, $showId, $cid) {
		list ( $uid, $showId, $cid ) = array (intval ( $uid ), intval ( $showId ), intval ( $cid ) );
		if ($uid < 1 || $showId < 1 || $cid < 1)
			return false;
		$data = array ();
		$data ['uid'] = $uid;
		$data ['showid'] = $showId;
		$data ['itemid'] = $cid;
		$data ['type'] = SystemConst::ACTION_TYPE_COMMENT;
		return $this->add ( $data );
	}
	
	public function deleteByShowId($showId) {
		$showId = intval ( $showId );
		if ($showId < 1)
			return false;
		$sql = $this->_queryBuilder->deleteClause ( $this->getTableName (), 'showid=:showid', array ($showId ) );
		$this->query ( $sql );
		return $this->getAffectRows;
	}
	
	public function deleteByCommentId($cid) {
		$cid = intval ( $cid );
		if ($cid < 1)
			return false;
		$sql = $this->_queryBuilder->deleteClause ( $this->getTableName (), 'type=:type AND itemid=:itemid', array (SystemConst::ACTION_TYPE_COMMENT, $cid ) );
		$this->query ( $sql );
		return $this->getAffectRows;
	}
	
	public function count($fields = array()) {
		list ( $condition, $params ) = $this->buildConditionSql ( $fields );
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), $condition, $params, array (PW_EXPR => array ('count(*) as c' ) ) );
		return $this->getField ( $this->query ( $sql ) );
	}
	
	public function getList($fields = array(), $page, $perpage) {
		list ( $start, $perpage ) = Ext_Static_Common_Service::filterPage ( $page, $perpage );
		list ( $condition, $params ) = $this->buildConditionSql ( $fields );
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), $condition, $params, array (PW_LIMIT => array ($start, $perpage ), PW_ORDERBY => array ('created_time' => PW_DESC ) ) );
		return $this->fetchArray ( $this->query ( $sql ), $this->_primarykey );
	}
	
	//*****************************************************
	

	public function add($fields) {
		$fields = $this->checkFields ( $fields );
		if (! S::isArray ( $fields ))
			return false;
		$time = time ();
		$fields ['created_time'] = $time;
		$fields ['day'] = date ( 'Y-m-d', $time );
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
		if (isset ( $fields ['showid'] )) {
			$condition [] = 'showid=:showid';
			$params [] = $fields ['showid'];
		}
		if (isset ( $fields ['itemid'] )) {
			$condition [] = 'itemid=:itemid';
			$params [] = $fields ['itemid'];
		}
		if (isset ( $fields ['type'] )) {
			$condition [] = 'type=:type';
			$params [] = $fields ['type'];
		}
		if (isset ( $fields ['day'] )) {
			$condition [] = 'day=:day';
			$params [] = $fields ['day'];
		}
		return array (implode ( ' AND ', $condition ), $params );
	}
	
	private function checkFields($fields) {
		if (! S::isArray ( $fields ))
			return false;
		$data = array ();
		isset ( $fields ['uid'] ) && $data ['uid'] = intval ( $fields ['uid'] );
		isset ( $fields ['showid'] ) && $data ['showid'] = intval ( $fields ['showid'] );
		isset ( $fields ['itemid'] ) && $data ['itemid'] = intval ( $fields ['itemid'] );
		isset ( $fields ['type'] ) && $data ['type'] = intval ( $fields ['type'] );
		isset ( $fields ['day'] ) && $data ['day'] = trim ( $fields ['day'] );
		isset ( $fields ['created_time'] ) && $data ['created_time'] = intval ( $fields ['created_time'] );
		isset ( $fields ['modified_time'] ) && $data ['modified_time'] = intval ( $fields ['modified_time'] );
		return $data;
	}
}
	