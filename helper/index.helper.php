<?php
class IndexHelper extends BaseHelper {
	/**
	 * 添加文字情书
	 * Enter description here ...
	 */
	public function insertTextShows($fields) {
		if (! S::isArray ( $fields ) || ! isset ( $fields ['uid'] ) || ! isset ( $fields ['title'] ) || ! isset ( $fields ['content'] ) || ! isset ( $fields ['pic'] ))
			return array (false, '信息填写不完整' );
		$fields = $this->getModelFactory ()->getShowsModel ()->checkFields ( $fields );
		if ($fields ['uid'] < 1 || $fields ['title'] == '' || $fields ['content'] == '' || $fields ['pic'] == '')
			return array (false, '信息填写不完整' );
		$fields ['type'] = SystemConst::SHOW_TYPE_TEXT;
		$result = $this->getModelFactory ()->getShowsModel ()->add ( $fields );
		if ($result){
			$this->updateUserShowNum($uid);
		}
		return $result ? array (true, $result ) : array (false, '' );
	}
	
	/**
	 * 添加音频情书
	 * Enter description here ...
	 */
	public function insertSoundShows($fields) {
		if (! S::isArray ( $fields ) || ! isset ( $fields ['uid'] ) || ! isset ( $fields ['title'] ) || ! isset ( $fields ['pic'] ) || ! isset ( $fields ['url'] ))
			return array (false, '信息填写不完整' );
		$fields = $this->getModelFactory ()->getShowsModel ()->checkFields ( $fields );
		if ($fields ['uid'] < 1 || $fields ['title'] == '' || $fields ['content'] == '' || $fields ['url'] == '')
			return array (false, '信息填写不完整' );
		$fields ['type'] = SystemConst::SHOW_TYPE_SOUND;
		$result = $this->getModelFactory ()->getShowsModel ()->add ( $fields );
		if ($result){
			$this->updateUserShowNum($uid);
		}
		return $result ? array (true, $result ) : array (false, '' );
	}
	
	/**
	 * 添加视频情书
	 * Enter description here ...
	 */
	public function insertVideoShows($fields) {
		if (! S::isArray ( $fields ) || ! isset ( $fields ['uid'] ) || ! isset ( $fields ['title'] ) || ! isset ( $fields ['pic'] ) || ! isset ( $fields ['url'] ))
			return array (false, '信息填写不完整' );
		$fields = $this->getModelFactory ()->getShowsModel ()->checkFields ( $fields );
		if ($fields ['uid'] < 1 || $fields ['title'] == '' || $fields ['content'] == '' || $fields ['url'] == '')
			return array (false, '信息填写不完整' );
		$fields ['type'] = SystemConst::SHOW_TYPE_VIDEO;
		$result = $this->getModelFactory ()->getShowsModel ()->add ( $fields );
		if ($result){
			$this->updateUserShowNum($uid);
		}
		return $result ? array (true, $result ) : array (false, '' );
	}
	
	/**
	 * 支持情书
	 * Enter description here ...
	 * @param unknown_type $uid
	 * @param unknown_type $showId
	 */
	public function addHold($uid, $showId) {
		list ( $uid, $showId ) = array (intval ( $uid ), intval ( $showId ) );
		if ($uid < 1 || $showId < 1)
			return array (false, '参数错误' );
		$showInfo = $this->getModelFactory ()->getShowsModel ()->get ( $showId );
		if (! S::isArray ( $showInfo ))
			return array (false, '您支持的情书不存在或已被删除' );
		
		// verify uid has hold
		$data = array ();
		$data ['uid'] = $uid;
		$data ['showid'] = $showId;
		$data ['type'] = SystemConst::ACTION_TYPE_HOLD;
		$count = $this->getModelFactory ()->getRelationModel ()->count ( $data );
		if ($count > 0)
			return array (false, '您已经支持过了哦' );
		$result = $this->getModelFactory ()->getRelationModel ()->add ( $data );
		if ($result) { //update show hold num
			$this->getModelFactory ()->getShowsModel ()->update ( $showId, array ('hold' => intval ( $showInfo ['hold'] ) + 1 ) );
		}
		return $result ? array (true, '支持成功' ) : array (false, '支持失败，请重试' );
	}
	
