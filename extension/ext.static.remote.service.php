<?php
/**
 * @description - 获取客户端信息
 * @oldname - toolkit/common/remoteService.php
 * @newname - ext.static.remote.service.php
 * @operator - renbingbing/2011-11-15
 */
! defined ( 'WEBNAME' ) && exit ( 'Forbidden' );
class Ext_Static_Remote_Service {
	
	public static function getBrowser() {
		$browser = "";
		if (! isset ( $_SERVER ['HTTP_USER_AGENT'] ) || ! ($agent = $_SERVER ['HTTP_USER_AGENT']))
			return $browser;
		if (strpos ( $agent, 'Maxthon' )) {
			$browser = 'Maxthon';
		} elseif (strpos ( $agent, 'MSIE 8.0' )) {
			$browser = 'MSIE 8.0';
		} elseif (strpos ( $agent, 'MSIE 7.0' )) {
			$browser = 'MSIE 7.0';
		} elseif (strpos ( $agent, 'MSIE 6.0' )) {
			$browser = 'MSIE 6.0';
		} elseif (strpos ( $agent, 'Firefox' )) {
			$browser = 'Firefox';
		} elseif (strpos ( $agent, 'Chrome' )) {
			$browser = 'Chrome';
		} elseif (strpos ( $agent, 'Safari' )) {
			$browser = 'Safari';
		} elseif (strpos ( $agent, 'Opera' )) {
			$browser = 'Opera';
		} elseif (strpos ( $agent, 'NetCaptor' )) {
			$browser = 'NetCaptor';
		} elseif (strpos ( $agent, 'Netscape' )) {
			$browser = 'Netscape';
		} elseif (strpos ( $agent, 'Lynx' )) {
			$browser = 'Lynx';
		} elseif (strpos ( $agent, 'Konqueror' )) {
			$browser = 'Konqueror';
		} else {
			$browser = 'other';
		}
		return $browser;
	}
	
	public static function getUserOs() {
		$os = "";
		if (! isset ( $_SERVER ['HTTP_USER_AGENT'] ) || ! ($agent = $_SERVER ['HTTP_USER_AGENT']))
			return $os;
		if (strpos ( $agent, 'NT 5.1' )) {
			$os = 'Windows XP (SP2)';
		} elseif (strpos ( $agent, 'NT 5.2' ) && strpos ( $agent, 'WOW64' )) {
			$os = 'Windows XP 64-bit Edition';
		} elseif (strpos ( $agent, 'NT 5.2' )) {
			$os = 'Windows 2003';
		} elseif (strpos ( $agent, 'NT 6.0' )) {
			$os = 'Windows Vista';
		} elseif (strpos ( $agent, 'NT 5.0' )) {
			$os = 'Windows 2000';
		} elseif (strpos ( $agent, '4.9' )) {
			$os = 'Windows ME';
		} elseif (strpos ( $agent, 'NT 4' )) {
			$os = 'Windows NT 4.0';
		} elseif (strpos ( $agent, '98' )) {
			$os = 'Windows 98';
		} elseif (strpos ( $agent, '95' )) {
			$os = 'Windows 95';
		} elseif (strpos ( $agent, 'Mac' )) {
			$os = 'Mac';
		} elseif (strpos ( $agent, 'Linux' )) {
			$os = 'Linux';
		} elseif (strpos ( $agent, 'Unix' )) {
			$os = 'Unix';
		} elseif (strpos ( $agent, 'FreeBSD' )) {
			$os = 'FreeBSD';
		} elseif (strpos ( $agent, 'SunOS' )) {
			$os = 'SunOS';
		} elseif (strpos ( $agent, 'BeOS' )) {
			$os = 'BeOS';
		} elseif (strpos ( $agent, 'OS/2' )) {
			$os = 'OS/2';
		} elseif (strpos ( $agent, 'PC' )) {
			$os = 'Macintosh';
		} elseif (strpos ( $agent, 'AIX' )) {
			$os = 'AIX';
		} elseif (strpos ( $agent, 'IBM OS/2' )) {
			$os = 'IBM OS/2';
		} elseif (strpos ( $agent, 'BSD' )) {
			$os = 'BSD';
		} elseif (strpos ( $agent, 'NetBSD' )) {
			$os = 'NetBSD';
		} else {
			$os = 'other';
		}
		return $os;
	}
	
	public static function _ip2long($ip) {
		list ( , $ip ) = unpack ( 'l', pack ( 'l', ip2long ( $ip ) ) );
		return $ip;
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
	
	public static function checkUrlHttp($url, $sign = true) {
		if (empty ( $url ))
			return false;
		$url = rtrim ( $url, "/" );
		if ($sign) {
			return preg_match ( '/^http:\/\//', $url ) ? $url : 'http://' . $url;
		} else {
			return preg_match ( '/^http:\/\//i', $url ) ? str_replace ( 'http://', '', $url ) : $url;
		}
	}
	
	public static function getRequestInfo() {
		$info = array ();
		$info ['user_ip'] = self::getRealIp ();
		$info ['user_referer'] = isset ( $_SERVER ['HTTP_REFERER'] ) ? trim ( $_SERVER ['HTTP_REFERER'] ) : '';
		$info ['user_browser'] = trim ( self::getBrowser () );
		$info ['user_os'] = trim ( self::getUserOs () );
		$info ['user_address'] = isset ( $_SERVER ['REMOTE_ADDR'] ) ? trim ( $_SERVER ['REMOTE_ADDR'] ) : '';
		$info ['user_host'] = isset ( $_SERVER ['REMOTE_HOST'] ) ? trim ( $_SERVER ['REMOTE_HOST'] ) : '';
		$info ['user_port'] = isset ( $_SERVER ['REMOTE_PORT'] ) ? trim ( $_SERVER ['REMOTE_PORT'] ) : '';
		$info ['user_agent'] = isset ( $_SERVER ['HTTP_USER_AGENT'] ) ? trim ( $_SERVER ['HTTP_USER_AGENT'] ) : '';
		$info ['accept_info'] = isset ( $_SERVER ['HTTP_ACCEPT'] ) ? trim ( $_SERVER ['HTTP_ACCEPT'] ) : '';
		$info ['accept_charset'] = isset ( $_SERVER ['HTTP_ACCEPT_CHARSET'] ) ? trim ( $_SERVER ['HTTP_ACCEPT_CHARSET'] ) : '';
		$info ['accept_encoding'] = isset ( $_SERVER ['HTTP_ACCEPT_ENCODING'] ) ? trim ( $_SERVER ['HTTP_ACCEPT_ENCODING'] ) : '';
		$info ['accept_language'] = isset ( $_SERVER ['HTTP_ACCEPT_LANGUAGE'] ) ? trim ( $_SERVER ['HTTP_ACCEPT_LANGUAGE'] ) : '';
		$info ['connect_info'] = isset ( $_SERVER ['HTTP_CONNECTION'] ) ? trim ( $_SERVER ['HTTP_CONNECTION'] ) : '';
		$info ['query_string'] = isset ( $_SERVER ['QUERY_STRING'] ) ? trim ( $_SERVER ['QUERY_STRING'] ) : '';
		$info ['request_method'] = isset ( $_SERVER ['REQUEST_METHOD'] ) ? strtolower ( trim ( $_SERVER ['REQUEST_METHOD'] ) ) : '';
		$info ['request_time'] = isset ( $_SERVER ['REQUEST_TIME'] ) ? trim ( $_SERVER ['REQUEST_TIME'] ) : '';
		return $info;
	}
}