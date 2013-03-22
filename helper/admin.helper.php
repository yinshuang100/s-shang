<?php
class AdminHelper extends BaseHelper {

	public function getShowListWithUserInfo($fields, $page, $perpage, $order){
		list($page, $perpage, $order) = array(intval($page), intval($perpage), trim($order));
		$order && $order = array($order => 'DESC');
		$list = $this->getModelFactory ()->getShowsModel ()->getList ( $fields, $page, $perpage, $order );
		if(!S::isArray($list)) return array();
		$uids = $this->getUidsByShowsList($list);
		$userInfo = $this->getModelFactory()->getUserModel()->getByUids($uids);
		return array($list, $userInfo);
	}

	private function getUidsByShowsList($fields){
		if (!S::isArray($fields)){
			return array();
		}
		$data =array();
		foreach($fields as $v){
			(isset($v['uid']) && $v['uid'] > 0) && $data[] = $v['uid'];
		}
		return $data;
	}

}