	/**
	 * 分享情书
	 * Enter description here ...
	 * @param unknown_type $uid
	 * @param unknown_type $showId
	 */
	public function addShare($uid, $showId) {
		list ( $uid, $showId ) = array (intval ( $uid ), intval ( $showId ) );
		if ($uid < 1 || $showId < 1)
			return array (false, '参数错误' );
		$showInfo = $this->getModelFactory ()->getShowsModel ()->get ( $showId );
		if (! S::isArray ( $showInfo ))
			return array (false, '您分享的情书不存在或已被删除' );
		
		// verify uid has hold
		$data = array ();
		$data ['uid'] = $uid;
		$data ['showid'] = $showId;
		$data ['type'] = SystemConst::ACTION_TYPE_SHARE;
		$result = $this->getModelFactory ()->getRelationModel ()->add ( $data );
		if ($result) { //update show hold num
			$this->getModelFactory ()->getShowsModel ()->update ( $showId, array ('share' => intval ( $showInfo ['share'] ) + 1 ) );
		}
		return $result ? array (true, '分享成功' ) : array (false, '分享失败，请重试' );
	}
	
	/**
	 * 发表评论
	 * Enter description here ...
	 * @param unknown_type $uid
	 * @param unknown_type $showId
	 * @param unknown_type $content
	 */
	public function addComment($uid, $showId, $content) {
		list ( $uid, $showId, $content ) = array (intval ( $uid ), intval ( $showId ), trim ( $content ) );
		if ($uid < 1 || $showId < 1)
			return array (false, '参数错误' );
		if ($content == '')
			return array (false, '请输入评论内容' );
		$showInfo = $this->getModelFactory ()->getShowsModel ()->get ( $showId );
		if (! S::isArray ( $showInfo ))
			return array (false, '您评论的情书不存在或已被删除' );
		
		$fields = array ('uid' => $uid, 'showid' => $showId, 'description' => $content );
		$commentId = $this->getModelFactory ()->getCommentModel ()->add ( $fields );
		if (! $commentId)
			return array (false, '评论发表失败，请重试' );
		
		// verify uid has hold
		$data = array ();
		$data ['uid'] = $uid;
		$data ['showid'] = $showId;
		$data ['itemid'] = $commentId;
		$data ['type'] = SystemConst::ACTION_TYPE_COMMENT;
		$result = $this->getModelFactory ()->getRelationModel ()->add ( $data );
		if ($result) { //update show hold num
			$this->getModelFactory ()->getShowsModel ()->update ( $showId, array ('comment' => intval ( $showInfo ['comment'] ) + 1 ) );
			$count = $this->getModelFactory ()->getRelationModel ()->count ( $data );
			//计算单用户单条情书评论
			($count < 1) && $this->getModelFactory ()->getShowsModel ()->update ( $showId, array ('ucomment' => intval ( $showInfo ['ucomment'] ) + 1 ) );
		}
		return $result ? array (true, '评论成功' ) : array (false, '评论失败，请重试' );
	}
	
	public function updateUserShowNum($uid){
		$uid = intval ( $uid );
		if ($uid < 1)
			return false;
		$userInfo = $this->getModelFactory ()->getUserModel ()->get ( $uid );
		return $this->getModelFactory ()->getUserModel ()->update(array('shownum' => intval($userInfo['shownum'])+1), $uid);
	}
	
	private function checkShowAllowAction($showId) {
		$showId = intval ( $showId );
		if ($showId < 1)
			return array (false, '您支持的情书不存在' );
		$showInfo = $this->getModelFactory ()->getShowsModel ()->get ( $showId );
		if (! S::isArray ( $showInfo ))
			return array (false, '您支持的情书不存在或已被删除' );
		return array (true, '' );
	}

}