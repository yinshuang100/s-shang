<?php
class ProductHelper extends BaseHelper {

	public function addProduct($fields, $smallpicFlag = false, $smallpic_seq = array(), $smallpic = array()) {
		$pid = $this->getModelFactory ()->getProductModel ()->add ($fields);
		if (! $pid)
			return array (false,'添加失败' );
		($smallpicFlag) && $this->replaceProductImgs ($pid, $smallpic_seq, $smallpic);
		return $pid;
	}

	public function updateProduct($pid, $fields, $smallpicFlag = false, $smallpic_seq = array(), $smallpic = array()) {
		$pid = intval ($pid);
		if ($pid < 1)
			return array (false,'修改失败' );
		$result = $this->getModelFactory ()->getProductModel ()->update ($pid, $fields);
		if (! $result)
			return array (false,'修改失败' );
		($smallpicFlag) && $this->replaceProductImgs ($pid, $smallpic_seq, $smallpic);
		return $pid;
	}

	public function deleteProduct($pid) {
		$pid = intval ($pid);
		if ($pid < 1)
			return array (false,'删除失败' );
		$imgs = $this->getImgsByPid ($pid);
		$bool = $this->getModelFactory ()->getProductImgModel ()->deleteByPid ($pid);
		($bool && S::isArray ($imgs)) && $this->deleteImgFiles ($imgs, 'product');
		$bool = $this->getModelFactory ()->getProductModel ()->delete ($pid);
		return array ($bool,'' );
	}

	public function deleteProducts($pids) {
		$pids = ( array ) $pids;
		if (! S::isArray ($pids))
			return array (false,'删除失败' );
		foreach ( $pids as $pid ) {
			$this->deleteProduct ($pid);
		}
		return array (true,'' );
	}

	public function deleteImgFiles($fields, $sign) {
		if (! S::isArray ($fields))
			return false;
		foreach ( $fields as $v ) {
			$this->getModelFactory ()->getCommonUploadModel ()->deleteFile ($v, $sign);
		}
		return true;
	}

	private function getImgsByPid($pid) {
		$product = $this->getModelFactory ()->getProductModel ()->get ($pid);
		$data = array ();
		if (S::isArray ($product)) {
			(isset ($product ['cover']) && $product ['cover']) && $data [] = $product ['cover'];
			(isset ($product ['coverhover']) && $product ['coverhover']) && $data [] = $product ['coverhover'];
		}
		$smallImgs = $this->getModelFactory ()->getProductImgModel ()->getByPid ($pid);
		if (S::isArray ($smallImgs)) {
			foreach ( $smallImgs as $v ) {
				(isset ($v ['pic']) && $v ['pic']) && $data [] = $v ['pic'];
				(isset ($v ['thumbpic']) && $v ['thumbpic']) && $data [] = $v ['thumbpic'];
			}
		}
		return $data;
	}

	private function replaceProductImgs($pid, $smallpic_seq = array(), $smallpic = array()) {
		$pid = intval ($pid);
		if ($pid < 1 || ! $this->getModelFactory ()->getProductImgModel ()->deleteByPid ($pid))
			return false;
		$fields = $this->buildSmallPicFields ($pid, $smallpic_seq, $smallpic);
		if (! S::isArray ($fields))
			return false;
		foreach ( $fields as $v ) {
			$this->getModelFactory ()->getProductImgModel ()->add ($v);
		}
		return true;
	}

	private function buildSmallPicFields($pid, $smallpic_seq, $smallpic) {
		$pid = intval ($pid);
		$fields = $this->buildSmallPicImgs ($smallpic_seq, $smallpic);
		if ($pid < 1 || ! S::isArray ($fields))
			return false;
		$data = array ();
		foreach ( $fields as $v ) {
			if (count ($v) < 2 || ! trim ($v [1]))
				continue;
			$tmp = array ();
			$tmp ['pid'] = $pid;
			$tmp ['seq'] = intval ($v [0]);
			$tmp ['pic'] = $tmp ['thumbpic'] = trim ($v [1]);
			$data [] = $tmp;
		}
		return $data;
	}

	private function buildSmallPicImgs($smallpic_seq, $smallpic) {
		if (! S::isArray ($smallpic_seq) || ! S::isArray ($smallpic))
			return false;
		$data = array ();
		foreach ( $smallpic_seq as $k => $seq ) {
			(isset ($smallpic [$k]) && $smallpic [$k]) && $data [] = array ($seq,$smallpic [$k] );
		}
		return $data;
	}
}