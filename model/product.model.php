<?php
require_once EXT_PATH . '/ext.static.common.service.php';

class ProductModel extends BaseModel {
	
	public $_tableName = 'product';
	public $_primarykey = 'id';
	
	public function add($fields) {
		if (! S::isArray ( $fields ))
			return false;
		$fields ['created_time'] = time ();
		return $this->_add ( $fields );
	}
	
	public function countByCategoryIds($categoryIds) {
		if (! S::isArray ( $categoryIds ))
			return false;
		$data = array ();
		foreach ( $categoryIds as $v ) {
			$data [$v] = $this->count ( array ('category' => intval ( $v ) ) );
		}
		return $data;
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
		$result = $this->fetchArray ( $this->query ( $sql ), $this->_primarykey );
		return $this->formatListResult ( $result );
	}
	
	public function get($id) {
		$id = intval ( $id );
		if ($id < 1)
			return false;
		return $this->formatResult ( $this->_get ( $id ) );
	}
	
	public function formatListResult($result) {
		if (! S::isArray ( $result ))
			return array ();
		foreach ( $result as $k => $v ) {
			$result [$k] = $this->formatResult ( $v );
		}
		return $result;
	}
	
	public function formatResult($result) {
		if (! S::isArray ( $result ))
			return array ();
		return $result;
	}
	
	public function getByCategory($categoryId, $page, $perpage) {
		$categoryId = intval ( $categoryId );
		if ($categoryId < 1)
			return false;
		list ( $start, $perpage ) = Ext_Static_Common_Service::filterPage ( $page, $perpage );
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), 'category=:category', array ($categoryId ), array (PW_LIMIT => array ($start, $perpage ), PW_ORDERBY => array ('created_time' => PW_DESC ) ) );
		$result = $this->fetchArray ( $this->query ( $sql ) );
		return $this->formatListResult ( $result );
	}
	
	public function update($id, $fields) {
		$id = ( array ) $id;
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
	
	public function deleteByCategory($categoryId) {
		$categoryId = intval ( $categoryId );
		if ($categoryId < 1)
			return false;
		$childrenIds = $this->getCategoryModel ()->getChildrenIds ( $categoryId );
		$childrenIds [] = $categoryId;
		$sql = $this->_queryBuilder->deleteClause ( $this->getTableName (), 'category in (:category)', array ($childrenIds ) );
		return $this->query ( $sql );
	}
	
	public function buildConditionSql($fields) {
		if (! S::isArray ( $fields ))
			return array ('', array () );
		$params = $condition = array ();
		if (isset ( $fields ['title'] )) {
			$condition [] = 'title like :title';
			$params [] = "%" . $fields ['title'] . "%";
		}
		if (isset ( $fields ['category'] )) {
			$childrenIds = $this->getCategoryModel ()->getChildrenIds ( $fields ['category'] );
			$childrenIds [] = $fields ['category'];
			$condition [] = 'category in (:category)';
			$params [] = $childrenIds;
		}
		if (isset ( $fields ['status'] )) {
			$condition [] = 'status=:status';
			$params [] = $fields ['status'];
		}
		return array (implode ( ' AND ', $condition ), $params );
	}
	
	public function getCategoryModel() {
		require_once MODEL_PATH . '/model.factory.php';
		return ModelFactory::getInstance ()->getProductCategoryModel ();
	}
}
	