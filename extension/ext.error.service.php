<?php
! defined ( 'WEBNAME' ) && exit ( 'Forbidden' );
class errorService {
	public function showError($message, $url = "back") {
		$temp = '<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
				<link rel="stylesheet" type="text/css" href="misc/css/error_style.css" />
				<div id="error_tips">
					<h2>提示</h2>
					<div class="error_cont">';
		$endTemp = '</div></div>';
		if (! $url) {
			$temp .= '<ul><li>' . $message . '</li><li><a href="index.php">返回首页</a></li></ul>';
			$temp .= '<div class="error_return"><a href="index.php" class="btn">确定</a></div>';
			echo $temp . $endTemp;
			exit ();
		}
		if ($url !== 'back') {
			$temp .= '<ul><li>' . $message . '</li><li><a href="' . $url . '">3秒后自动返回</a></li></ul>';
			$temp .= '<div class="error_return"><a href="' . $url . '" class="btn">确定</a></div>';
			$temp .= '<meta http-equiv="refresh" content="3;url=' . $url . '">';
		} else {
			$temp .= '<ul><li>' . $message . '</li><li><a href="javascript:;" onclick="goback();">返回继续操作</a></li></ul>';
			$temp .= '<div class="error_return"><a href="javascript:;" onclick="goback();" class="btn">确定</a></div>';
			$temp .= '<script type="text/javascript">
var agt = navigator.userAgent.toLowerCase();
var is_ie = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
function goback(){
	var needReload = (is_ie ? history.length == 0 : history.length == 1); 
	if(needReload){self.location.reload();}else{history.back();}return false;
}
</script>';
		}
		echo $temp . $endTemp;
		exit ();
	}
	
	public function obHeader($url) {
		echo '<meta http-equiv="refresh" content="0;url=' . $url . '">';
	}
}