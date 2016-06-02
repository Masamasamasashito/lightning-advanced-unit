jQuery(document).ready(function(){
	run_slide_menu_control();
});
jQuery(window).resize(function(){
	run_menuResize();
});
/*-------------------------------------------*/
/*	メニューの開閉
/*	<div id="menu" onclick="showHide('menu');" class="itemOpen">MENU</div>
/*-------------------------------------------*/
var menu_width = '360px';

function run_slide_menu_control(){
	jQuery('.menuBtn').prependTo('#bodyInner');
	jQuery('.menuBtn').each(function(){
		jQuery(this).click(function(){
			if ( jQuery(this).hasClass('menuBtn_left') ){
				var menuPosition = 'left';
			} else {
				var menuPosition = 'right';
			}
			// ※ この時点でLightning本体のmaster.jsによって既に menuOpenに新しく切り替わっている
			if ( jQuery(this).hasClass('menuOpen') ) {
				slide_menu_open(menuPosition);
			} else {
				slide_menu_close(menuPosition);
			}
		});
	});
}

function slide_menu_open(menuPosition){
	// console.log('_|＼○_ﾋｬｯ ε=＼＿○ﾉ ﾎｰｳ!!');
	var navSection_open_position = 'navSection_open_' + menuPosition;
	jQuery('#navSection').addClass(navSection_open_position);

	var wrap_width = jQuery('body').width();
	jQuery('#bodyInner').css({"width":wrap_width});
	jQuery('#wrap').css({"width":wrap_width});

	jQuery('#gMenu_outer').appendTo('#navSection');

	if ( menuPosition == 'right' ){
		jQuery('#wrap').stop().animate({
			// 右にメニューを表示するために左に逃げる
			"margin-left": "-" + menu_width,
		},200);
		jQuery('#navSection').css({"display":"block","width":menu_width, "right" :"-"+menu_width }).stop().animate({
			"right":0,
		},200);
	} else if ( menuPosition == 'left' ){
		jQuery('#wrap').stop().animate({
			"margin-left":menu_width,
		},200);
		jQuery('#navSection').css({"display":"block","width":menu_width, "left" :"-"+menu_width }).stop().animate({
			"left":0,
		},200,function(){
		});
	}
}
function slide_menu_close(menuPosition){

	if ( !menuPosition ){
		if ( jQuery('#navSection').hasClass('navSection_open_right') ){
			menuPosition = 'right';
		} else {
			menuPosition = 'left';
		}
	}

	var wrap_width = jQuery('body').width();
	jQuery('#bodyInner').css({"width":wrap_width});
	jQuery('#wrap').css({"width":wrap_width});
	jQuery('#wrap').stop().animate({ "margin-left":"0" },200);

	if ( menuPosition == 'right' ) {
		jQuery('#navSection').stop().animate({ "right":"-"+menu_width },200,function(){
			menuClose_common();
		});
	} else if ( menuPosition == 'left' ){
		jQuery('#navSection').stop().animate({ "left":"-"+menu_width },200,function(){
			menuClose_common();
		});
	}
}
function menuClose_common(){
	// アニメーションが終わってから実行
	jQuery('#navSection').removeClass('navSection_open_right');
	jQuery('#navSection').removeClass('navSection_open_left');
	jQuery('#gMenu_outer').insertAfter('.navbar-header');
	jQuery('#navSection').css({"right":""});
	jQuery('#navSection').css({"left":""});
}
function run_menuResize(){
	var wrap_width = jQuery('body').width();
	jQuery('#bodyInner').css({"width":wrap_width});
	jQuery('#wrap').css({"width":wrap_width});
}