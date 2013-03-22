$(document).ready(function(){
	pagejs.initRightMenuImg();
	pagejs.initRightMenuImgToggle();
	pagejs.initIndexMenuImgToggle();
});
var pagejs={
	initRightMenuImg : function(){
		var $curA = $("#ev-rightmenu-box a.current");
		if($curA.length < 1) return false;
		var $orgImg = $curA.find("img");
		var $orgImgPath = $orgImg.attr("src");
		var $curImg = $orgImgPath.replace(".jpg", "_0.png");
		$orgImg.attr("src", $curImg);
	},
	initRightMenuImgToggle : function(){
		var $boxAs = $("#ev-rightmenu-box a");
		if($boxAs.length < 1) return false;
		$boxAs.not(".current").on("mousemove", function(){
			var $thisA = $(this);
			var $currA = $("#ev-rightmenu-box a.curr");
			if($currA.length){
				var $currImg = $currA.find("img");
				var $currImgPath = $currImg.attr("src");
				$currImg.attr("src", $currImgPath.replace("_1.jpg", ".jpg"));
				$currA.removeClass("curr");
			};
			if(!$thisA.hasClass("curr")){
				var $thisImg = $thisA.find("img");
				var $thisImgPath = $thisImg.attr("src");
				$thisImg.attr("src", $thisImgPath.replace(".jpg", "_1.jpg"));
				$thisA.addClass("curr");
			};
		});
		$("#ev-rightmenu-box a.curr").live("mouseout", function(){
			var $thisA = $(this);
			var $currImg = $thisA.find("img");
			var $currImgPath = $currImg.attr("src");
			$currImg.attr("src", $currImgPath.replace("_1.jpg", ".jpg"));
			$thisA.removeClass("curr");
		});
	},
	initIndexMenuImgToggle : function(){
		var $menuBox = $("#ev-index-menu-box");
		if($menuBox.length < 1) return false;
		$menuBox.find("img").on("mousemove", function(){
			var $thisImg = $(this);
			var $currImg = $menuBox.find("img.curr");
			if($currImg.length){
				var $currImgPath = $currImg.attr("src");
				$currImg.attr("src", $currImgPath.replace("_1.png", ".png"));
				$currImg.removeClass("curr");
			};
			if(!$thisImg.hasClass("curr")){
				var $thisImgPath = $thisImg.attr("src");
				$thisImg.attr("src", $thisImgPath.replace(".png", "_1.png"));
				$thisImg.addClass("curr");
			};
		});
		$menuBox.find("img.curr").live("mouseout", function(){
			var $currImg = $(this);
			var $currImgPath = $currImg.attr("src");
			$currImg.attr("src", $currImgPath.replace("_1.png", ".png"));
			$currImg.removeClass("curr");
		});
		
	}
};

var common={
	getLen : function (str) {
		var len = 0;
		for ( var i = 0; i < str.length; i++) {
			if (str.charCodeAt(i) > 127)
				len += 3;
			else
				len++;
		}
		return len;
	},
	pageReload : function(url){
		if(!common.isDefined(url) || url == ''){
			window.location.reload(); 
		}else{
			window.location = url;
		}
	},
	isDefined : function(param){
		var bool = ("undefined" == typeof param);
		return bool ? false : true;
	},
	verifyUrl : function(url){
		var urlreg=/^((https|http)?:\/\/)+[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/  
		return urlreg.test(url);
	},
	formatUrl : function(url){
		return url + "&_t=" + Math.random();
	}
}
