<?php
global $porto_settings, $porto_layout, $porto_sidebar;
?>
<div class="sidebar-overlay"></div>
<style>.mobile-sidebar #main-sidebar-menu {    display: block !important;}.mobile-sidebar {    width: 280px;    left: -20px !important;}.sidebar-opened .mobile-sidebar {    left: 0px !important;}body .mobile-sidebar .sidebar-menu > li.menu-item > .arrow {  top: 0px !important;}

#main-sidebar-menu #nav-menu-item-1167, #main-sidebar-menu #nav-menu-item-1169, #main-sidebar-menu #nav-menu-item-1168, #main-sidebar-menu #nav-menu-item-2413 {
	display: none !important;
}
</style><div class="mobile-sidebar">	
    <div class="sidebar-toggle"><i class="fa"></i></div>
    <div class="sidebar-content">
        <?php		$sidebar_menu = porto_sidebar_menu();            if ($sidebar_menu) : ?>                <div id="main-sidebar-menu" class="widget_sidebar_menu">                    <?php if ($porto_settings['menu-sidebar-title']) : ?>                        <div class="widget-title">                            <?php echo force_balance_tags($porto_settings['menu-sidebar-title']) ?>                            <?php if ($porto_settings['menu-sidebar-toggle']) : ?>                                <div class="toggle"></div>                            <?php endif; ?>                        </div>                    <?php endif; ?>                    <div class="sidebar-menu-wrap" >                        <?php echo $sidebar_menu ?>                    </div>                </div>            <?php endif; ?>
    </div>
</div>
