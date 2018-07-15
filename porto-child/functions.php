<?php
if (is_admin()) {

    function theme_options_panel() {
        add_menu_page('theme_options_panel', 'Cập nhật Phụ tùng', 'manage_options', 'cap-nhat-permalink', 'wps_cap_nhat_permalink');
    }

    add_action('admin_menu', 'theme_options_panel');

    function sanitize_title_with_num($post_name, $num = 0) {
        global $wpdb;
        if ($num > 0)
            $post_name_tmp = $post_name . '-' . $num;
        else
            $post_name_tmp = $post_name;
        $count = $wpdb->get_var("SELECT COUNT(*) FROM biker_posts WHERE post_name = '$post_name_tmp'");
        if ($count == 0)
            return $post_name_tmp;
        else {
            $num++;
            return sanitize_title_with_num($post_name, $num);
        }
    }

    function wps_cap_nhat_permalink() {
        global $wpdb;
        if (isset($_POST["cap-nhat-categories"])) {
            $categories = $wpdb->get_results("SELECT * FROM bang_gia_phu_tung_categories");
            if (count($categories) > 0) {
                foreach ($categories as $cat) {
                    $nhan_hieu = $cat->nhan_hieu;
                    $term_nhan_hieu = get_term_by('name', $nhan_hieu, 'product_cat');
                    if (!$term_nhan_hieu) {
                        wp_insert_term($nhan_hieu, 'product_cat');
                        $term_nhan_hieu = get_term_by('name', $nhan_hieu, 'product_cat');
                    }
                    $term_nhan_hieu_id = $term_nhan_hieu->term_id;

                    $dong_xe = $cat->dong_xe;
                    $term_dong_xe = get_term_by('name', $dong_xe, 'product_cat');
                    if (!$term_dong_xe) {
                        wp_insert_term($dong_xe, 'product_cat', array('parent' => $term_nhan_hieu_id));
                        $term_dong_xe = get_term_by('name', $dong_xe, 'product_cat');
                    }

                    $term_dong_xe_id = $term_dong_xe->term_id;

                    $wpdb->query("UPDATE bang_gia_phu_tung_categories SET term_taxonomy_id = $term_dong_xe_id WHERE dong_xe = \"$dong_xe\"");
                }
            }
        }
        if (isset($_POST["cap-nhat-permalink"])) {
            $products = $wpdb->get_results("SELECT ID, post_title, post_name FROM biker_posts WHERE post_name = '' AND post_type = 'product'");
            if (count($products) > 0) {
                foreach ($products as $product) {
                    $ID = $product->ID;
                    $post_name = $product->post_name;
                    if ($post_name == '') {
                        $post_name = sanitize_title($product->post_title);
                        $post_name = sanitize_title_with_num($post_name);
                        $wpdb->query("UPDATE biker_posts SET post_name = '$post_name' WHERE ID = $ID");
                    }
                }
            }
        }
        if (isset($_POST["cap-nhat-hinh-anh"])) {
            $hinh_anh = $wpdb->get_results("SELECT * FROM hinh_anh_phu_tung_chua_xu_ly");
            if (count($hinh_anh) > 0) {
                foreach ($hinh_anh as $ha) {
                    $post_title = $ha->hinh_anh;
                    $term_taxonomy_id = $ha->term_taxonomy_id;
                    $phu_tung_ids = $ha->phu_tung_ids;
                    $post = array(
                        'post_author' => 1,
                        'post_content' => '',
                        'post_status' => "publish",
                        'post_title' => $post_title,
                        'post_parent' => '',
                        'post_type' => "product",
                    );

                    $post_id = wp_insert_post($post, $wp_error);
                    $term = array((int) $term_taxonomy_id);
                    wp_set_object_terms($post_id, $term, 'product_cat');
                    wp_set_object_terms($post_id, 'simple', 'product_type');

                    update_post_meta($post_id, '_visibility', 'visible');
                    update_post_meta($post_id, '_stock_status', 'instock');
                    update_post_meta($post_id, 'total_sales', '0');
                    update_post_meta($post_id, '_downloadable', 'no');
                    update_post_meta($post_id, '_virtual', 'yes');
                    update_post_meta($post_id, '_regular_price', "");
                    update_post_meta($post_id, '_sale_price', "");
                    update_post_meta($post_id, '_purchase_note', "");
                    update_post_meta($post_id, '_featured', "no");
                    update_post_meta($post_id, '_weight', "");
                    update_post_meta($post_id, '_length', "");
                    update_post_meta($post_id, '_width', "");
                    update_post_meta($post_id, '_height', "");
                    update_post_meta($post_id, '_sku', "");
                    update_post_meta($post_id, '_product_attributes', array());
                    update_post_meta($post_id, '_sale_price_dates_from', "");
                    update_post_meta($post_id, '_sale_price_dates_to', "");
                    update_post_meta($post_id, '_price', "");
                    update_post_meta($post_id, '_sold_individually', "");
                    update_post_meta($post_id, '_manage_stock', "no");
                    update_post_meta($post_id, '_backorders', "no");
                    update_post_meta($post_id, '_stock', "");
                    update_post_meta($post_id, 'loai_san_pham', "cha");
                    update_post_meta($post_id, 'phu_tung_ids', $phu_tung_ids);
                }
            }
            $wpdb->query("INSERT INTO hinh_anh_phu_tung_da_xu_ly SELECT * FROM hinh_anh_phu_tung_chua_xu_ly");
            $wpdb->query("DELETE FROM hinh_anh_phu_tung_chua_xu_ly");
        }
        if (isset($_POST["cap-nhat-san-pham-pho-bien"])) {
            $sps = $wpdb->get_results("SELECT * FROM san_pham_pho_bien");
            if (count($sps) > 0) {
                foreach ($sps as $sp) {
                    $post_id = $sp->post_id;
                    $term_id = $sp->term_id;
                    $terms = wp_get_object_terms($post_id, 'product_cat');
                    $term_ids = array();
                    foreach ($terms as $term)
                        $term_ids[] = (int) $term->term_id;
                    if (!in_array($term_id, $term_ids)) {
                        $term_ids[] = (int) $term_id;
                        wp_set_object_terms($post_id, $term_ids, 'product_cat');
                    }
                }
            }
        }
        ?>
        <div class="wrap">
            <h1>Cập nhật permalink</h1>
        </div>
        <div class="wrap">
            <span id="result_message" class="warning-msg"></span>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="submit" name="cap-nhat-categories" value="Cập nhật categories" class="btn page-title-action" />
                <input type="submit" name="cap-nhat-permalink" value="Cập nhật permalink" class="btn page-title-action" />
                <input type="submit" name="cap-nhat-hinh-anh" value="Cập nhật hình ảnh" class="btn page-title-action" />
                <input type="submit" name="cap-nhat-san-pham-pho-bien" value="Cập nhật sản phẩm phổ biến" class="btn page-title-action" />
            </form>
        </div>
        <?php
    }

}

