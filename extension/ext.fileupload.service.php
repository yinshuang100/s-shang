<?php
! defined ( 'WEBNAME' ) && exit ( 'Forbidden' );
/**
 * @description - 上传服务
 * @oldname - toolkit/common/uploadService.php
 * @newname - ext.fileupload.service.php
 */

class Ext_FileUpload_Service {
	private $allowSuffix;
	private $tmpName;
	private $name;
	private $size;
	private $directory;
	private $ext;
	private $type;
	private $filename;
	private $fileSize;
	private $thumbWidth;
	private $thumbHeight;
	private $thumbPrefix;
	private $isThumb;
	
	public function init($dirctory, $type, $fileName = '', $options = array()) {
		$this->directory = & $dirctory;
		$this->type = $type;
		$this->fileName = ($fileName) ? $fileName . date ( "YmdHis", time () ) : date ( "YmdHis", time () );
		$this->setDefaultOptions ( $options );
	}
	
	public function upload($file) {
		if ($errorMsg = $this->checkFileError ( $file ['error'] ))
			return array (false, $errorMsg );
		if (! isset ( $file ['tmp_name'] ) || $file ['tmp_name'] == '' || ! isset ( $file ['name'] ) || $file ['name'] == '') {
			return array (false, '上传文件为空' . $file ['name'] );
		}
		list ( $bool, $msg ) = $this->checkAllowExt ( $file );
		if (! $bool)
			return array (false, '上传文件类型不支持，仅允许上传类型：' . $msg );
		if (! $this->checkFileSize ( $file )) {
			return array (false, '上传文件大小不能超过' . $this->getSizeMsg ( $this->fileSize ) );
		}
		$tmpName = str_replace ( "\\\\", "\\", $file ['tmp_name'] );
		if (! is_uploaded_file ( $tmpName )) {
			return array (false, '未知错误' . $tmpName );
		}
		$this->_createDirectory ();
		$fileName = $this->_create ( $file );
		$thumbFileName = ($this->type == 'image' && $this->isThumb && $fileName) ? $this->_createThumb ( $fileName ) : "";
		return array (true, $fileName, $thumbFileName );
	}
	
	public function delete($fileName, $isThumb = true) {
		$filepath = $this->directory . $fileName;
		$thumbPath = $this->directory . $this->thumbPrefix . $fileName;
		if (file_exists ( $filepath )) {
			@unlink ( $filepath );
		}
		if ($isThumb && file_exists ( $thumbPath )) {
			@unlink ( $thumbPath );
		}
		return true;
	}
	
	private function getExts($type) {
		$data = array ();
		$data ['image'] = array ('gif', 'jpg', 'jpeg', 'png', 'bmp' );
		$data ['flash'] = array ('swf', 'flv' );
		$data ['mp3'] = array ('mp3', 'wav', 'wmv' );
		$data ['media'] = array ('swf', 'flv', 'avi', 'mpg', 'asf', 'rm', 'rmvb' );
		$data ['file'] = array ('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2' );
		return isset ( $data [$type] ) ? $data [$type] : $data ['image'];
	}
	
	private function setDefaultOptions($options) {
		$this->isThumb = isset ( $options ['isThumb'] ) ? $options ['isThumb'] : false;
		$this->fileSize = isset ( $options ['fileSize'] ) ? intval ( $options ['fileSize'] ) : 1024 * 1024 * 2;
		$this->thumbWidth = isset ( $options ['thumbWidth'] ) ? intval ( $options ['thumbWidth'] ) : 60;
		$this->thumbHeight = isset ( $options ['thumbHeight'] ) ? intval ( $options ['thumbHeight'] ) : 60;
		$this->thumbPrefix = isset ( $options ['thumbPrefix'] ) ? trim ( $options ['thumbPrefix'] ) : 'thumb_';
	}
	
	private function checkAllowExt($file) {
		$this->ext = substr ( $file ['name'], strrpos ( $file ['name'], "." ) + 1 );
		$allExt = $this->getExts ( $this->type );
		if (! in_array ( strtolower ( $this->ext ), $allExt )) {
			return array (false, implode ( '|', $allExt ) );
		}
		return array (true, '' );
	}
	
	private function checkFileSize($file) {
		if (filesize ( $file ['tmp_name'] ) > $this->fileSize) {
			return false;
		}
		return true;
	}
	
	private function getSizeMsg($size) {
		$msg = '';
		if ($size > 0 && $size < 1024) {
			$msg = $size . 'B';
		} elseif ($size >= 1024 && $size < 1024 * 1024) {
			$msg = $size / 1024 . 'K';
		} elseif ($size >= 1024 * 1024) {
			$msg = $size / (1024 * 1024) . 'M';
		}
		return $msg;
	}
	
	private function checkFileError($errorCode) {
		$error = '';
		if (! empty ( $errorCode )) {
			switch ($errorCode) {
				case '1' :
					$error = '超过php.ini允许的大小。';
					break;
				case '2' :
					$error = '超过表单允许的大小。';
					break;
				case '3' :
					$error = '图片只有部分被上传。';
					break;
				case '4' :
					$error = '请选择图片。';
					break;
				case '6' :
					$error = '找不到临时目录。';
					break;
				case '7' :
					$error = '写文件到硬盘出错。';
					break;
				case '8' :
					$error = 'File upload stopped by extension。';
					break;
				case '999' :
				default :
					$error = '未知错误';
			}
		}
		return $error;
	}
	
	private function _createDirectory() {
		if (! is_dir ( $this->directory )) {
			@mkdir ( $this->directory, 0777 );
		}
	}
	
	private function _create($file) {
		$filename = $this->fileName . "." . $this->ext;
		$destination = $this->directory . $filename;
		if (move_uploaded_file ( $file ['tmp_name'], $destination )) {
			return $filename;
		} elseif (copy ( $file ['tmp_name'], $destination )) {
			return $filename;
		}
		return FALSE;
	}
	
	private function _createThumb($filename) {
		$filepath = $this->directory . $filename;
		list ( $width, $height, $type ) = getimagesize ( $filepath );
		switch (strtolower ( $type )) {
			case 1 :
				$source = imagecreatefromgif ( $filepath );
				break;
			case 2 :
				$source = imagecreatefromjpeg ( $filepath );
				break;
			case 3 :
				$source = imagecreatefrompng ( $filepath );
				break;
			default :
				return;
		}
		$thumb = imagecreatetruecolor ( $this->thumbWidth, $this->thumbHeight );
		imagecopyresized ( $thumb, $source, 0, 0, 0, 0, $this->thumbWidth, $this->thumbHeight, $width, $height );
		$thumbfilename = $this->directory . $this->thumbPrefix . $filename;
		switch (strtolower ( $type )) {
			case 1 :
				imageGIF ( $thumb, $thumbfilename );
			case 2 :
				imageJPEG ( $thumb, $thumbfilename );
			case 3 :
				imagePNG ( $thumb, $thumbfilename );
		}
		unset ( $source );
		unset ( $thumb );
		return $thumbfilename;
	}
}
?>