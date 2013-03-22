<?php
! defined ( 'WEBNAME' ) && exit ( 'Forbidden' );
class Ext_Static_Common_Service {
	
	public static function filterPage($page, $perpage) {
		$page = intval ( $page ) ? intval ( $page ) : 1;
		$start = ($page - 1) * $perpage;
		$start = intval ( $start );
		$perpage = intval ( $perpage );
		return array ($start, $perpage, $page );
	}
	
	public static function getPageNav($count, $page, $numofpage, $url, $max = null, $ajaxCallBack = '') {
		$total = $numofpage;
		if (! empty ( $max )) {
			$max = ( int ) $max;
			$numofpage > $max && $numofpage = $max;
		}
		if ($numofpage <= 1 || ! is_numeric ( $page )) {
			return '';
		}
		$ajaxurl = $ajaxCallBack ? " onclick=\"return $ajaxCallBack(this.href);\"" : '';
		$tmp = explode ( '#', $url );
		count ( $tmp ) > 1 ? list ( $url, $mao ) = $tmp : list ( $url, $mao ) = array (current ( $tmp ), '' );
		$mao && $mao = '#' . $mao;
		$pages = "<div class=\"pages\"><a href=\"{$url}page=1$mao\"{$ajaxurl}>&laquo;</a>";
		($page > 1) && $pages .= "<a href=\"{$url}page=" . ($page - 1) . "$mao\"{$ajaxurl}>上一页</a>";
		for($i = $page - 3; $i <= $page - 1; $i ++) {
			if ($i < 1)
				continue;
			$pages .= "<a class=\"number\" href=\"{$url}page=$i$mao\"{$ajaxurl}>$i</a>";
		}
		$pages .= "<a class=\"number current\" href=\"#\">$page</a>";
		if ($page < $numofpage) {
			$flag = 0;
			for($i = $page + 1; $i <= $numofpage; $i ++) {
				$pages .= "<a class=\"number\" href=\"{$url}page=$i$mao\"{$ajaxurl}>$i</a>";
				$flag ++;
				if ($flag == 4)
					break;
			}
		}
		($page < $numofpage) && $pages .= "<a href=\"{$url}page=" . ($page + 1) . "$mao\"{$ajaxurl}>下一页</a>";
		$pages .= "<a href=\"{$url}page=$numofpage$mao\"{$ajaxurl}>&raquo;</a></div>";
		return $pages;
	}
	
	public static function aheader($url) {
		echo '<meta http-equiv="refresh" content="0;url=' . $url . '">';
		exit ();
	}
	
	public static function getPicSourceInfo($picUrl) {
		$picUrl = trim ( $picUrl );
		if ($picUrl == '')
			array (0, '' );
		return (strpos ( $picUrl, 'http://' ) === 0) ? array (1, $picUrl ) : array (0, $picUrl );
	}
	
	public static function createfile($fileName, $filePath, $content) {
		if (! is_dir ( $filePath )) {
			@mkdir ( $filePath );
		}
		@chmod ( $filePath, 0777 );
		return file_put_contents ( $filePath . '/' . $fileName, $content );
	}
	
	public static function deletefile($fileName, $filePath) {
		if ($fileName == '')
			return false;
		require_once EXT_PATH . '/ext.fileupload.service.php';
		$_uploadService = new Ext_FileUpload_Service ();
		$_uploadService->init ( $filePath );
		return $_uploadService->delete ( $fileName );
	}
	
	public static function readover($fileName, $method = 'rb') {
		$fileName = S::escapePath ( $fileName );
		$data = '';
		if ($handle = @fopen ( $fileName, $method )) {
			flock ( $handle, LOCK_SH );
			$data = @fread ( $handle, filesize ( $fileName ) );
			fclose ( $handle );
		}
		return $data;
	}
	
	public static function StrCode($string, $action = 'ENCODE') {
		$dbsitehash = self::getHash ();
		$action != 'ENCODE' && $string = base64_decode ( $string );
		$code = '';
		$key = substr ( md5 ( $_SERVER ['HTTP_USER_AGENT'] . $dbsitehash ), 8, 18 );
		$keyLen = strlen ( $key );
		$strLen = strlen ( $string );
		for($i = 0; $i < $strLen; $i ++) {
			$k = $i % $keyLen;
			$code .= $string [$i] ^ $key [$k];
		}
		return ($action != 'DECODE' ? base64_encode ( $code ) : $code);
	}
	
	public static function writeover($fileName, $data, $method = 'rb+', $ifLock = true, $ifCheckPath = true, $ifChmod = true) {
		$fileName = S::escapePath ( $fileName, $ifCheckPath );
		touch ( $fileName );
		$handle = fopen ( $fileName, $method );
		$ifLock && flock ( $handle, LOCK_EX );
		$writeCheck = fwrite ( $handle, $data );
		$method == 'rb+' && ftruncate ( $handle, strlen ( $data ) );
		fclose ( $handle );
		$ifChmod && @chmod ( $fileName, 0777 );
		return $writeCheck;
	}
	
