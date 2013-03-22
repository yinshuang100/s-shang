<?php
class General {
	
	public static function getHashConfig() {
		$config = array ();
		$config ['dbsitehash'] = 'eF3$^2Qa';
		$config ['dbcookiepre'] = 'ysf_';
		$config ['dbcookiename'] = 's-shang';
		$config ['dbcookiedate'] = 24 * 3600;
		return $config;
	}
	
	public static function getAttachPath($key) {
		$configs = self::getAttachPathConfig ();
		return isset ( $configs [$key] ) ? $configs [$key] : $configs ['default'];
	}
	
	public static function getAttachPathConfig() {
		$data = array ();
		$data ['default'] = sprintf ( 'data/%s/', date ( 'Ym', time () ) );
		$data ['articles'] = 'data/news/';
		$data ['product'] = 'data/product/';
		$data ['cases'] = 'data/cases/';
		$data ['ecmm'] = 'data/ecmm/';
		return $data;
	}
}