add_action('wp_enqueue_scripts', 'porto_child_css', 1001);

// Load CSS
function porto_child_css() {
// porto child theme styles
    wp_deregister_style('styles-child');
    wp_register_style('styles-child', get_stylesheet_directory_uri() . '/style.css');
    wp_enqueue_style('styles-child');
    if (is_rtl()) {
        wp_deregister_style('styles-child-rtl');
        wp_register_style('styles-child-rtl', get_stylesheet_directory_uri() . '/style_rtl.css');
        wp_enqueue_style('styles-child-rtl');
    }
}

function search_filter($query) {
    if (!is_admin() && $query->is_main_query()) {
        if ($query->is_search) {
            $search_keyword = $query->query['s'];
            if ($search_keyword != "") {
                global $wpdb;
                $product_ids = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $search_keyword));
                if (count($product_ids) > 0) {
                    $query->set('post__in', $product_ids);
                    $query->set('s', '');
                }
            }
        }
    }
    return $query;
}

add_action('pre_get_posts', 'search_filter', 999);
include_once('custom-app.php');

function knowledgetheme_scripts() {
    wp_enqueue_script('panzoom-js', get_stylesheet_directory_uri() . '/js/panzoom.js', array('jquery'), '1.0', false);
    wp_enqueue_script('scrolltofixed-js', get_stylesheet_directory_uri() . '/js/jquery-scrolltofixed-min.js', array('jquery'), '1.0', false);
    wp_enqueue_script('scrollbar-js', get_stylesheet_directory_uri() . '/js/simple-scrollbar.min.js', array('jquery'), '1.0', false);
    wp_enqueue_script('jq-scrollbar-js', get_stylesheet_directory_uri() . '/js/jquery.scrollbar.min.js', array('jquery'), '1.0', false);
    wp_enqueue_style('jq-scrollbar-css', get_stylesheet_directory_uri() . '/jquery.scrollbar.css');
    wp_enqueue_style('styles-scrollbar', get_stylesheet_directory_uri() . '/simple-scrollbar.css');
    wp_enqueue_style('camera_style', get_stylesheet_directory_uri() . '/camera_style.css');
    wp_enqueue_script('camera-js', get_stylesheet_directory_uri() . '/js/camera.js', array('jquery'), '1.0', false);
}