	public static function GetCookie($cookieName) {
		if (isset ( $_COOKIE [self::CookiePre () . '_' . $cookieName] ))
			return $_COOKIE [self::CookiePre () . '_' . $cookieName];
		return false;
	}
	
	public static function Cookie($cookieName, $cookieValue, $expireTime = 'F', $needPrefix = true) {
		$sIsSecure = null;
		$cookiePath = '/';
		$cookieDomain = '';
		$isHttponly = false;
		if ($cookieName == 'AdminUser' || $cookieName == 'winduser') {
			$agent = strtolower ( $_SERVER ['HTTP_USER_AGENT'] );
			if (! ($agent && preg_match ( '/msie ([0-9]\.[0-9]{1,2})/i', $agent ) && strstr ( $agent, 'mac' ))) {
				$isHttponly = true;
			}
		}
		$cookieValue = str_replace ( "=", '', $cookieValue );
		strlen ( $cookieValue ) > 512 && $cookieValue = substr ( $cookieValue, 0, 512 );
		$needPrefix && $cookieName = self::CookiePre () . '_' . $cookieName;
		$timestamp = time ();
		if ($expireTime == 'F') {
			$expireTime = $timestamp + 31536000;
		} elseif ($cookieValue == '' && $expireTime == 0) {
			return setcookie ( $cookieName, '', $timestamp - 31536000, $cookiePath, $cookieDomain, $sIsSecure );
		}
		return setcookie ( $cookieName, $cookieValue, $expireTime, $cookiePath, $cookieDomain, $sIsSecure, $isHttponly );
	}
	
	public static function randNumberCode($length = 32) {
		$chars = '1234567890';
		$chars_length = (strlen ( $chars ) - 1);
		$string = $chars {rand ( 0, $chars_length )};
		for($i = 1; $i < $length; $i = strlen ( $string )) {
			$r = $chars {rand ( 0, $chars_length )};
			if ($r != $string {$i - 1})
				$string .= $r;
		}
		return $string;
	}
	
	public static function randCode($length = 32) {
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
		$chars_length = (strlen ( $chars ) - 1);
		$string = $chars {rand ( 0, $chars_length )};
		for($i = 1; $i < $length; $i = strlen ( $string )) {
			$r = $chars {rand ( 0, $chars_length )};
			if ($r != $string {$i - 1})
				$string .= $r;
		}
		return $string;
	}
	
	/**
	 * 显示友好时间格式
	 * @param integer $time
	 */
	public static function getLastDate($time) {
		$time = intval ( $time );
		if ($time < 1)
			return '';
		$timestamp = time ();
		$tdtime = strtotime ( date ( 'Y-m-d', $timestamp ) );
		$decrease = $timestamp - $time;
		$thistime = strtotime ( date ( 'Y-m-d', $time ) );
		$thisyear = strtotime ( date ( 'Y', $time ) );
		$thistime_without_day = date ( 'H:i', $time );
		$yeartime = strtotime ( date ( 'Y', $timestamp ) );
		$originalData = date ( "Y-m-d H:i", $time );
		$originalTime = date ( "m-d H:i", $time );
		if ($decrease <= 0) {
			return $originalData;
		}
		if ($thistime == $tdtime) {
			if ($decrease <= 60) {
				return $decrease . '秒前';
			}
			return ($decrease <= 3600) ? (ceil ( $decrease / 60 ) . '分钟前') : (ceil ( $decrease / 3600 ) . '小时前');
		} elseif ($thistime == $tdtime - 86400) {
			return '昨天' . $thistime_without_day;
		} elseif ($thistime == $tdtime - 172800) {
			return '前天' . $thistime_without_day;
		} elseif ($thisyear == $yeartime) {
			return $originalTime;
		}
		return $originalData;
	}
	
	public static function checkUrlIsExist($url, $isReturnContent = FALSE) {
		return self::sendClientRequestInfo ( $url, $isReturnContent );
	}
	
	public static function arrayToString($array) {
		return base64_encode ( serialize ( $array ) );
	}
	
	public static function stringToArray($string) {
		return unserialize ( base64_decode ( $string ) );
	}
	
	public static function parseDomainName($url) {
		return ($url) ? trim ( str_replace ( array ("http://", "https://" ), array ("" ), $url ), "/" ) : "";
	}
	
