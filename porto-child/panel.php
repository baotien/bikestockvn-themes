<?php
global $porto_settings;
?>
<div class="panel-overlay"></div>
<div id="nav-panel" class="<?php echo (isset($porto_settings['mobile-panel-pos']) && $porto_settings['mobile-panel-pos']) ? $porto_settings['mobile-panel-pos'] : '' ?>">
    <?php
     wp_nav_menu($args);
    ?>
</div>
<a href="#" id="nav-panel-close" class="<?php echo (isset($porto_settings['mobile-panel-pos']) && $porto_settings['mobile-panel-pos']) ? $porto_settings['mobile-panel-pos'] : '' ?>"><i class="fa fa-close"></i></a>