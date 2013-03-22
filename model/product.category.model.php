<?php
class ProductCategoryModel extends BaseModel {
	
	public $_tableName = 'product_category';
	public $_primarykey = 'id';
	
	public function addCategory($fields) {
		if (! S::isArray ( $fields ))
			return false;
		return $this->_add ( $fields );
	}
	
	public function getAllCategory($pid = 0, $depth = 0, &$fields = array()) {
		$result = $this->getChildren ( $pid );
		if (S::isArray ( $result )) {
			foreach ( $result as $v ) {
				$v ['depth'] = $depth;
				$fields [$v ['id']] = $v;
				$this->getAllCategory ( $v ['id'], $depth + 1, $fields );
			}
		}
		return $fields;
	}
	
	public function getChildren($pid) {
		$sql = $this->_queryBuilder->selectClause ( $this->getTableName (), 'pid=:pid', array (intval ( $pid ) ), array (PW_ORDERBY => array ('seq' => PW_ASC ) ) );
		$result = $this->query ( $sql );
		return $this->fetchArray ( $result, $this->_primarykey );
	}
	
	public function getChildrenIds($pid) {
		$pid = intval ( $pid );
		$children = $this->getChildren ( $pid );
		return S::isArray ( $children ) ? array_keys ( $children ) : array ();
	}
	
	public function get($id) {
		$id = intval ( $id );
		if ($id < 1)
			return false;
		return $this->_get ( $id );
	}
	
	public function updateCategory($id, $fields) {
		$id = intval ( $id );
		if ($id < 1 || ! S::isArray ( $fields ))
			return false;
		return $this->_update ( $fields, array ($id ) );
	}
	
	public function deleteCategory($id) {
		$id = intval ( $id );
		if ($id < 1)
			return false;
		$ids = $this->getChildrenIds ( $id );
		$ids [] = $id;
		return $this->_delete ( $ids );
	}

}