	public static function getRealIp() {
		if (isset ( $_SERVER ['HTTP_X_FORWARDED_FOR'] ) && $_SERVER ['HTTP_X_FORWARDED_FOR'] && $_SERVER ['REMOTE_ADDR']) {
			if (strstr ( $_SERVER ['HTTP_X_FORWARDED_FOR'], ',' )) {
				$x = explode ( ',', $_SERVER ['HTTP_X_FORWARDED_FOR'] );
				$_SERVER ['HTTP_X_FORWARDED_FOR'] = trim ( end ( $x ) );
			}
			if (preg_match ( '/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER ['HTTP_X_FORWARDED_FOR'] )) {
				return $_SERVER ['HTTP_X_FORWARDED_FOR'];
			}
		} elseif (isset ( $_SERVER ['HTTP_CLIENT_IP'] ) && $_SERVER ['HTTP_CLIENT_IP'] && preg_match ( '/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER ['HTTP_CLIENT_IP'] )) {
			return $_SERVER ['HTTP_CLIENT_IP'];
		}
		if (preg_match ( '/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER ['REMOTE_ADDR'] )) {
			return $_SERVER ['REMOTE_ADDR'];
		}
		return 'Unknown';
	}
	
	public static function convertCharset($text, $charset) {
		return ($charset && ! in_array ( strtolower ( $charset ), array ('utf8', 'utf-8' ) )) ? self::gbkToUtf8 ( $text ) : $text;
	}
	
	public static function gbkToUtf8($text) {
		return (! $text) ? "" : @iconv ( 'GBK', 'UTF-8//IGNORE', $text );
	}
	
	public static function utf8ToGbk($text) {
		return (! $text) ? "" : @iconv ( 'UTF-8', 'GBK//IGNORE', $text );
	}
	
	public static function sendClientRequest($url, $data) {
		require_once EXT_PATH . '/ext.httpclient.service.php';
		return Ext_HttpClient_Service::request ( 'curl', $url . '&randcode=' . self::getClientSecurityCode (), $data );
	}
	
	public static function sendClientRequestInfo($url, $isReturnContent) {
		require_once EXT_PATH . '/ext.httpclient.service.php';
		$result = CurlHttpClient::requestInfo ( $url . '&randcode=' . self::getClientSecurityCode (), $isReturnContent );
		return $isReturnContent ? $result : ($result == 200 ? true : false);
	}
	
	public static function getClientSecurityCode() {
		return md5 ( date ( 'Y-m-d' ) . "-" . "UIneIOEONIE8983K83kINIEELLI9e87" . "-" . date ( 'Y-m-d' ) );
	}
	
	public static function buildSecutiryCode($string, $key, $action = 'ENCODE') {
		$action != 'ENCODE' && $string = base64_decode ( $string );
		$code = '';
		$key = substr ( md5 ( $key ), 8, 18 );
		$keyLen = strlen ( $key );
		$strLen = strlen ( $string );
		for($i = 0; $i < $strLen; $i ++) {
			$k = $i % $keyLen;
			$code .= $string [$i] ^ $key [$k];
		}
		return ($action != 'DECODE' ? base64_encode ( $code ) : $code);
	}
	
	public static function getTemplateData($templatePath, $params = array()) {
		$templatePath = S::escapePath ( $templatePath );
		if (! is_file ( $templatePath ))
			return false;
		$template = '';
		ob_start ();
		include $templatePath;
		$template = ob_get_contents ();
		ob_end_clean ();
		return $template;
	}
	
	public static function substrs($content, $length, $charset = 'utf-8') {
		if (strlen ( $content ) > $length) {
			if ($charset != 'utf-8') {
				$cutStr = '';
				for($i = 0; $i < $length - 1; $i ++) {
					$cutStr .= ord ( $content [$i] ) > 127 ? $content [$i] . $content [++ $i] : $content [$i];
				}
				$i < $length && ord ( $content [$i] ) <= 127 && $cutStr .= $content [$i];
				return $cutStr . '...';
			}
			return self::utf8Trim ( substr ( $content, 0, $length ) ) . '...';
		}
		return $content;
	}
	
	public static function cutStrHtml($string, $sublen) {
		$string = strip_tags ( $string );
		$string = preg_replace ( '/\n/is', '', $string );
		$string = preg_replace ( '/ |　/is', '', $string );
		$string = preg_replace ( '/&nbsp;/is', '', $string );
		
		preg_match_all ( "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $t_string );
		if (count ( $t_string [0] ) - 0 > $sublen)
			$string = join ( '', array_slice ( $t_string [0], 0, $sublen ) ) . "…";
		else
			$string = join ( '', array_slice ( $t_string [0], 0, $sublen ) );
		return $string;
	}
	
	private static function utf8Trim($str) {
		$hex = '';
		$len = strlen ( $str ) - 1;
		for($i = $len; $i >= 0; $i -= 1) {
			$ch = ord ( $str [$i] );
			$hex .= " $ch";
			if (($ch & 128) == 0 || ($ch & 192) == 192) {
				return substr ( $str, 0, $i );
			}
		}
		return $str . $hex;
	}
	
	public static function generalUniqueId() {
		return "2008" . time () . rand ( 1000, 9999 );
	}
	
	public static function CookiePre() {
		return substr ( self::getHash (), 0, 5 );
	}
	
	public static function getHash() {
		return md5 ( '&(*^&*ljlksd' );
	}
}