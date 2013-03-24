<?php
require_once EXT_PATH . '/ext.static.common.service.php';
class ProductImgModel extends BaseModel {
	public $_tableName = 'product_imgs';
	public $_primarykey = 'id';

	public function add($fields) {
		if (! S::isArray ($fields))
			return false;
		$fields ['created_time'] = time ();
		return $this->_add ($fields);
	}

	public function count($fields = array()) {
		list ( $condition, $params ) = $this->buildConditionSql ($fields);
		$sql = $this->_queryBuilder->selectClause ($this->getTableName (), $condition, $params, array (PW_EXPR => array ('count(*) as c' ) ));
		return $this->getField ($this->query ($sql));
	}

	public function getList($fields = array(), $page, $perpage) {
		list ( $start, $perpage ) = Ext_Static_Common_Service::filterPage ($page, $perpage);
		list ( $condition, $params ) = $this->buildConditionSql ($fields);
		$sql = $this->_queryBuilder->selectClause ($this->getTableName (), $condition, $params, array (PW_LIMIT => array ($start,$perpage ),PW_ORDERBY => array ('seq' => PW_ASC ) ));
		return $this->fetchArray ($this->query ($sql), $this->_primarykey);
	}

	public function getByPid($pid) {
		$pid = intval ($pid);
		if ($pid < 1)
			return array ();
		$sql = $this->_queryBuilder->selectClause ($this->getTableName (), 'pid=:pid', array ($pid ), array (PW_ORDERBY => array ('seq' => PW_ASC ) ));
		return $this->fetchArray ($this->query ($sql), $this->_primarykey);
	}

	public function get($id) {
		$id = intval ($id);
		if ($id < 1)
			return false;
		return $this->_get ($id);
	}

	public function update($id, $fields) {
		$id = ( array ) $id;
		if (! S::isArray ($id) || ! S::isArray ($fields))
			return false;
		$fields ['modified_time'] = time ();
		return $this->_update ($fields, $id);
	}

	public function delete($id) {
		$id = ( array ) $id;
		if (! S::isArray ($id))
			return false;
		return $this->_delete ($id);
	}

	public function deleteByPid($pid) {
		$pid = intval ($pid);
		if ($pid < 1)
			return false;
		$sql = $this->_queryBuilder->deleteClause ($this->getTableName (), 'pid=:pid', array ($pid ));
		return $this->query ($sql);
	}

	public function deleteByPids($pids) {
		if (! S::isArray ($pids))
			return false;
		$sql = $this->_queryBuilder->deleteClause ($this->getTableName (), 'pid in (:pid)', array ($pids ));
		return $this->query ($sql);
	}

	public function buildConditionSql($fields) {
		if (! S::isArray ($fields))
			return array ('',array () );
		$params = $condition = array ();
		if (isset ($fields ['pid'])) {
			$condition [] = 'pid=:pid';
			$params [] = $fields ['pid'];
		}
		if (isset ($fields ['status'])) {
			$condition [] = 'status=:status';
			$params [] = $fields ['status'];
		}
		return array (implode (' AND ', $condition),$params );
	}
}
	