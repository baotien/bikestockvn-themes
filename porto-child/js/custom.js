jQuery(document).ready(function($){
	
	
	
	
	jQuery('.widget-mobile-footer ul#sidebar ul').hide();
	jQuery('.widget-mobile-footer .widgettitle').click(function(e) {
		jQuery(this).toggleClass("show-submenus"); 
		jQuery(this).parent().find('.menu').slideToggle(); 
	});
	jQuery('.woocommerce-MyAccount-navigation-link--edit-account a').text('Chi tiết tài khoản');
	
	jQuery('#mobi-menu-cat').hide();
	//jQuery('#mobi-menu-cat .popup').css('height','0px');
	jQuery('.popup .sub-menu .sub-menu').hide();
	//jQuery('.popup .sub-menu .sub-menu').css('height','0px');
	jQuery('.sidebar-menu-wrap > ul.sidebar-menu >li >a').click(function(e) {
		//e.preventDefault();
		//jQuery(this).parent().find('.popup').css('height','auto'); 
		//jQuery(this).parent().find('.popup').slideToggle(); 
	});
	jQuery('#header.sticky-header .section-mobile-top').slideToggle();
	
	
	
	jQuery( '.mobile-toggle' ).on('click',function(){
		jQuery("html, body").animate({ scrollTop: 0 }, "slow");
	});
	
	jQuery('.scrollbar-inner').scrollbar();

	
	
	jQuery('.scrollbar-inner .menu-item-has-children .arrow, .scrollbar-inner .sub-menu .menu-item-has-children').on('click',function(){
		if( jQuery('.showed').length  > 0 ){
			jQuery('.scrollbar-inner').css('height','500px');
		}else{
			jQuery('.scrollbar-inner').css('height','auto');	
		}
		
		jQuery('.scrollbar-inner').scrollbar();
	});
	
	
	var width_screen = jQuery(window).width();
	if( width_screen > 991 ){
		jQuery('.main-menu-wrap').scrollToFixed();
	}
	if( width_screen < 991 ){
		jQuery('.section-mobile').show();
		jQuery('.section-mobile-top').show();
		
		/*
		var summaries = $('#mobi-cat');
		summaries.each(function(i) {
			var summary = $(summaries[i]);

			summary.scrollToFixed({
				marginTop: 50,
				zIndex: 999
			});
		});
		*/
		
		/*
		var summariess = $('.section-mobile-top');
		summariess.each(function(i) {
			var summary = $(summariess[i]);
			summary.scrollToFixed({
				marginTop: 0,
				zIndex: 9998
			});
		});
		*/
	}
	
	jQuery('#mobi-menu-cat').hide();
	//jQuery('#mobi-menu-cat .popup').css('height','0px');
	jQuery('.popup >.inner >.sub-menu').hide();
	jQuery('.popup .sub-menu .sub-menu').hide();
	jQuery('.sidebar-menu .narrow .popup li.menu-item-has-children > a').click(function(e) {
		e.preventDefault();
		
		jQuery('.sidebar-menu .narrow .popup li.menu-item-has-children > a').not(this).parent().removeClass('showed');
		jQuery('.sidebar-menu .narrow .popup li.menu-item-has-children > a').not(this).parent().find('.sub-menu').hide();
		jQuery('.sidebar-menu .narrow .popup li.menu-item-has-children > a').not(this).parent().find('.popup').hide();
		jQuery('.sidebar-menu .narrow .popup li.menu-item-has-children > a').not(this).parent().removeClass('show-submenu-two');
		
		if( jQuery(this).parent().hasClass('showed') ){
			jQuery(this).parent().removeClass('showed');
			jQuery(this).parent().find('.sub-menu').hide();
			jQuery(this).parent().find('.popup').hide();
			jQuery(this).parent().removeClass('show-submenu-two');
		}else{
			jQuery(this).parent().addClass('showed');
			jQuery(this).parent().find('.sub-menu').slideToggle();
			jQuery(this).parent().find('.popup').show();
			jQuery(this).parent().addClass('show-submenu-two');
			
		}
		
	});

	if( width_screen > 991 ){
		jQuery('#menu-main-menu li.menu-item-has-children').hover(function(e) {
			jQuery('.popup >.inner >.sub-menu').show();
		});
	}

	jQuery( '#mobi-cat h3' ).on('click',function(){
		jQuery('#mobi-menu-cat').slideToggle();
		jQuery("html, body").animate({ scrollTop: 0 }, "slow");
	});
	jQuery('.sidebar-menu-wrap > ul.sidebar-menu > li> .arrow').click(function(e) {
		jQuery('.sidebar-menu-wrap > ul.sidebar-menu >li>.arrow').not(this).parent().removeClass('showed');
		jQuery(this).parent().parent().find('.inner >.sub-menu').hide();
		if( jQuery(this).parent().hasClass('showed') ){
			jQuery(this).parent().removeClass('showed');
			jQuery(this).parent().find('.inner >.sub-menu').hide();
		}else{
			jQuery(this).parent().addClass('showed');
			jQuery(this).parent().find('.inner >.sub-menu').slideToggle();
		}
		
	});
	if( width_screen > 992 ){
		jQuery('.popup >.inner >.sub-menu').hide();
		jQuery('.header-main').hide();
	}
})