add_action('wp_enqueue_scripts', 'knowledgetheme_scripts', 99);

function knowledgetheme_scripts_new() {
    wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/js/custom.js', array('jquery'), '1.0', true);
}

add_action('wp_enqueue_scripts', 'knowledgetheme_scripts_new', 99);

function pu_remove_script_version($src) {
    return remove_query_arg('ver', $src);
}

add_filter('script_loader_src', 'pu_remove_script_version');
add_filter('style_loader_src', 'pu_remove_script_version');
add_filter('show_admin_bar', '__return_false');

function pu_remove_script_version_new($src) {
    return remove_query_arg('version', $src);
}

add_filter('script_loader_src', 'pu_remove_script_version_new');
add_filter('style_loader_src', 'pu_remove_script_version_new');

function wps_theme_func() {
    echo '
<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2>Theme</h2>
</div>
';
}

function wps_theme_func_settings() {
    echo '
<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2>Settings</h2>
</div>
';
}

function wps_theme_func_faq() {
    echo '
<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2>FAQ</h2>
</div>
';
}

add_action('widgets_init', 'theme_slug_widgets_footer_mobile');

function theme_slug_widgets_footer_mobile() {
    register_sidebar(array(
        'name' => __('Footer Mobile', 'theme-slug'),
        'id' => 'sidebar-1',
        'description' => __('Widgets in this area will be shown on all posts and pages.', 'theme-slug'),
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h2 class="widgettitle">',
        'after_title' => '</h2>',
    ));
}

