<?php


global $porto_settings, $porto_layout;

?>


<header id="header" class="header-separate header-1 <?php echo $porto_settings['search-size'] ?> sticky-menu-header<?php echo ($porto_settings['logo-overlay'] && $porto_settings['logo-overlay']['url']) ? ' logo-overlay-header' : '' ?>">


    <?php if ($porto_settings['show-header-top']) : ?>


    <div class="header-top">


        <div class="container">


            <div class="header-left">
				<div class="row">
					<div class="col-lg-6 col-md-12">
						<?php echo do_shortcode('[porto_block label="" name="menu-ads"]'); ?>
					</div>
				</div>
                <?php


                // show social links


                echo porto_header_socials();





                // show currency and view switcher


                $currency_switcher = porto_currency_switcher();


                $view_switcher = porto_view_switcher();





                if ($currency_switcher || $view_switcher)


                    echo '<div class="switcher-wrap">';





                echo $currency_switcher;





                if ($currency_switcher && $view_switcher)


                    echo '<span class="gap switcher-gap">|</span>';





                echo $view_switcher;





                if ($currency_switcher || $view_switcher)


                    echo '</div>';


                ?>


            </div>


            <div class="header-right">


                <?php


                // show welcome message and top navigation


                $top_nav = porto_top_navigation();





                if ($porto_settings['welcome-msg']){
					//echo '<span class="welcome-msg">' . do_shortcode($porto_settings['welcome-msg']) . '</span>';
				}
                if ($porto_settings['welcome-msg'] && $top_nav)
                    echo '<span class="gap">|</span>';
                //echo $top_nav;
                ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
	
	<div class="section-mobile-top" style="display:none;min-height:50px;">
		<div class="col-xs-3 custom-col">
			<a class="mobile-toggle"><i class="fa fa-reorder"></i></a>
		</div>
		<div class="col-xs-3 custom-col">
			<div class="fb-customer"><a href="<?php echo home_url();?>/faq/"><img src="<?php echo home_url();?>/wp-content/themes/porto-child/images/faq.png"></a></div>
		</div>
		<div class="col-xs-3 custom-col" style="border-right:none;">
			<?php echo porto_minicart();?>
		</div>
		<div class="col-xs-3 custom-col">
			<?php  echo porto_search_form(); ?>
		</div>
		
	</div>
	
    <div class="header-main">
        <div class="container">
            <div class="header-left">
                <?php
                // show logo
                $logo = porto_logo();
                echo $logo;
                ?>
            </div>
            <div class="header-center">
                <?php
                $minicart = porto_minicart();
                ?>
                <?php
                get_template_part('header/header_tooltip');
                ?>
            </div>
            <div class="header-right">
				<div class="clear">	
                 <?php
                // show search form
				echo '<span class="welcome-msg">' . do_shortcode($porto_settings['welcome-msg']) . '</span>';
				?>
				</div>
				<?php  echo porto_search_form(); ?>
				 <div class="<?php if ($minicart) echo 'header-minicart'.str_replace('minicart', '', $porto_settings['minicart-type']) ?>">
                    <?php
                    // show contact info and mini cart
                    $contact_info = $porto_settings['header-contact-info'];
                    echo $top_nav;
					echo '<div class="fb-customer"><a href="'.home_url().'/faq/"><img src="'.home_url().'/wp-content/themes/porto-child/images/faq.png"></a></div>';
                    echo $minicart;
                    ?>
                </div>
                <a class="mobile-toggle"><i class="fa fa-reorder"></i></a>
            </div>
        </div>
    </div>
    <?php
    // check main menu
    $main_menu = porto_main_menu();
    if ($main_menu) : ?>
        <div class="main-menu-wrap<?php echo ($porto_settings['menu-type']?' '.$porto_settings['menu-type']:'') ?>">
            <div id="main-menu" class="container <?php echo $porto_settings['menu-align'] ?><?php echo $porto_settings['show-sticky-menu-custom-content'] ? '' : ' hide-sticky-content' ?>">
                <?php if ($porto_settings['show-sticky-logo']) : ?>
                    <div class="menu-left">
                        <?php
                        // show logo
                        $logo = porto_logo( true );
                        echo $logo;
                        ?>
                    </div>
                <?php endif; ?>
                <div class="menu-center">
                    <?php
                    // show main menu
                    echo $main_menu;
                    ?>
                </div>
                <?php if ($porto_settings['show-sticky-searchform'] || $porto_settings['show-sticky-minicart']) : ?>
                    <div class="menu-right">
                        <?php
                        // show search form
                        echo porto_search_form();
                        // show mini cart
                        echo porto_minicart();
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
	<div class="section-mobile" style="display:none;">
		<div id="mobi-cat-block">
			<?php echo do_shortcode('[porto_block label="" name="menu-ads"]'); ?>
		</div>
		<div id="mobi-logo">
			<?php echo $logo; ?>
		</div>
		<div id="mobi-cat">
			<h3>Danh mục <i class="fa fa-reorder"></i></h3>
		</div>
		<div id="mobi-menu-cat">
			<?php
				$sidebar_menu = porto_sidebar_menu();
			?>
			 <div class="sidebar-menu-wrap" ss-container style="height: 345px;">
				<?php echo $sidebar_menu ?>
			</div>
		</div>
		<div id="logobrands-mobile">
			<?php echo do_shortcode("[show-logos orderby='date' category='0' activeurl='inactive' style='normal' interface='grid12' tooltip='false' description='false' limit='0' filter='false' ]"); ?> 
		</div>
	</div>
</header>