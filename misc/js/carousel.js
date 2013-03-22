$(function(){
    
    /* Plugin carousel */   
    Carousel = {
        defaultOptions: { 
            group : 1,
            loop: true,
            active: 1,
            transition: { type: "sliding", color: false, easing: "", speed: 300 },
            direction: true,
            pager: false,
            keyboard: true,
            touch: false,
            thumb: false,
            fullWidth: true,
            diaporama: { active: false, duration: 5000, pause: true }
        },
        init: function(el,options) {
            thiscarousel = this;
            thiscarousel.opts = $.extend({},thiscarousel.defaultOptions, options);
            thiscarousel.el = el;
            thiscarousel.$$ = $(thiscarousel.el);
            thiscarousel.elCount = Math.ceil($(".carousel-view li", thiscarousel.$$).length);
            thiscarousel.slideCount = Math.ceil(thiscarousel.elCount / thiscarousel.opts.group);
            thiscarousel.slideCurrent = thiscarousel.opts.active;
            thiscarousel.isMoving = false;
            thiscarousel.buildView();
            thiscarousel.buildNav();
            if(thiscarousel.opts.callback) {
                thiscarousel.opts.callback();
            }
        },
        buildView: function() {
            if(thiscarousel.opts.transition.type=="fading") {
                $(".carousel-view > ul > li:eq("+ thiscarousel.slideCurrent-1 +")",thiscarousel.$$).siblings().css("z-index",1).hide();
                if(thiscarousel.opts.transition.color) {
                    $(".carousel-view",thiscarousel.$$).css("background-color", thiscarousel.opts.transition.color);
                }
            } else { 
                if(thiscarousel.opts.fullWidth) {
                    $(window).resize(function(){ 
                        thiscarousel.resizeCarousel();
                    });
                    thiscarousel.resizeCarousel();
                } else {
                    thiscarousel.elWidth = $(".carousel-view > ul > li", thiscarousel.$$).outerWidth(true);
                    thiscarousel.slideWidth = thiscarousel.elWidth * thiscarousel.opts.group;
                    thiscarousel.leftPosition = -(thiscarousel.slideCurrent-1)*thiscarousel.slideWidth;
                    thiscarousel.leftPositionMax = (thiscarousel.elCount > 1) ? (thiscarousel.slideCount-1) * (-thiscarousel.slideWidth) : 0;
                    thiscarousel.leftPositionCloned = (thiscarousel.elCount > 1) ? (thiscarousel.slideCount) * (-thiscarousel.slideWidth) : 0;
                }
                if(thiscarousel.opts.group>1) {
                    var modulo = thiscarousel.elCount % thiscarousel.opts.group;
                    if(modulo) {
                        var elOldMargin = parseInt($(".carousel-view > ul > li:first",thiscarousel.$$).css("margin-right"));
                        var elNewMargin = (thiscarousel.opts.group - modulo) * thiscarousel.elWidth + elOldMargin;
                        $(".carousel-view > ul > li:last", thiscarousel.$$).css("margin-right", elNewMargin);
                    }
                }
                /* generate html code */
                $(".carousel-view", thiscarousel.$$)
                    .css("overflow", "hidden")
                        .find(">ul")
                            .css("width", (thiscarousel.slideCount+1) * thiscarousel.slideWidth)
                            .css("left", thiscarousel.leftPosition)
                        .find(">li:lt("+thiscarousel.opts.group+")")
                            .clone()
                            .appendTo($(".carousel-view >ul",thiscarousel.$$));
            }
        },
        buildNav: function() {
            if(thiscarousel.opts.direction || thiscarousel.opts.pager) {
                $nav = $("<div/>",{"class":"carousel-nav png"}).appendTo(thiscarousel.$$);
                if(thiscarousel.opts.direction) {
                    thiscarousel.buildDirection($nav)
                }
                if(thiscarousel.opts.pager) {
                    thiscarousel.buildPager($nav)
                }
				if(thiscarousel.elCount < 2){
					if($('.carousel-view-text', thiscarousel.$$).length){
						$('.carousel-direction', thiscarousel.$$).hide();
					}else{
						$('.carousel-nav', thiscarousel.$$).hide();
					}
				}
                thiscarousel.updateNav($nav);
            }
            if(thiscarousel.opts.keyboard) {
                $(document).keydown(function(e){
                    if(e.keyCode=="37") {
                        thiscarousel.goTo(thiscarousel.slideCurrent-1);
                        return false;
                    } else if(e.keyCode=="39") {
                        thiscarousel.goTo(thiscarousel.slideCurrent+1);
                        return false;
                    }
                });
            }
            if(thiscarousel.opts.template == "quisommesnous") {
                $('.carousel-view-text .heading span', thiscarousel.$$).live({
                    'click': function() {
                        if(!$(this).hasClass('heading-active')) {
                            spanId = $(this).prevAll().length;
                            $(this).siblings().removeClass('heading-active');
                            $('.carousel-view-text>div:visible', thiscarousel.$$).fadeOut('fast', function(){
                                 $('.carousel-view-text>div:eq('+ spanId +')', thiscarousel.$$).fadeIn('fast');
                                 $('.carousel-view-text .heading span:eq('+ spanId +')', thiscarousel.$$).addClass('heading-active');
                            });
                            
                            $(this).parent().css('background-position', 134*spanId+'px bottom')
                        }
                    }, 'mouseenter': function() {
                        $('body').css('cursor', 'pointer');
                    }, 'mouseleave': function() {
                        $('body').css('cursor', 'auto');
                    }
                });
            }
        },
        buildDirection: function(nav) {
             var $ul = $('<div/>', { 'class':'carousel-direction'});
             var $prev = $('<a/>', {
                 'class': 'carousel-prev',
                 html:  '<span class="icon icon-prev"></span>'
             }).bind("click", function() { thiscarousel.goTo(thiscarousel.slideCurrent - 1); return false; })
               .appendTo($ul);               
             $('<span class="carousel-item-active">' + thiscarousel.formatNumber(thiscarousel.slideCurrent) +
               '</span><sup class="carousel-item-total">/ ' + thiscarousel.formatNumber(thiscarousel.slideCount) + '</sup>')
                .appendTo($ul);
             var $next = $('<a/>', {
                'class': 'carousel-next',
                 html:  '<span class="icon icon-next"><span />'
             }).bind("click", function() { thiscarousel.goTo(thiscarousel.slideCurrent + 1); return false; })
               .appendTo($ul);
             $ul.appendTo(nav);
             
             if(thiscarousel.opts.template == 'home') {
                 var $div = $('<div/>', { 'class':'carousel-view-text'});
                 $('.carousel-view .carousel-view-text', thiscarousel.$$).hide();
                 $($('.carousel-view-text').eq(thiscarousel.slideCurrent-1).html()).appendTo($div);
                 $div.appendTo(nav);
             }

               

        },
        formatNumber: function(number){
            return (number>9) ? number : '0'+number;
        },
        centerNav: function(nav){
            var marginTop = nav.height()/2;
            nav.css('margin-top', '-'+marginTop+'px');
        },
        buildPager: function(nav) {
            var $ul = $("<ul/>", { "class":"carousel-pager"} );
            for(var i=1; i<= thiscarousel.slideCount; i++) {
                var active = (i==1) ? "carousel-page-active" : "";
                var first  = (i==1) ? "carousel-page-first" : "";
                var last  = (i==thiscarousel.slideCount) ? "carousel-page-last" : "";
                var $li = $("<li/>", {
                    "class": "carousel-page-"+i+" "+active+" "+first+" "+last,
                    html: "<a href=\"#\" class=\"icon\">"+i+"</a>"
                }).bind("click", function() { thiscarousel.goTo(parseInt($(this).text())); })
                  .appendTo($ul);
            }
            $ul.appendTo(nav);
        },
        goTo: function(slide, looping) {  
            if( thiscarousel.existSlide(slide) == true ) {
                if(thiscarousel.isMoving == false) {
                    thiscarousel.isMoving = true;
                    if( thiscarousel.opts.transition.type == "fading" ) {
                        $(".carousel-view li:eq("+parseInt(slide-1)+")", thiscarousel.$$).css("z-index",3)
                            .fadeIn("normal", function(){
                                $(".carousel-view li:eq("+parseInt(slide-1)+")", thiscarousel.$$)
                                    .css("z-index",2)
                                    .show()
                                .siblings()
                                    .css("z-index",1)
                                    .hide();
                                thiscarousel.goneTo(slide);
                            });
                    } else {
                        thiscarousel.leftPosition = (parseInt(slide)-1) * (-thiscarousel.slideWidth);
                        if(looping == "carousel-page-first") { 
                            thiscarousel.leftPosition = thiscarousel.leftPositionCloned;
                        } else if(looping == "carousel-page-last") { 
                            $(".carousel-view ul", thiscarousel.$$).css("left", thiscarousel.leftPositionCloned);
                        }
                        $(".carousel-view ul", thiscarousel.$$).animate({
                            left: thiscarousel.leftPosition
                        }, thiscarousel.opts.speed, function(){
                            if(looping == "carousel-page-first") { 
                                thiscarousel.leftPosition = 0;
                                $(".carousel-view ul", thiscarousel.$$).css("left", thiscarousel.leftPosition);
                            }
                            thiscarousel.goneTo(slide);
                        });
                    } 
                }
            } else {
                if(thiscarousel.opts.loop) { 
                    slide = (slide < 1) ? thiscarousel.slideCount : 1;
                    looping = (slide == 1) ? "carousel-page-first" : "carousel-page-last";
                    thiscarousel.goTo(slide, looping);   
                }
            }
            return false;
        },
        goneTo: function(slide) {
            thiscarousel.slideCurrent = slide;
            thiscarousel.isMoving = false;
            thiscarousel.updateNav($(".carousel-nav",thiscarousel.$$));
        },
        existSlide: function(slide) {
            if((slide<1) || (slide>thiscarousel.slideCount)) {
                return false;
            } else {
                return  true;
            }
        },
        updateNav: function(nav) { 
            if(thiscarousel.opts.direction) {
                if(thiscarousel.opts.loop == false) {
                    if(thiscarousel.slideCurrent == 1) {
                        $(".carousel-prev", nav).hide().siblings().show();
                    } else if (thiscarousel.slideCurrent == thiscarousel.slideCount) {
                        $(".carousel-next", nav).hide().siblings().show();
                    } else {
                        $(".carousel-prev, .carousel-next", nav).show();
                    }
                }
                $('.carousel-item-active',nav).text(thiscarousel.formatNumber(thiscarousel.slideCurrent));
                $('.carousel-item-total',nav).text('/ '+thiscarousel.formatNumber(thiscarousel.slideCount));
                $('.carousel-view-text',nav).html($('.carousel-view .carousel-view-text',thiscarousel.$$).eq(thiscarousel.slideCurrent-1).html());
                if(thiscarousel.opts.template == 'home' || thiscarousel.opts.template == 'media') {
                    //thiscarousel.centerNav(nav);
					var color = $(".carousel-view ul li", thiscarousel.$$).eq(thiscarousel.slideCurrent - 1).data('color');
					if(nav.data('color')) nav.removeClass(nav.data('color'))
					nav.addClass(color).data('color', color);
                    if ($.browser.msie && $.browser.version.substr(0,1)<7) {
                        $('.carousel-prev').html('');
                       $('.carousel-next').html('');
                        if(color == "black" ){
                            $('.carousel-prev').append('<span class="icon icon-prev black"></span>');
                            $('.carousel-next').append('<span class="icon icon-next black"></span>');
                        }else{
                            $('.carousel-prev').append('<span class="icon icon-prev"></span>');
                            $('.carousel-next').append('<span class="icon icon-next"></span>');

                        }
                    }
                } else if(thiscarousel.opts.template == 'quisommesnous') {
                    thiscarousel.centerNav($('.carousel-view-text',thiscarousel.$$));
                }
            }
            if(thiscarousel.opts.pager) {
                $("li.carousel-page-"+thiscarousel.slideCurrent, nav).addClass("carousel-page-active")
                    .siblings().removeClass("carousel-page-active");
            }
        },
        resizeCarousel: function() { 
            if(thiscarousel.opts.template=='home' || thiscarousel.opts.template=='quisommesnous') {
                //thiscarousel.slideWidth = ($(window).width()<975) ? 975 : $(window).width();
				//@modified for img width not to full width
				thiscarousel.slideWidth = thiscarousel.$$.parent().width();
                thiscarousel.slideHeight = Math.ceil((thiscarousel.slideWidth*600)/1400);
                if(thiscarousel.opts.template=='quisommesnous' && thiscarousel.slideHeight < 540){
                    thiscarousel.slideHeight = 540;
                }
            } else {
                thiscarousel.slideWidth = thiscarousel.$$.parent().width();
                thiscarousel.slideHeight = thiscarousel.slideWidth;
            }
            thiscarousel.leftPosition = -(thiscarousel.slideCurrent-1)*thiscarousel.slideWidth;
            thiscarousel.leftPositionMax = (thiscarousel.elCount > 1) ? (thiscarousel.slideCount-1) * (-thiscarousel.slideWidth) : 0;
            thiscarousel.leftPositionCloned = (thiscarousel.elCount > 1) ? (thiscarousel.slideCount) * (-thiscarousel.slideWidth) : 0;
            $(thiscarousel.$$).css({
                "width": thiscarousel.slideWidth,
                "height": thiscarousel.slideHeight
            });
            $(".carousel-view > ul",thiscarousel.$$).css({
                "width": (thiscarousel.slideCount+1) * thiscarousel.slideWidth,
                "left": thiscarousel.leftPosition
            });
            $(".carousel-view > ul > li",thiscarousel.$$).css({
                "width": thiscarousel.slideWidth,
                "height": thiscarousel.slideHeight,
                "overflow": "hidden"
            });
            $(".carousel-view li img.picture",thiscarousel.$$).attr('width',thiscarousel.slideWidth).attr('height',thiscarousel.slideHeight);
            if(thiscarousel.opts.template=='home') {
                //thiscarousel.centerNav($('.carousel-nav',thiscarousel.$$)); 
            }
        }
    }

    $.fn.carousel = function(options) {  
        return this.each(function(){
            Carousel.init(this, options);
        });
    }
    
    /* Base */   
    Script = {

        init: function() {
            script = this;
            script.buildBinds();
            script.buildFooter();
            script.placeholder();
            //$('.carousel-home').carousel({template:'home'});
            //$('.carousel-quisommesnous').carousel({template:'quisommesnous'});
            if($('.push-list-home').length) {
                script.resizeHome();
            } else {
                script.resizeMedia();
            }

   			/*$('img').each(function(){
				$(this).hide().load(function(){
					$(this).show();
				});
			});*/  
        },
        buildBinds: function() {
            $(window).resize(function(){

                windowWidth = $(window).width();
                if($('.push-list-home').length) {
                    script.resizeHome(); 
                } else {
                    script.resizeMedia();
                }
            });

            // Go to top
            $('.icon-gototop').live('click', function(){
                $('html,body').animate({
                    scrollTop: 0
                }, 'normal');
                return false;
            });
            
            // Qui sommes nous anchors
            $('.theme-nav a').live('click', function(){
                $('html,body').animate({
                    scrollTop: $('#'+$(this).attr('href').substring(1, 20)).offset().top
                }, 'normal');
                return false;
            });

			// Mot de passe oublié
			$('#forget-password').live('click', function(e){
				e.preventDefault();
				$('.box-member-1.connect').fadeOut('fast', function(){
					$('.box-member-1.password').fadeIn('fast');
				});
			});
			
			$('#back-to-connect').live('click', function(e){
				e.preventDefault();
				$('.box-member-1.password').fadeOut('fast', function(){
					$('.box-member-1.connect').fadeIn('fast');
				});
			});
			
			$('#box-member .box-member-success a.btn').live('click', function(e){
				e.preventDefault();
				$('#box-member .box-member-success').fadeOut('fast', function(){
					$('#box-member .box-member-content').fadeIn('fast');
				});
			});
            

            // Open LOGIN/REGISTER
            $('a.nav-member, a.sitemap-member').live('click', function(e){
				e.preventDefault();
                if($('#box-member:visible').length) {
                    $('#box-member').fadeOut('fast');
                } else {
                    $('#box-member').fadeIn('fast');
                }
            });
            $('#box-member .icon-billet-close').live('click', function(){
                $('#box-member').fadeOut('fast');
            });
            
            // Open detail
            /*$('.push-list-media .push').live('click', function(){ 
                $(this).addClass('push-active').siblings().removeClass('push-active');
                windowWidth = $(window).width();
                nbByLine = (windowWidth>=2145) ? 10 : (windowWidth>=1755) ? 8 : (windowWidth>=1365) ? 6 : 4;
                index = ($(this).prevAll().length) - (($(this).prevAll().length) % nbByLine) + nbByLine -1;
                if($('.push').eq(index).length != 1) {
                    index = $('.push:last').prevAll().length;
                }
                pushDetail = ($(this).hasClass('push-video')) ? $('.push-detail-video-clone').clone() : $('.push-detail-article-clone').clone();
                pushDetail.insertAfter($('.push').eq(index)).slideDown('fast');
                
                if($(this).closest('.push-list-media-project').length) {
                    $('.push', $(this).closest('.push-list-media-project')).slideUp();
                } else {
                    $('html,body').animate({
                        scrollTop: pushDetail.offset().top
                    }, 'fast');
                }
                $('.carousel-push', pushDetail).carousel();
               
                return false;
            });

			// Close detail
            $('.push-list-media .push-detail .icon-close').live('click', function(){ 
                pushTop = $('.push-active').removeClass('push-active').offset().top;
                $(this).closest('.push-detail').slideUp('fast', function(){ 
                    $(this).remove(); 
                });
                if($(this).closest('.push-list-media-project').length) {
                    $('.push', $(this).closest('.push-list-media-project')).slideDown();
                }
                $('html,body').animate({
                    scrollTop: pushTop
                }, 'fast');

                return false;
            });*/

			$('.push-detail-text-pager a.pager-item').live('click', function(e) {
                e.preventDefault();
                if(!$(this).hasClass('active')) {
                    id = $(this).attr('rel');
                    var parent = $(this).closest('.push-detail-text');
                    // var parent = $('.push-detail-text');

                    pageOld = $('.push-detail-text-page:visible', parent);
                    pageNew = $('.push-detail-text-page:eq('+id+')', parent);
                    pageOld.fadeOut('fast', function(){ 
                        pageNew.fadeIn('fast');
                    })
                    $(this).addClass('active').siblings().removeClass('active');
                }
                return false;
            });
            // pager
            $('.pager a').live('click', function() { 
                if(!$(this).hasClass('active')) {
                    id = $(this).attr('rel'); 
                    papa = $(this).closest('.page-wrapper');
                    pageOld = $('.page-content:visible', papa);
                    pageNew = $('.page-content:eq('+id+')', papa);
                    pageOld.fadeOut('fast', function(){ 
                        pageNew.fadeIn('fast');
                    })
                    $(this).addClass('active').siblings().removeClass('active');
                }
                return false;
            });            
            
            
            if ($.browser.msie && $.browser.version.substr(0,1)<7) {
                $('.push').live({
                    mouseenter: function(){
                        $('body').css('cursor', 'pointer');
                        $(this).addClass('push-hover');
                    }, mouseleave: function(){
                        $('body').css('cursor', 'auto');
                        $(this).removeClass('push-hover');
                    }
                });
            }

            
            // Play video
            $('.trigger-player').live('click', function(){
                playerBtn = $(this);
                $('<iframe/>', {
                     "width": "100%",
                     "height": "100%", 
                     "src": playerBtn.attr('href'),
                     "frameborder": "0"
                }).insertAfter(playerBtn).fadeIn("fast", function(){ playerBtn.remove(); });
                return false;
            });
            
            // open category
            if($.browser.msie && $.browser.version.substr(0,1)<7){
                 $('a.project-category').live('click', function(){
                if(!$(this).next().find('a.project').length) return false;
                if($(this).next().is('.project-list:visible')) {
                    $(this).next().hide();
                    $('.icon-expand', this).removeClass('icon-close');
                } else {
                    $(this).next().show();
                    $('.icon-expand', this).addClass('icon-close');
                }
                script.resizeMedia()
                return false;
            });
            }else{
            $('a.project-category').live('click', function(){
				if(!$(this).next().find('a.project').length) return false;
                if($(this).next().is('.project-list:visible')) {
                    $(this).next().slideUp('fast',function() {
                        script.resizeMedia()
                    });
                    $('.icon-expand', this).removeClass('icon-close');
                } else {
                    $(this).next().slideDown('fast',function() {
                        script.resizeMedia()
                    });
                    $('.icon-expand', this).addClass('icon-close');
                }
                return false;
            });
            }
			
            // open medias
            /*$('.project').live('click', function(){
                if($(this).next().is('.project-inner:visible')) {
                    $(this).next().slideUp('fast');
                } else {
                    projectInner = $('.project-inner-clone').clone();
                    $(this).next().html(projectInner.html()).slideDown('fast');
                }
                return false;
            });  */
            // hover helps

            $('.help').live({
                'mouseenter': function() {
                    //$('.picture',this).animate({top: -200});
                    $('.help-hover', this).stop().animate({top: 0}, 200);
                }, 'mouseleave': function() {
                    //$('.picture',this).animate({top: 0});
                    $('.help-hover', this).stop().animate({top: 200}, 200);
                }
            });

			$('.help-hover').live('click', function(){
				if($('.help-link', this).length){
					document.location = $('.help-link', this).attr('href');
				}
			});
            
            // open terms
			var heightTerms = $('.project-terms-inner').height();
            $('.project-terms').live('click', function(){
                /*if($('.aside-opened').length==0) {
                    var original = $(this).closest('.aside').css('opacity',0);
                    var copy = original.clone().addClass('aside-opened')
                                    .css('opacity',1)
                                    .insertBefore(original)
                                    .slideDown('fast');
                } else {
                    $('.project-terms-inner .icon-close').click();
                }*/
				$('.project-terms-inner').mySlideToggle();
                return false;
            });
            // close terms
            $('.project-terms-inner .icon-close').live('click', function(){
                /*$('.aside').css('opacity',1);
                $('.aside-opened').slideUp('fast', function(){ $(this).remove(); });*/
				$('.project-terms-inner').mySlideToggle();
                return false;
            });

            //open billet form
            $('.trigger-billet-form').live('click', function() {  
                $('.billet-form', $(this).closest('.project-billet'))
                    .fadeTo(1,0).css('display','block')
                    .animate({
                        'right': 0,
                        'opacity': 1
                }, 'fast', function(){
                    //$(this).height($(this).closest('.project-billet').height());
					if($('.reservation', $(this)).height() > $(this).closest('.project-billet').height()){
						$(this).closest('.project-billet').height($('.reservation', $(this)).height());
						$(this).height($('.reservation', $(this)).height());
					}else{
						$(this).height($(this).closest('.project-billet').height());
					}
                });
                return false;
            });
            
            $('.icon-billet-close').live('click', function() {  
                $(this).closest('.billet-form')
                    .animate({
                        'right': '-200',
                        'opacity': 0
                }, 'fast', function(){ $(this).css('display','none'); $(this).closest('.project-billet').css('height', 'auto'); });
                return false;
            });

			//add more field
			$('a.add-more-file').live('click', function(e){
				e.preventDefault();
				var formfile = $(this).closest('div.file-attachments');
				var formfileclone = formfile.clone();
				$('input', formfileclone)
					.attr('id', $('input', formfileclone).attr('id') + '-' + ($('div.file-attachments').length + 1))
				;
				$('.file-attachments:last a.add-more-file').remove();
				$('.file-attachments:last').after(formfileclone);
			});
			
			$('a.add-more-field').live('click', function(e){
 				e.preventDefault();
 				var formgroup = $(this).closest('div.form-group');
				var relToAddMore = $(this).closest('div.form-group-1').attr('rel');
 				$('div.form-group-1', formgroup).each(function(){
					if($(this).attr('rel') == relToAddMore){
						var last = $('div.form-item', this).length - 1;
						var clone = $('div.form-item:eq(' + last + ')', this).clone();
						$('input', clone).val('');
						$(this).append(clone);
						$('div.form-item:eq(' + last + ') a.add-more-field', this).remove();
					}
 				});
 			});
			
			
			// #submit-project-form-edit-btn
			$('#submit-project-form-edit-btn').live('click', function(e){
				e.preventDefault();
				$('#submit-project-review').hide();
				$('#submit-project-form-edit').show();
			});
			
			$('a.delete-file-icon').live('click', function(e){
				e.preventDefault();
				$(this).closest('span').remove();
			});
			
			// Billeterie
			$('.project-billetterie-list .project').live('click', function(e){
				e.preventDefault();
				$(this).next().mySlideToggle();
			});

        },
        buildFooter: function(){
            

            $('#footer-section').css('margin-bottom',$('#player').height()+'px');

			var isClosed = true;
			$("#player-expand").live('click', function(e){
				var userAgent = navigator.userAgent.toLowerCase();
				var ff = false;
				if (/mozilla/.test (userAgent) && !/(compatible|webkit)/.test(userAgent)) { ff = true; }
				isClosed = !isClosed;
				if(isClosed){
					if(ff) $("#player").height(29); else $("#player").animate({height:29}, 500);
				}else{
					if(ff) $("#player").height(380); else $("#player").animate({height:380}, 500);
				}
				return false;
			});
        },
        resizeHome: function() {
            windowWidth = $(window).width();
            $('.push').removeClass('push-2').removeClass('push-4').show();
            if(windowWidth >= 2145) {
                gutter = Math.ceil((windowWidth-2145)/11);
                $('.push:lt(9)').addClass('push-4').css('left',0)
                    .css('width', 390+(gutter*2)).css('height', 390+(gutter*2))
                    .find('img').attr('width', 390+(gutter*2)).attr('height', 390+(gutter*2));
                $('.push:eq(1), .push:eq(4), .push:eq(7)').css('left',390+(gutter*2));    
                $('.push:eq(2), .push:eq(5), .push:eq(8)').css('left',780+(gutter*4));
                $('.push:gt(8)').addClass('push-2').css('left',1170+(gutter*6))
                    .css('width', 390+(gutter*2)).css('height', 195+(gutter*1))
                    .find('img').attr('width', 195+(gutter*2)).attr('height', 195+(gutter*2));   
                $('.push:eq(10), .push:eq(12), .push:eq(14), .push:eq(16), .push:eq(18), .push:eq(20)').css('left',1560+(gutter*8));
                $('.push:eq(0), .push:eq(1), .push:eq(2), .push:eq(9), .push:eq(10)').css('top',0);
                $('.push:eq(3), .push:eq(4), .push:eq(5), .push:eq(13), .push:eq(14)').css('top',390+(gutter*2));
                $('.push:eq(6), .push:eq(7), .push:eq(8), .push:eq(17), .push:eq(18)').css('top',780+(gutter*4));
                $('.push:eq(11), .push:eq(12)').css('top',195+(gutter*1));
                $('.push:eq(15), .push:eq(16)').css('top',585+(gutter*3));
                $('.push:eq(19), .push:eq(20)').css('top',975+(gutter*5));
                $('.tweet-list').css('left',1950+(gutter*10))
                    .find('.tweet').css('padding', Math.ceil(20+(gutter/2))+'px');
                $('.push div').css('padding', Math.ceil(17+(gutter/2))+'px '+ Math.ceil(20+(gutter/2))+'px');
                $('.push-list-home').css('height',Math.ceil(1170+(gutter*6)));
                $('.push:gt(20)').hide();
            } else if (windowWidth >= 1755) {
                gutter = Math.ceil((windowWidth-1755)/9);
                $('.push:lt(6)').addClass('push-4').css('left',0)
                    .css('width', 390+(gutter*2)).css('height', 390+(gutter*2))
                    .find('img').attr('width', 390+(gutter*2)).attr('height', 390+(gutter*2));
                $('.push:eq(1), .push:eq(3), .push:eq(5)').css('left',390+(gutter*2));
                $('.push:gt(5)').addClass('push-2').css('left',780+(gutter*4))
                    .css('width', 390+(gutter*2)).css('height', 195+(gutter*1))
                    .find('img').attr('width', 195+(gutter*1)).attr('height', 195+(gutter*1));
                $('.push:eq(7), .push:eq(9), .push:eq(11), .push:eq(13), .push:eq(15), .push:eq(17)').css('left',1170+(gutter*6))
                $('.push:eq(0), .push:eq(1), .push:eq(6), .push:eq(7)').css('top',0);
                $('.push:eq(2), .push:eq(3), .push:eq(10), .push:eq(11)').css('top',390+(gutter*2));
                $('.push:eq(4), .push:eq(5), .push:eq(14), .push:eq(15)').css('top',780+(gutter*4));
                $('.push:eq(8), .push:eq(9)').css('top',195+(gutter*1));
                $('.push:eq(12), .push:eq(13)').css('top',585+(gutter*3));
                $('.push:eq(16), .push:eq(17)').css('top',975+(gutter*5));
                $('.tweet-list').css('left',1560+(gutter*8))
                    .find('.tweet').css('padding', Math.ceil(20+(gutter/2))+'px');
                $('.push div').css('padding', Math.ceil(17+(gutter/2))+'px '+ Math.ceil(20+(gutter/2))+'px');
                $('.push-list-home').css('height',Math.ceil(1170+(gutter*6)));
                $('.push:gt(17)').hide();
            } else if (windowWidth >= 1365) {
                gutter = Math.ceil((windowWidth-1365)/7);
                $('.push:lt(3)').addClass('push-4').css('left',0)
                    .css('width', 390+(gutter*2)).css('height', 390+(gutter*2))
                    .find('img').attr('width', 390+(gutter*2)).attr('height', 390+(gutter*2));
                $('.push:gt(2)').addClass('push-2').css('left',390+(gutter*2))
                    .css('width', 390+(gutter*2)).css('height', 195+(gutter*1))
                    .find('img').attr('width', 195+(gutter*1)).attr('height', 195+(gutter*1));
                $('.push:eq(4),.push:eq(6),.push:eq(8),.push:eq(10),.push:eq(12),.push:eq(14)').css('left',780+(gutter*4))
                $('.push:eq(0), .push:eq(3), .push:eq(4)').css('top',0);
                $('.push:eq(1), .push:eq(7), .push:eq(8)').css('top',390+(gutter*2));
                $('.push:eq(2), .push:eq(11), .push:eq(12)').css('top',780+(gutter*4));
                $('.push:eq(5), .push:eq(6)').css('top',195+(gutter*1));
                $('.push:eq(9), .push:eq(10)').css('top',585+(gutter*3));
                $('.push:eq(13), .push:eq(14)').css('top',975+(gutter*5));
                $('.tweet-list').css('left',1170+(gutter*6))    
                    .find('.tweet').css('padding', Math.ceil(20+(gutter/2))+'px');
                $('.push div').css('padding', Math.ceil(17+(gutter/2))+'px '+ Math.ceil(20+(gutter/2))+'px');
                $('.push-list-home').css('height',Math.ceil(1170+(gutter*6)));
                $('.push:gt(14)').hide();
            } else { // >= 975
                gutter = (windowWidth>975) ? Math.ceil((windowWidth-975)/5) : 0;
                $('.push:lt(3)').addClass('push-4').css('left',0)
                    .css('width', 390+(gutter*2)).css('height', 390+(gutter*2))
                    .find('img').attr('width', 390+(gutter*2)).attr('height', 390+(gutter*2));
                $('.push:gt(2)').addClass('push-2').css('left',390+(gutter*2))
                    .css('width', 390+(gutter*2)).css('height', 195+(gutter*1))
                    .find('img').attr('width', 195+(gutter*1)).attr('height', 195+(gutter*1));
                $('.push:eq(0), .push:eq(3)').css('top',0);
                $('.push:eq(1), .push:eq(5)').css('top',390+(gutter*2));
                $('.push:eq(2), .push:eq(7)').css('top',780+(gutter*4));
                $('.push:eq(4)').css('top',195+(gutter*1));
                $('.push:eq(6)').css('top',585+(gutter*3));
                $('.push:eq(8)').css('top',975+(gutter*5));
                $('.tweet-list').css('left',780+(gutter*4))
                    .find('.tweet').css('padding', Math.ceil(20+(gutter/2))+'px');
                $('.push div').css('padding', Math.ceil(17+(gutter/2))+'px '+ Math.ceil(20+(gutter/2))+'px');
                $('.push-list-home').css('height',Math.ceil(1170+(gutter*6)));
                $('.push:gt(8)').hide();
                if(windowWidth<975) {
                    $('body').css('width','975');
                    $('html').css('overflow-x', 'auto');
                } else {
                    $('body').css('width','auto');
                    $('html').css('overflow-x', 'hidden');
                }
            }
            $('.by-uzik').width(195+gutter);
            //$('.push-list-home').css('height', parseInt($('.push-list-home').height()) - 100).css('padding-bottom', 100);
            //Calcul de la nav du carrousel
            //var marginTop = $('.carousel-home .carousel-nav ').height()/2;
            //$('.carousel-home .carousel-nav ').css('margin-top', '-'+marginTop+'px');
            //DD_Belated refresh
            /*if ($.browser.msie && $.browser.version.substr(0,1)<7){
                //DD_belatedPNG.fix('.icon, .ui-icon, .icon-player-big, l.associated-list a');
                //DD_belatedPNG.fix('.icon, .ui-icon, .icon-player-big, .associated-list a');
                DD_belatedPNG.fix('.icon');
                //DD_belatedPNG.fix('.icon-player-big');
                //DD_belatedPNG.fix('ul.associated-list a');
                //DD_belatedPNG.fix('img');
            }*/

			var freeSpaceTweet = $('.tweet').eq(0).height()
				- $('.tweet').eq(0).find('.tweet-date').outerHeight(true)
				- $('.tweet').eq(0).find('.tweet-link').outerHeight(true)
				- parseInt($('.tweet').eq(0).find('.tweet-message').css('margin-bottom'))
				- parseInt($('.tweet').eq(0).find('.tweet-message').css('margin-top'))
			;
			$('.tweet-message').height(freeSpaceTweet - freeSpaceTweet % parseInt($('.tweet').eq(0).find('.tweet-message').css('line-height')) + 15);
			//alert(freeSpaceTweet - freeSpaceTweet % parseInt($('.tweet').eq(0).find('.tweet-message').css('line-height')));

        },     
        resizeMedia: function() { 

            windowWidth = $(window).width();

            if(windowWidth >= 2145) {
                gutter = Math.ceil((windowWidth-2145)/11);
                pushListWidth = ($('.aside').length) ? windowWidth-(195+gutter)+11 : windowWidth+11;
                $('#section').css('width',windowWidth+12);
                $(".project-category .icon-expand").css('right','12px');
            } else if (windowWidth >= 1755) {
                gutter = Math.ceil((windowWidth-1755)/9);
                pushListWidth = ($('.aside').length) ? windowWidth-(195+gutter)+8 : windowWidth+8;
                $('#section').css('width',windowWidth+9);
                $(".project-category .icon-expand").css('right','9px');
            } else if (windowWidth >= 1365) {
                gutter = Math.ceil((windowWidth-1365)/7);
                pushListWidth = ($('.aside').length) ? windowWidth-(195+gutter)+6 : windowWidth+6;
                $('#section').css('width',windowWidth+7);
                $(".project-category .icon-expand").css('right','4px');
            } else { // >= 975

                gutter = (windowWidth>975) ? Math.ceil((windowWidth-975)/5) : 0;
                if($('.aside').length) {
                    pushListWidth = (windowWidth>975) ? windowWidth-(195+gutter)+4 : 975-195 ;
                } else {
                    pushListWidth = (windowWidth>975) ? windowWidth+4 : 975 ;
                }  
                if(windowWidth<975) {
                    $('body, #section').css('width','975');
                    $('html').css('overflow-x', 'auto');
                } else {
                    $('body').css('width','auto');
                    $('#section').css('width',windowWidth+5);
                    $('html').css('overflow-x', 'hidden');
                }    

            }
           
            //fix IE6 for submit projet form
            if ($.browser.msie && $.browser.version.substr(0,1)<7 && ($('.project-submit-form').length || $('.project-confirm-text').length)) {
                $('#section').css('width',$('#section').width()+15);
            }
            
           
            // list
            $('.push-list-media').css('width',pushListWidth);


             if(!($.browser.msie && parseInt($.browser.version, 10) <= 7)){ $('.push').css('width', 195+(gutter*1)).css('height', 390+(gutter*2))
                      .find('img').attr('width', 195+(gutter*1)).attr('height', 195+(gutter*1));
                    $('.push div').css('padding', Math.ceil(17+(gutter/2))+'px '+ Math.ceil(20+(gutter/2))+'px '+ Math.ceil(17+(gutter/2))+'px '+ Math.floor(20+(gutter/2))+'px');
              }else{
                $('.push').css('width', 195).css('height', 390)
                      .find('img').attr('width', 195).attr('height', 195);
                      //$('.push div').css('padding', '17px 20px');
              }
            
            $('.tweet').css('padding', Math.ceil(20+(gutter/2))+'px');

            $('.aside .filter').css('padding', Math.ceil(20+(gutter/2))+'px '+Math.ceil(20+(gutter/2))+'px '
                                                    + Math.ceil(20+(gutter*3/2))+'px '+Math.ceil(20+(gutter/2))+'px');

			
			/* -2 for IE 6 */
            if(!($.browser.msie && parseInt($.browser.version, 10) <= 7)) $('.aside .project-submit, .aside .project-terms').css('padding', (Math.ceil(20+(gutter/2)))+'px');                             
            $('.help').css('padding', Math.ceil(40+gutter)+'px');
             
            /*$('.push-5').css('width', 975+(gutter*5)).css('height', 585+(gutter*3))
                      .find('img').attr('width', 585+(gutter*3)).attr('height', 585+(gutter*3));*/

            $('.push-5').css('width', 975+(gutter*5));

            $('.push-5 .push-inner').css('padding', 40+Math.ceil(gutter*3/2)+'px '+Math.ceil(40+gutter)+'px');     

            $('.push-gouvernance .gouvernance-organigramme, .push-gouvernance .heading-2').css('width', 465+Math.ceil(gutter*3)+'px');
            $('.carousel-view-left-inner').css('width',  $('.push-5').width() - ($('.push-5 div.push-inner').width() + (Math.ceil(40+gutter))*2 ));

            $('.carousel-view-left-inner').each(function(){
                $(this).css('height',  $(this).next('.push-5 div.push-inner').height() + (40+Math.ceil(gutter*3/2))*2 );
                $(this).parent().css('height',  $(this).next('.push-5 div.push-inner').height() + (40+Math.ceil(gutter*3/2))*2 );
            });
            //$('.carousel-view-left-inner').css('height',  $('.push-5 div.push-inner').height() + (40+Math.ceil(gutter*3/2))*2 );
            //$('.carousel-view-left-inner img').attr('width', $('.carousel-view-left-inner').height()).attr('height', $('.carousel-view-left-inner').height());
            $('.carousel-view-left-inner img.picture').each(function(){
                $(this).attr('width', $(this).parent().height()).attr('height', $(this).parent().height());
                $(this).css('margin-left', ($(this).parent().width() - $(this).width())/2);
            });

			// -5 hack for IE6
            if(!($.browser.msie && parseInt($.browser.version, 10) <= 7)) $('.aside-illus img').attr('width', 195+(gutter*1)).attr('height', 195+(gutter*1));
            $('.project-billet').width(680+gutter*4);
            $('.project-billet-1, .project-billet-2').width(315+gutter*2);
            $('.billet-form').width(290+gutter*2);//.height($('.project-billet').height()-100);
            //footer
            $('.cell').css('padding', Math.ceil(20+(gutter/2))+'px');
            $('.cell-2').css('padding', Math.ceil(40+(gutter))+'px');
			$('img').load(function() {
				$('.cell img, .cell-2 img').each(function(){
					if($(this).closest('p').hasClass('resize')){
						$(this).width($(this).closest('p').width());
					}
				});
			});
  
            // detail
            if(windowWidth<1365) {
                detailVideoTextWidth = 545;
                detailVideoTextPadding = Math.ceil(20+(gutter*3/2));
                detailVideoIllusMarginBottom = -40;
                detailArticleIllusWidth = 390+(gutter*2);
                descriptionFirstWidth = 350;
                descriptionFirstPadding = gutter;
                descriptionSecondPadding = gutter;
                termsFirstWidth = 310; termsFirstPadding = 40 + gutter;
                termsSecondWidth = 270; termsSecondPadding = 40 + gutter;
                formGroupOne = 290 + gutter*2;
                formGroupTwo = 680 + gutter*4;
                formDescriptionOne = 290 + gutter*2;
                formDescriptionTwo = 290 + gutter*2;
                confirmOne = 290 + gutter*2;
                confirmTwo = 390 + gutter*2;
                pHeaderFirstWidth = 310;
                pHeaderSecondWidth = 270;
                pHeaderPadding = 20 + gutter;
            } else {
                detailVideoTextWidth = 350;
                detailVideoTextPadding = Math.ceil(20+(gutter));
                detailVideoIllusMarginBottom = 0;
                detailArticleIllusWidth = 585+(gutter*3);
                descriptionFirstWidth = 545;
                descriptionFirstPadding = gutter*3/2;
                descriptionSecondPadding = gutter;
                termsFirstWidth = 485; termsFirstPadding = 50 + gutter*3/2;
                termsSecondWidth = 445; termsSecondPadding = 50 + gutter*3/2;
                formGroupOne = 485 ;
                formGroupTwo = 1070 ;
                formDescriptionOne = 485;
                formDescriptionTwo = 290;
                confirmOne = 485 + gutter*3;
                confirmTwo = 585 + gutter*3;
                pHeaderFirstWidth = 432;
                pHeaderSecondWidth = 392;
                pHeaderPadding = 50;
            }
            
            $('.push-detail-video .push-detail-illus').css('width', 585+(gutter*3)).css('height', 345+(gutter*1.7))
                                                      .css('margin-bottom',detailVideoIllusMarginBottom)
                                                      .find('img').attr('width', 585+(gutter*3)).attr('height', 345+(gutter*1.7));
            $('.push-detail-video .push-detail-text').css('width',detailVideoTextWidth)
                                                     .css('padding', detailVideoTextPadding+'px');

                   
           
            if (!($.browser.msie && $.browser.version.substr(0,1)<7)) {
                $('.push-detail-article .push-detail-text, .push-detail-portrait .push-detail-text, .push-detail-audio .push-detail-text').css('padding', Math.ceil(20+gutter)+'px');
                            $('.push-detail-article .push-detail-illus, .push-detail-portrait .push-detail-illus, .push-detail-audio .push-detail-illus').css('width', detailArticleIllusWidth)
                                                        .css('height', detailArticleIllusWidth)
                                                        .find('img').attr('width', detailArticleIllusWidth)
                                                                    .attr('height', detailArticleIllusWidth);  
            }else{
                detailArticleIllusWidth = detailArticleIllusWidth-20;
             $('.push-detail-article .push-detail-illus, .push-detail-portrait .push-detail-illus, .push-detail-audio .push-detail-illus').css('width', detailArticleIllusWidth)
                                                        .css('height', detailArticleIllusWidth)
                                                        .find('img').attr('width', detailArticleIllusWidth)
                                                                    .attr('height', detailArticleIllusWidth);  
            }
            //$('.push-detail-text.margin').css('padding-top', 0);

			$('.push-detail-audio .push-detail-text-nopadding').width($('.push-detail-audio .push-detail-text').outerWidth());
            if($('.form-description-1').length && windowWidth>1365) {
                $('.project-description').css('background-position', formDescriptionOne + 100 +'px 0')
            } else {
                $('.project-description').css('background-position', descriptionFirstWidth + (20 + descriptionFirstPadding)*2 +'px 0')
            }
            
            $('.project-description-1').css('padding', 20 + descriptionFirstPadding +'px')
                                       .css('width', descriptionFirstWidth);
            $('.project-description-2').css('padding', 20 + descriptionSecondPadding +'px');
            $('.project-terms-inner').css('background-position', termsFirstWidth + termsFirstPadding*2 +'px 0')
            $('.project-terms-1').css('padding', termsFirstPadding +'px')
                                 .css('width', termsFirstWidth);
            $('.project-terms-2').css('padding', termsSecondPadding +'px')
                                 .css('width', termsSecondWidth);
            
            $('.project-header-inner').css('background-position', pHeaderFirstWidth + pHeaderPadding*2 +'px 0')
            $('.project-header-1').css('padding', '10px '+pHeaderPadding*2 +'px 10px 0')
                                 .css('width', pHeaderFirstWidth);
            $('.project-header-2').css('padding', '10px '+pHeaderPadding +'px')
                                 .css('width', pHeaderSecondWidth);


			$('.project-inner .aside').css('overflow', 'hidden').css('height', $('.project-inner .project-description').height());
			if(!($.browser.msie && parseInt($.browser.version, 10) <= 7)){
				$('.project-inner .aside img.picture').attr('width', 195+(gutter*1)).attr('height', (195+(gutter*1))*3);
			}else{
				$('.project-inner .aside img.picture').attr('width', 195).attr('height', 3*195);
			}
			
			/*$('.project-terms-inner')
				.width($('.project-terms-inner').width() + $('.aside').width())
				.css('left', - $('.aside').width())
				.css('padding-left', $('.aside').width())
			;*/
            if (!($.browser.msie && $.browser.version.substr(0,1)<7)) {
                $('.form-group-1').css('width', formGroupOne);    
            }else{
                $('.form-group-1').css('width', formGroupOne-20);  
                $('.width-medium').css('left', '0').css('width', formGroupOne/2);
                $('.form-group-1 .form-text input').css('display','block');

                //$('.form-group-2').css('width', 370);    
                //$('.form-group-1').css('width', formGroupOne-2);       
			}
            if(formGroupOne <= 350) $('.form-group-1.hack-margin').css('margin-top', '0'); else $('.form-group-1.hack-margin').css('margin-top', '-18px');
            $('.form-group-2').css('width', formGroupTwo);
            $('.form-description-1').css('width', formDescriptionOne);
            $('.form-description-2').css('width', formDescriptionTwo);
            $('.project-confirm-text-inner').css('width', confirmOne);
            $('.project-confirm-illus').attr('width', confirmTwo).attr('height', confirmTwo);
            $('.push-more, .push-more-prev, .push-no-more').css('width', 195+gutter).css('padding', Math.floor(90+gutter/2)+'px 0');
            
            //footer
            $('.footer-section-inner .col').css('height', 'auto');
            footerSectionHeight = $('.footer-section-inner').height() - $('.footer-section-inner .cell-bottom').outerHeight();
            footerSectionSpace = $(window).height()-$('#header').outerHeight()-$('#footer').outerHeight()-$('#player').outerHeight();
            footerSectionHeight = (footerSectionHeight<footerSectionSpace) ? footerSectionSpace : footerSectionHeight; 
            $('.by-uzik').width(195+gutter);
             
            if($('.footer-section-inner .cell-2').length && windowWidth<1365) {
                //$('.footer-section-inner').css('width', Math.ceil(40*4+2*310+(gutter*4)));
                $('.footer-section-inner').css('width', '975px');
                $('.footer-section-inner .cell-2').css('padding', '40px');
                $('.footer-section-inner .cell-2').css('width', '244px');
                if(windowWidth<= 975)$('.icon-gototop').css('right', 0);
                else $('.icon-gototop').css('right', '-40px');
                $('.footer-section-inner .col').css('height', $('.footer-section-inner').height());
            } else {
                if(windowWidth>1356){
                    $('.footer-section-inner').css('width', 'auto');
                    $('.footer-section-inner .cell').css('width', '154px');
                }else{
                    //$('.footer-section-inner').css('width', 1356);
                    $('.footer-section-inner .cell').css('padding', '21px');
                    $('.footer-section-inner .cell').css('width', '119px');
                    if(windowWidth<= 975)$('.icon-gototop').css('right', 0);
                    else $('.icon-gototop').css('right', '-40px');
                }
                $('.footer-section-inner .col').css('height', footerSectionHeight);
                
            }



            if($(document).height() <= $(window).height()  ){
                $('.icon-gototop').css('display','none');
            }else{
                $('.icon-gototop').css('display','block');
            }

            if(windowWidth > 1365  ){
                //$('.icon-gototop').css('right', '-40px');
            }else{
                $('.icon-gototop').css('right', '0');
            }
			script.resizePush();

            //Date Picker
            $('.form-datepicker input').datepicker();

           /* if ($.browser.msie && $.browser.version.substr(0,1)<7){
                /*DD_belatedPNG.fix('.icon');
                DD_belatedPNG.fix('.ui-icon');
                DD_belatedPNG.fix('.icon-player-big');
                DD_belatedPNG.fix('ul.associated-list a');
                //DD_belatedPNG.fix('img');
            } */
            $("#loaderFilter").hide();


        },
		resizePush: function(push){
			/*$('.push-detail').each(function(){
				var margin = $('.push-detail-illus', $(this)).width();
				$('.push-detail-text.margin', $(this)).each(function(){
					if($(this).position().left != margin){
						$(this).css('margin-left', margin + 'px');
					}
				});
			});*/
		}
        ,
        placeholder: function(){
            _placeholder();
        }
    }
    
    Script.init();
});

/*function ie6resize(padding){
    $('.push div').css('*padding', '17px 20px 0 20px');
    $('.push div').css('*height', (178 + padding)+'px');
}*/

function _placeholder(){
	//Placeholder
    var i = document.createElement("input");
    // Only bind if placeholder isn't natively supported by the browser
    if (!("placeholder" in i)) {
        $("input[placeholder]").each(function () {
            var self = $(this);
            self.val(self.attr("placeholder")).bind({
                focus: function () {
                    if (self.val() === self.attr("placeholder")) {
                        self.val("");
                    }
                },
                blur: function () {
                    var label = self.attr("placeholder");
                    if (label && self.val() === "") {
                        self.val(label);
                    }
                }
            });
        });
    }
}

_placeholder();
$(document).ready(function(){ 
    $.fn.mySlideToggle = function(){
    if($.browser.msie && $.browser.version.substr(0,1)<7){
        if($(this).css('display') === 'none'){
            $(this).show();
            $(this).css('overflow','visible');
        }else{
            $(this).hide();
        }
    }else{
        $(this).stop().slideToggle();
    }
}
}); 