add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
    echo '<style>
			
			body #wcp-editor-main-tab-contents .wcp-editor-main-tab-content .wcp-editor-main-tab-content-inner-wrap .wcp-editor-form-tabs-wrap .wcp-editor-form-tab-button {
				display: none;
			}
			body #wcp-editor-main-tab-contents .wcp-editor-main-tab-content .wcp-editor-main-tab-content-inner-wrap .wcp-editor-form-tabs-wrap .wcp-editor-form-tab-button:nth-child(2) {
				display: block;
			}
			body #wcp-editor-main-tab-contents .wcp-editor-main-tab-content .wcp-editor-main-tab-content-inner-wrap .wcp-editor-form-tabs-wrap .wcp-editor-form-tab-button:nth-child(1) {
				display: block;
			}
	
			body #wcp-editor-right {
				width: 240px;
			}
			body #wcp-editor-left {
				width: 280px;
			}
			body #wcp-editor-toolbar-wrap {
				position: absolute;
				left: 234px;
				top: 12px;
			}
			body #wcp-editor-toolbar .wcp-editor-toolbar-button .wcp-editor-toolbar-button-icon {
				width: 44px;
				height: 64px;
			}
			body #wcp-editor-toolbar .wcp-editor-toolbar-button {
				width: 44px;
				height: 64px;
				display: inline-block;
				float: left;
				line-height: 64px;
				border-right: 1px solid #ddd;
				border-bottom: none;
			}
			body #wcp-editor-toolbar.wcp-editor-toolbar-grouped {
				position: relative;
				left: 0;
				top: 0;
				margin-bottom: 10px;
				overflow: hidden;
				float: left;
				display: inline-block;
			}
			body #wcp-editor-extra-main-buttons .wcp-editor-extra-main-button .wcp-editor-extra-main-button-title {
				font-size: 10px;
				padding: 0 5px;
				min-width: 30px;
			}
			body .wcp-editor-save-list-item-wrap .wcp-editor-save-list-item {
				height: 25px;
				line-height: 25px;
			}
			body .wcp-editor-save-list-item-wrap .wcp-editor-save-list-item-delete-button {
				width: 25px;
				height: 25px;
				font-size: 16px;
				text-align: center;
				line-height: 25px;
			}
			body .wcp-editor-save-list-item-wrap {
				margin-bottom: 5px;
			}
			body #wcp-editor-modal .wcp-editor-modal-body .wcp-editor-modal-content {
				overflow: hidden;
			}
			body .wcp-editor-save-list-item-wrap {
				display: flex;
				width: 11.5%;
				margin-bottom: 5px;
				float: left;
				margin-left: 5px;
				margin-right: 5px;
			}
			body #wcp-editor-modal .wcp-editor-modal-body .wcp-editor-modal-content {
				margin: 0px;
			}
			body #wcp-editor-modal .wcp-editor-modal-body {
				margin: 0px;
				max-width: 100%;
			}
			body #wcp-editor-modal .wcp-editor-modal-body .wcp-editor-modal-close {
				right: 0px;
				top: 0px;
			}
			body #wcp-editor-right .wcp-editor-list-item {
				height: 20px;
			}
			body #wcp-editor-right .wcp-editor-list-item .wcp-editor-list-item-title {
				line-height: 20px;
			}
			body #wcp-editor-center {
				text-align: center;
			}
			body #wcp-editor-center #wcp-editor-canvas {
				top: 200px;
				text-align: center;
				display: inline-block;
			}
			body #wcp-editor-center {
				display: block;
			}
			#wcp-editor-right div#wcp-editor-list-title {
				display: none;
			}
			#wcp-editor-right #wcp-editor-list-item-title-buttons {
				top: 0px;
				height: 25px;
			}
			body #wcp-editor-right.wcp-editor-right-with-title-buttons {
				padding-top: 25px;
			}
			#wcp-editor-modal .wcp-editor-modal-body .wcp-editor-modal-header {
				display: none;
			}
		</style>';
}

if (function_exists('acf_add_options_page')) {
    acf_add_options_page();
    acf_add_options_sub_page('Slider Zoom');
}

function shortcode_slider_ct() {
    ob_start();
    ?>
    <div class="slider_block">
        <script type="text/javascript">
            jQuery(function () {
                jQuery('#camera_wrap').camera({
                    alignmen: 'topCenter',
                    height: '31.707%',
                    minHeight: '34px',
                    loader: true,
                    loaderColor: '#f48b47',
                    loaderPadding: '0',
                    loaderStroke: '2',
                    navigation: false,
                    pagination: true,
                    fx: 'simpleFade',
                    navigationHover: false,
                    thumbnails: false,
                    playPause: false
                });
            });
        </script>
        <div class="fluid_container">
            <div id="camera_wrap" class="camera_wrap camera_orange_skin">
                <?php
                if (have_rows('detail_slider', 'option')) {
                    while (have_rows('detail_slider', 'option')) : the_row();

                        the_sub_field('sub_field_name');
                        ?>
                        <div data-link="<?php echo get_sub_field('link_slider'); ?>" data-src="<?php echo get_sub_field('image_slider'); ?>">
                            <div class="camera_caption fadeIn">
                                <div class="lof_camera_title"><?php echo get_sub_field('title_slider'); ?></div>
                                <div class="lof_camera_desc"><?php echo get_sub_field('subtitle_slider'); ?></div>
                            </div>
                        </div>
                        <?php
                    endwhile;
                }
                ?>
            </div>
            <div class="clear">&nbsp;</div>
        </div>
    </div>
    <?php
    $shortcode_slider_ct = ob_get_contents();
    ob_end_clean();
    return $shortcode_slider_ct;
    die(1);
}

add_shortcode('shortcode_slider_ct', 'shortcode_slider_ct');

add_action('vc_before_init', 'slider_ct');

function slider_ct() {
    vc_map(array(
        "name" => __("Slider Zoom Custom", "starbeach"),
        "base" => "shortcode_slider_ct",
        "class" => "",
        "category" => __("Content", "starbeach"),
        "params" => array(
        ),
        "description" => "Slider Zoom Custom",
    ));
}