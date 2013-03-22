
function trim(str) {
	for ( var i = 0; i < str.length && str.charAt(i) == "  "; i++)
		;
	for ( var j = str.length; j > 0 && str.charAt(j - 1) == "  "; j--)
		;
	if (i > j)
		return "";
	return str.substring(i, j);
}

function checkspace(checkstr) {
	var str = '';
	for (i = 0; i < checkstr.length; i++) {
		str = str + ' ';
	}
	return (str == checkstr);
}

function isNum(str){
	var regNum =/^\d+$/;
	return regNum.test(str)  //��������ַ���true�������false
}

// ///////////////////
function getLen(str) {
	var len = 0;
	for ( var i = 0; i < str.length; i++) {
		if (str.charCodeAt(i) > 127)
			len += 3; // utf8��ʽ������ռ3λ��gb2312���޸�λ2λ
		else
			len++;
	}
	return len;
}

function changeInputTips(obj, status, cssname, msg){
	if(!obj) return false;
	if(status == "+"){
		obj.next("span").addClass(cssname); 
	}else if(status == "-"){
		obj.next("span").removeClass(cssname);
	}
	obj.next("span").html(msg);
}