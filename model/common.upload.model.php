<?php
! defined ( 'WEBNAME' ) && exit ( 'Forbidden' );
require_once ROOT . '/config/general.php';
class CommonUploadModel extends BaseModel {
	
	/**
	 * 通用上传
	 * @param 标记 $sign(news,head,rule)
	 * @param 类别 $type(image flash media file mp3)
	 */
	public function commonUpload($file, $sign, $type, $params = array()) {
		$picResult = '';
		$uploadDir = General::getAttachPath ( $sign );
		$options = $this->getTypeOptions ( $type );
		S::isArray ( $params ) && $options = array_merge ( $params, $options );
		list ( $bool, $result, $thumbFileName ) = $this->getUploadService ( $uploadDir, $type, '', $options )->upload ( $file );
		return $bool ? array (true, '', sprintf ( '%s%s', $uploadDir, $result ), $thumbFileName ) : array (false, '上传错误：' . $result, '', '' );
	}
	
	public function verifyFilesAndUpload($files, $uploadDir, $filenamePre) {
		if (S::isArray ( $files ) && $uploadDir && isset ( $files ['name'] ) && $files ['name'] && (intval ( $files ['error'] ) === 0)) {
			if (! in_array ( $files ['type'], array ('image/gif', 'image/jpg', 'image/jpeg', 'image/png', 'image/x-png', 'image/pjpeg' ) )) {
				return array (false, '错误图片格式,支持gif,jpg,png', '' );
			}
			if ($files ['size'] > 500 * 1024)
				return array (false, '图片大小不能超过500K', '' );
			list ( $bool, $result ) = $this->uploadFile ( $files, $uploadDir, $filenamePre );
			if (! $bool) {
				return array (false, '图片上传失败:' . $result, '' );
			}
			return array (true, '图片上传成功', $result );
		}
		return array (false, '参数传递错误', '' );
	}
	
	public function deleteFile($file, $sign){
		$uploadDir = General::getAttachPath ( $sign );
		return $this->getUploadService ( $uploadDir, '', '', array())->delete($file);
	}
	
	private function getTypeOptions($type) {
		$options = array ();
		$options ['flash'] = array ('fileSize' => 1024 * 1024 * 20 );
		$options ['mp3'] = array ('fileSize' => 1024 * 1024 * 3 );
		return isset ( $options [$type] ) ? $options [$type] : array ();
	}
	
	private function uploadFile($fileArray, $uploadDir, $filenamePre = '') {
		if (! $fileArray || ! $uploadDir) {
			return false;
		}
		return $this->getUploadService ( $uploadDir, 'image' )->upload ( $fileArray );
	}
	
	private function getUploadService($directory, $type, $filenamePre = '', $options = array()) {
		$filenamePre = $filenamePre ? $filenamePre : Ext_Static_Common_Service::randCode ( 6 );
		require_once EXT_PATH . '/ext.fileupload.service.php';
		$uploadService = new Ext_FileUpload_Service ();
		$uploadService->init ( $directory, $type, $filenamePre, $options );
		return $uploadService;
	}
}