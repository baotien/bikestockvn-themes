<?php
add_action( 'wp_ajax_ajax_get_years', 'ajax_get_years' );
add_action( 'wp_ajax_nopriv_ajax_get_years', 'ajax_get_years' );

add_action( 'wp_ajax_ajax_get_models', 'ajax_get_models' );
add_action( 'wp_ajax_nopriv_ajax_get_models', 'ajax_get_models' );

add_action( 'wp_ajax_ajax_get_products', 'ajax_get_products' );
add_action( 'wp_ajax_nopriv_ajax_get_products', 'ajax_get_products' );

add_action( 'wp_ajax_ajax_get_products_html', 'ajax_get_products_html' );
add_action( 'wp_ajax_nopriv_ajax_get_products_html', 'ajax_get_products_html' );

add_action( 'wp_ajax_ajax_get_brands', 'ajax_get_brands' );
add_action( 'wp_ajax_nopriv_ajax_get_brands', 'ajax_get_brands' );

add_action( 'wp_ajax_ajax_get_product_item', 'ajax_get_product_item' );
add_action( 'wp_ajax_nopriv_ajax_get_product_item', 'ajax_get_product_item' );

add_action( 'wp_ajax_ajax_add_all_to_cart', 'ajax_add_all_to_cart' );
add_action( 'wp_ajax_nopriv_ajax_add_all_to_cart', 'ajax_add_all_to_cart' );

add_action( 'wp_ajax_ajax_search_by_part', 'ajax_search_by_part' );
add_action( 'wp_ajax_nopriv_ajax_search_by_part', 'ajax_search_by_part' );

function sanitize_json_for_save($save) {
	$json = $save['json'];

	$json = str_replace('\\\n', "<br>", $json); // Replace new line characters with <br>
	$json = str_replace('\"', '"', $json); // Replace \" with "
	$json = str_replace("\\'", "'", $json); // Replace \' with '
	$json = str_replace('\\\"', '\"', $json); // Replace \\" with \"
	
	$decoded = json_decode($json);
	
	for ($i=0; $i<count($decoded->spots); $i++) {
		$spot = $decoded->spots[$i];

		if (isset($spot->tooltip_content->plain_text)) {
			$spot->tooltip_content->plain_text = do_shortcode($spot->tooltip_content->plain_text);

			$pattern = '/\\+"/';
			$spot->tooltip_content->plain_text = preg_replace($pattern, '\"', $spot->tooltip_content->plain_text);
		}
		
		if (isset($spot->tooltip_content->squares_settings)) {
			// Loop over containers
			for ($j=0; $j<count($spot->tooltip_content->squares_settings->containers); $j++) {
				$container = $spot->tooltip_content->squares_settings->containers[$j];
				$elements = $container->settings->elements;

				// Loop over elements
				for ($k=0; $k<count($elements); $k++) {
					$element = $elements[$k];

					if ($element->settings->name == 'Paragraph') {
						// Replace
						if (isset($element->options->text->text)) {
							$element->options->text->text = do_shortcode($element->options->text->text);

							$pattern = '/\\+"/';
							$element->options->text->text = preg_replace($pattern, '\"', $element->options->text->text);
						}
					}
				}
			}
		}
	}

	$save['json'] = json_encode($decoded);
	return $save;
}

function ajax_search_by_part(){
	$search_keyword = $_POST['s'];
	if( $search_keyword != "" ){
		global $wpdb;
		$product_ids = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $search_keyword ) );
		$products = array();
		if( count( $product_ids ) > 0 ){
			$args = array(
				'posts_per_page'   => -1,
				'post_status'      => 'publish',
				'post_type'        => 'product',
				'post__in'         => $product_ids
			);
			$products = get_posts( $args );
		}
		ob_start();
		if( count( $products ) > 0 ){
			foreach( $products as $product ){
				$thumbnail_id 	= get_post_thumbnail_id( $product->ID );
				$image 			= wp_get_attachment_url( $thumbnail_id );
				?>
					<li class="show-links-onimage product">
					   <div class="product-image">
						  <a href="javascript:void(0);" class="product-item" data-id="<?php echo $product->ID;?>">
							 <div class="inner">
								<img width="300" height="300" src="<?php echo $image;?>" class=" wp-post-image" alt="">
							</div>
						  </a>
						  <div class="links-on-image">
							 <div class="add-links-wrap">
								<div class="add-links  clearfix custom-links">
									<a href="javascript:void(0);">Name: <?php echo $product->post_title ;?></a>
									<a href="javascript:void(0);">Model: <?php echo $product->post_title ;?></a>
								</div>
							 </div>
						  </div>
					   </div>
					</li>
				<?php
			}
		}else{
			?>
			<p class="woocommerce-info">Không tìm thấy sản phẩm nào khớp với lựa chọn của bạn.</p>
			<?php
		}
		echo ob_get_clean();
		die();
	}
}

function ajax_add_all_to_cart(){
	$pro_ids 	= $_POST['pro_ids'];
	$pro_qtys 	= $_POST['pro_qtys'];
	
	$cart_array = array();
	$return['status'] = 0;
	if( count( $pro_ids ) > 0 ){
		foreach( $pro_ids as $key => $pro_id ){
			$cart_array[$key]['pro_id'] = $pro_id;
			$cart_array[$key]['qty'] 	= $pro_qtys[$key];
		}
		
		foreach( $cart_array as $cat_item ){			
			$product_id = $cat_item['pro_id'];
			$quantity 	= $cat_item['qty'];
			$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
			if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity  ) ) {
				do_action( 'woocommerce_ajax_added_to_cart', $product_id );
				if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
					wc_add_to_cart_message( $product_id );
				}
				$return['status'] = 1;
			}
		}
	}
	echo json_encode( $return );
	die();
}

function ajax_get_product_item(){
	$pro_id  	= $_POST['pro_id'];
	$is_image_hotspot 	= get_field('image_hotspot',$pro_id);
	$is_product_childs 	= get_field('san_pham_con',$pro_id);
	ob_start();
	?>
	<div class="row">
		<?php if( $is_image_hotspot ): ?>
			<?php
				$image_hotspots = get_field('image_hotspot',$pro_id);
				
				$id_hotspot = 0;
				if( $image_hotspots && is_array($image_hotspots) ){
					foreach( $image_hotspots as $image_hotspot ){
						$id_hotspot = $image_hotspot->ID;
					}
				}
            ?>
			<?php if( $id_hotspot ): ?>
				 <div class="col-md-6 summary-before">
                    <?php 
                        $h_shortcode_id = get_post_meta( $id_hotspot,'wpcf-h-shortcode-id',true );
                        $h_shortcode    = get_post_meta( $id_hotspot,'wpcf-h-shortcode',true );

                        $id_hotspots_points = array();

                        $options = get_option('image-map-pro-wordpress-admin-options');
                        $save           = $options['saves'][$h_shortcode_id];
                        $json           = $save['json'];
                        $json_decode    = json_decode($json);
                        $spots          = $json_decode->spots;

                        if( count( $spots ) > 0 ){
                            foreach ($spots as $key => $spot) {
								if( $spot->actions ){
									$id_hotspots_points[$key]['id']         = $spot->actions->link;
									$id_hotspots_points[$key]['object_id']  = $spot->id;
								}

                            }
                        }
						
                        echo '<div class="panzoom-parent">';
                            echo ' <div class="buttons">
                                <button class="zoom-in"><i class="fa fa-plus-circle" aria-hidden="true"></i>Zoom In</button>
                                <button class="zoom-out"><i class="fa fa-minus-circle" aria-hidden="true"></i>Zoom Out</button>
                                <button class="reset"><i class="fa fa-undo"></i>Reset</button>
                                <input type="range" class="zoom-range">
                             </div>';
                            echo '<div class="panzoom">';
                             echo do_shortcode('['.$h_shortcode.']'); 
                            echo '</div>';
                        echo '</div>';
                       
                    ?>
				</div>
				<div class="col-md-6 summary entry-summary custom-height-scroll">
					<?php if( count($id_hotspots_points) > 0):?>
					<table id="hotspot_items" style="margin-bottom:0px;" class="table table data_head">
						<thead style="">
								<tr style="color: #333;">
									<th style="border-top-left-radius: 5px;">No</th>
									<th>Part No</th>
									<th>Name</th>
									<th>Sug.Q</th>
									<th style="border-top-right-radius: 5px;">Price</th>
								</tr>
						</thead>
						<tbody class="">
							<?php
								$i = 0;
								foreach ($id_hotspots_points as $id_hotspots_point) {
									$p_data       = get_post($id_hotspots_point['id']); 
									$_product     = wc_get_product( $id_hotspots_point['id'] );
									if( $_product->price ){
										$price          = $_product->get_price_html();
									}else{
										$price          = '<span class="woocommerce-Price-amount amount">Liên Hệ</span>';
									}
									$p_data_title = $p_data->post_title;
									$sku 		= get_post_meta($id_hotspots_point['id'],'_sku',true);
									$stt 		= get_post_meta($id_hotspots_point['id'],'stt',true);
									$sg_qty 	= get_post_meta($id_hotspots_point['id'],'so_luong',true);
									$i++;
								  
								   ?>
									<tr class="quickview-add-to-tmp-cart menu-item hotspot-item hotspot-item-id-<?php echo $id_hotspots_point['id'];?>" data-id="<?php echo $id_hotspots_point['id'];?>" data-object="<?php echo $id_hotspots_point['object_id'];?>">
										<td width="10%"><span class="pro-no"><?php echo $stt;?></span></td>
										<td width="30%"><span class="pro-sku"><?php echo $sku;?></span></td>
										<td width="40%"><span class="pro-title"><?php echo $p_data_title;?></span></td>
										<td width="10%"><span class="pro-qty"><?php echo $sg_qty;?></span></td>
										<td width="10%"><span class=""><?php echo $price;?></td>
									</tr>
									
								   <?php
								   
								}
							?>
						</tbody>
					</table>
					<?php  endif;?>
				   
				</div>
				
				
				<?php echo _print_css_js(); ?>

				<script type="text/javascript" src="<?php echo home_url();?>/wp-content/plugins/image-map-pro-wordpress/js/image-map-pro.min.js"></script>
				<link rel="stylesheet" type="text/css" href="<?php echo home_url();?>/wp-content/plugins/image-map-pro-wordpress/css/image-map-pro.min.css">
				<script>
				;(function ($, window, document, undefined ) {
					$(document).ready(function() {
						setTimeout(function() {
							
						<?php

						$save = sanitize_json_for_save($save);

						echo 'var settings = '. $save['json'] .';' . "\n";
						echo "jQuery('#image-map-pro-". $h_shortcode_id . "').imageMapPro(settings);";

						?>

						}, 0);
					});
				})(jQuery, window, document);
				</script>
			<?php endif; ?>
		<?php elseif( count( $is_product_childs ) > 0 ): ?>
			<div class="col-md-6 summary-before">
				<?php 
					$feat_image_url = wp_get_attachment_url( get_post_thumbnail_id($pro_id) );
					echo '<div class="panzoom-parent">';
                            echo ' <div class="buttons">
                                <button class="zoom-in"><i class="fa fa-plus-circle" aria-hidden="true"></i>Zoom In</button>
                                <button class="zoom-out"><i class="fa fa-minus-circle" aria-hidden="true"></i>Zoom Out</button>
                                <button class="reset"><i class="fa fa-undo"></i>Reset</button>
                                <input type="range" class="zoom-range">
                             </div>';
                            echo '<div class="panzoom">';
                             echo '<img src="'.$feat_image_url.'">';
                            echo '</div>';
                        echo '</div>';
					
				?>
				
			</div>
			<div class="col-md-6 summary entry-summary custom-height-scroll">
				<?php if( count($is_product_childs) > 0):?>
				<table id="hotspot_items" style="margin-bottom:0px;" class="table table data_head">
					<thead style="">
							<tr style="color: #333;">
								<th style="border-top-left-radius: 5px;">No</th>
								<th>Part No</th>
								<th>Name</th>
								<th>Sug.Q</th>
								<th style="border-top-right-radius: 5px;">Price</th>
							</tr>
					</thead>
					<tbody class="">
						<?php
							$i = 0;
							foreach ($is_product_childs as $is_product_child) {
								$p_data       = get_post($is_product_child['product_id']); 
								$_product     = wc_get_product( $is_product_child['product_id'] );
								if( $_product->price ){
									$price          = $_product->get_price_html();
								}else{
									$price          = '<span class="woocommerce-Price-amount amount">Liên Hệ</span>';
								}
								$p_data_title = $p_data->post_title;
								$sku 		= get_post_meta($is_product_child['product_id'],'_sku',true);
								$stt 		= get_post_meta($is_product_child['product_id'],'stt',true);
								$sg_qty 	= get_post_meta($is_product_child['product_id'],'so_luong',true);
								$i++;
							  
							   ?>
								<tr class="quickview-add-to-tmp-cart menu-item hotspot-item hotspot-item-id-<?php echo $is_product_child['product_id'];?>" data-id="<?php echo $is_product_child['product_id'];?>">
									<td width="10%"><span class="pro-no"><?php echo $stt;?></span></td>
									<td width="30%"><span class="pro-sku"><?php echo $sku;?></span></td>
									<td width="40%"><span class="pro-title"><?php echo $p_data_title;?></span></td>
									<td width="10%"><span class="pro-qty"><?php echo $sg_qty;?></span></td>
									<td width="10%"><span class=""><?php echo $price;?></td>
								</tr>
								
							   <?php
							   
							}
						?>
					</tbody>
				</table>
				<?php  endif;?>
			</div>
			<?php echo _print_css_js(); ?>
		<?php else: ?>
			<h3>Sản phẩm này chưa được cấu hình đầy đủ, vui lòng xem thêm tại <a title="<?php echo get_the_title( $pro_id );?>" href="<?php echo get_the_permalink( $pro_id );?>"></a></h3>
		<?php endif; ?>
	</div>
	<?php
	echo ob_get_clean();
	die();
}


function _print_css_js (){
	ob_start(); 
	?>
	<div class="col-md-12 summary after-summary custom-height-scroll">
		 <form id="to-tmp-cart-form" style="display:none;">
			<input type="hidden" name="action" value="ajax_add_all_to_cart">
			<table id="add_cart_data" class="table table-responsive table-hover" >
				<thead style="">
					<tr style="color: #333;">
						<th style="border-top-left-radius: 5px;">No</th>
						<th>Part No</th>
						<th>Name</th>
						<th>Sug.Q</th>
						<th>Price</th>
						<th style="border-top-right-radius: 5px;">Action</th>
					</tr>
				</thead>
			   <tbody id="tbody_cart_parts">
				 
			   </tbody>
			   <tfoot>
					<tr>
					  <td colspan="5" class="btn-cart-action hide">
						<a class="btn btn-primary pull-left fa fa-shopping-cart" id="btn-add-all-to-cart"> Thêm Vào Giỏ </a>
						<a class="btn btn-primary pull-right btn-cart-action-link hide" href="<?php echo home_url();?>/checkout"> Thanh Toán </a>
						<a class="btn btn-default pull-right btn-cart-action-link hide" href="<?php echo home_url();?>/cart"> Giỏ Hàng </a>
					  </td>
					</tr>
				  </tfoot>
			</table>
		</form>
	</div>
	<style type="text/css">
	.quickview-add-to-tmp-cart {
		cursor: pointer;
	}
	.quickview-add-to-tmp-cart.h-hover, .quickview-add-to-tmp-cart:hover, .quickview-add-to-tmp-cart.active, .quickview-add-to-tmp-cart:hover > td{
		background: #fec700 !important;
		color: white;
	}
	.main-content.col-md-9 {
		width: 100%;
	}
	.col-md-3.sidebar.left-sidebar.mobile-hide-sidebar {
		display: none !important;
	}
	.p-hover, .imp-shape:hover { fill: rgba(255, 205, 24, 0.4) !important; }
	.h-hover a { color: #ffd332 !important; }
	form.woocommerce-ordering { display: none; }
	.back_arrow { float: right; margin-top: -33px; margin-right: 3px; cursor: pointer; font-size: 31px; }
	.gridlist-toggle { display: none; }
	nav.woocommerce-pagination { display: none; }
	.brand-item { display:block; }
	.data_add_to_cart td.price { font-size: 13px; }
	.data_add_to_cart .close_button { cursor: pointer; }
	.tmp-cart-msg { text-align: center !important; display: block; padding: 10px; color: green; }
</style>
<script type="text/javascript">
	jQuery( document ).ready( function(){
		var ajax_url = '<?php echo home_url();?>/wp-admin/admin-ajax.php';
		var $section = jQuery('.panzoom-parent').first();
			$section.find('.panzoom').panzoom({
			$zoomIn: $section.find(".zoom-in"),
			$zoomOut: $section.find(".zoom-out"),
			$zoomRange: $section.find(".zoom-range"),
			$reset: $section.find(".reset")
		});

		 jQuery('.hotspot-item').on('mouseenter',function(){
			var dta_object = jQuery(this).attr('data-object');
			jQuery('#'+dta_object).addClass('p-hover');
		});
		jQuery('.hotspot-item').on('mouseleave',function(){
			var dta_object = jQuery(this).attr('data-object');
			jQuery('#'+dta_object).removeClass('p-hover');
		});
		
		jQuery('body').on('click','.imp-shape',function(){
			var h_id = jQuery(this).attr('id');
			jQuery('.hotspot-item[data-object="'+h_id+'"].quickview-add-to-tmp-cart').trigger('click');
		});
	   
		jQuery('body').on('mouseenter','.imp-shape',function(){
			var h_id = jQuery(this).attr('id');
			jQuery('.hotspot-item[data-object="'+h_id+'"]').addClass('h-hover')
		});
		jQuery('body').on('mouseleave','.imp-shape',function(){
			 var h_id = jQuery(this).attr('id');
			 jQuery('.hotspot-item[data-object="'+h_id+'"]').removeClass('h-hover')
		});

		 /* CART */
		jQuery('body').on('click','#btn-add-all-to-cart',function(){
			
			if( jQuery('.data_add_to_cart').length > 0 ){
				add_loading_ajax();
				var dta = jQuery('#to-tmp-cart-form').serialize();
				jQuery.ajax({
					type: "POST",
					url: ajax_url,
					data: dta,
					dataType: "json"
				})
				.done(function( respon ) {
					if( respon.status == 1 ){
						jQuery('#tbody_cart_parts').empty();
						jQuery('#tbody_cart_parts').html('<span class="text-center tmp-cart-msg">Sản phẩm đã được thêm vào giỏ hàng !</span>');
						jQuery('.btn-cart-action-link').removeClass('hide');
						jQuery('#btn-add-all-to-cart').addClass('hide');
					}
					remove_loading_ajax();
				});
			}else{
				alert('Vui lòng chọn ít nhất một sản phẩm !');
			}
		});
		
		jQuery('body').on('click','.data_add_to_cart .close_button',function(){
			var pro_id      = jQuery(this).attr('data-id');
			jQuery('.to-tmp-cart-id-'+pro_id).remove();
			if( jQuery('.data_add_to_cart').length == 0 ){
				jQuery('.btn-cart-action').addClass('hide');
				
			}
			if( jQuery('.data_add_to_cart').length == 0 ){
				jQuery('#to-tmp-cart-form').hide();
			}
		});
		jQuery('body').on('click','.quickview-add-to-tmp-cart',function(){
			
			jQuery('.tmp-cart-msg').remove();
			jQuery('#btn-add-all-to-cart').removeClass('hide');
			jQuery('.btn-cart-action-link').addClass('hide');
			
			var pro_id      	= jQuery(this).attr('data-id');
			var pro_sku     	= jQuery(this).find('.pro-sku').text();
			var pro_no          = jQuery(this).find('.pro-no').text();
			var pro_title       = jQuery(this).find('.pro-title').text();
			var pro_qty       	= jQuery(this).find('.pro-qty').text();
			var pro_price       = jQuery(this).find('ins .woocommerce-Price-amount').html();

			if( typeof pro_price != 'string' ){
				pro_price  = jQuery(this).find('.woocommerce-Price-amount').html();
			}

			var xhtml = '';
			xhtml+= '<tr class="data_add_to_cart to-tmp-cart-id-'+pro_id+'" data-id="'+pro_id+'">';
					xhtml += '<td>'+pro_no+'</td>';
					xhtml += '<td>'+pro_sku+'</td>';
					xhtml += '<td class="product_name">'+pro_title+'</td>';
					xhtml += '<td class="qty" style="width: 70px;">';
					xhtml += '<input name="pro_qtys[]" class="qty_value" type="number"  value="'+pro_qty+'" min="0" style="width: 45px;">';
					xhtml += '<input name="pro_ids[]" type="hidden"  value="'+pro_id+'" min="0" style="display: none;">';
					xhtml += '</td>';
					xhtml += '<td class="price" style="width: 80px;">'+pro_price+'</td>';
					xhtml += ' <td><i class="fa fa-trash-o close_button" data-id="'+pro_id+'"></i></td>';
			xhtml+= '</tr>';
			
			console.log(xhtml);
			
			if( jQuery('.to-tmp-cart-id-'+pro_id).length > 0 ){
				
			}else{
				
				jQuery('#tbody_cart_parts').append(xhtml);
			}
			jQuery('#to-tmp-cart-form').show();
			
			jQuery('.btn-cart-action').removeClass('hide');
		});
		function remove_loading_ajax(){
			jQuery('#fancybox-loading').remove();
			jQuery('.fancybox-overlay').remove();
		}
		function add_loading_ajax(){
			var xhtml = '<div id="fancybox-loading"><div></div></div><div class="fancybox-overlay fancybox-overlay-fixed" style="width: auto; height: auto; display: block;"><div class="fancybox-wrap fancybox-desktop fancybox-type-ajax fancybox-tmp" tabindex="-1"><div class="fancybox-skin" style="padding: 15px;"><div class="fancybox-outer"><div class="fancybox-inner"></div></div></div></div></div>';
			jQuery('body').append(xhtml);
		}

	});
	</script>
	<?php
	return ob_get_clean();
}





function ajax_get_products(){
	$model_id  	= $_POST['model_id'];
	$year_id  	= $_POST['year_id'];
	$brand_id  	= $_POST['brand_id'];
	$taxonomy  	= @$_POST['taxonomy'];
	$products = Product_App::get_products_by_all_fields($brand_id,$model_id,$year_id);
	$item_html = '';
	$option_html = '<option value="">Catalog...</option>';
	if( count( $products ) > 0 ){
		
		foreach( $products as $product ){
			$option_html .= '<option value="'.$product->ID.'">'.$product->post_title.'</option>"';
			
			$thumbnail_id 	= get_post_thumbnail_id( $product->ID );
			$image 			= wp_get_attachment_url( $thumbnail_id );
			$img_title = $product->post_title;
			if( !$image ) $image = 'https://placeholdit.imgix.net/~text?txtsize=33&txt='.$img_title.'&w=337&h=215';
			ob_start();
			?>
				<li class="show-links-onimage product">
				   <div class="product-image">
					   <div class="product-item" data-id="<?php echo $product->ID;?>">
						 <div class="inner">
							<img width="300" height="300" src="<?php echo $image;?>" class=" wp-post-image" alt="" style="padding-top:0px;">
						</div>
						 <div class="links-on-image">
							 <div class="add-links-wrap">
								<div class="add-links  clearfix custom-links">
									<!--
									<a href="javascript:void(0);">Name: <?php echo $product->post_title ;?></a> <br>
									<a href="javascript:void(0);">Brand: Honda</a>
									<a href="javascript:void(0);">Model: Wave</a>
									<a href="javascript:void(0);">Year: 2016</a>
									-->
								</div>
							 </div>
						  </div>
					  </div>
				   </div>
				</li>
			<?php
			$item_html .= ob_get_clean();
		}
	}else{
		$item_html .= '<p class="woocommerce-info">Không tìm thấy sản phẩm nào khớp với lựa chọn của bạn.</p>';
	}
	
	$return['item_html'] 	= $item_html;
	$return['option_html']  = $option_html;
	echo json_encode($return);
	die();
}
function ajax_get_models(){
	$brand_id  	= $_POST['brand_id'];
	$models 	= Categories_App::get_models_by_brand( $brand_id );
	
	$item_html = $option_html = '';
	if( count( $models ) > 0 ){
		
		$option_html = '<option value="">Model...</option>';
		foreach( $models as $model ){
			$option_html .= '<option value="'.$model->term_id.'">'.$model->name.'</option>"';
			$thumbnail_id 	= get_woocommerce_term_meta( $model->term_id, 'thumbnail_id', true );
			
			$taxonomy 		= $model->taxonomy;
			$term_id 		= $model->term_id;
			$thumbnail_id 	= get_field('hinh_anh', $taxonomy. '_' . $term_id);
			if( $thumbnail_id ) $thumbnail_id	= $thumbnail_id['ID'];
			$image 			= wp_get_attachment_url( $thumbnail_id );
			
			$img_title = $model->name;
			if( !$image ) $image = 'https://placeholdit.imgix.net/~text?txtsize=33&txt='.$img_title.'&w=337&h=215';
			ob_start();
			?>
				<li class="show-links-onimage product">
				   <div class="product-image">
					   <div class="model-item" data-id="<?php echo $model->term_id;?>">
							<div class="inner">
								<img width="300" height="300" src="<?php echo $image;?>" class=" wp-post-image" alt="" style="padding-top:0px;">
							</div>
							<div class="links-on-image">
								 <div class="add-links-wrap">
									<div class="add-links  clearfix custom-links">
										<a href="javascript:void(0);">Model: <?php echo $model->name;?></a><br>
									</div>
								 </div>
							</div>
					  </div>
				   </div>
				</li>
			<?php
			$item_html .= ob_get_clean();
		}
	}else{
		$item_html .= '<p class="woocommerce-info">Không tìm thấy mục nào khớp với lựa chọn của bạn.</p>';
	}
	$return['item_html'] 	= $item_html;
	$return['option_html']  = $option_html;
	echo json_encode($return);
	die();
}

function ajax_get_years(){
	$model_id  	= $_POST['model_id'];
	$years 	= Categories_App::get_years_by_model( $model_id );
	
	$item_html = '';
	$option_html = '<option value="">Year...</option>';
	if( count( $years ) > 0 ){
		ob_start();
		foreach( $years as $year ){
			$option_html .= '<option value="'.$year->term_id.'">'.$year->name.'</option>"';
			$thumbnail_id 	= get_woocommerce_term_meta( $year->term_id, 'thumbnail_id', true );
			
			$taxonomy 		= $year->taxonomy;
			$term_id 		= $year->term_id;
			$thumbnail_id 	= get_field('hinh_anh', $taxonomy. '_' . $term_id);
			if( $thumbnail_id ) $thumbnail_id	= $thumbnail_id['ID'];
			$image 			= wp_get_attachment_url( $thumbnail_id );
			
			$img_title = $year->name;
			if( !$image ) $image = 'https://placeholdit.imgix.net/~text?txtsize=33&txt='.$img_title.'&w=337&h=215';
			?>
				<li class="show-links-onimage product">
				   <div class="product-image">
					   <div class="year-item" data-id="<?php echo $year->term_id;?>">
							<div class="inner">
								<img width="300" height="300" src="<?php echo $image;?>" class=" wp-post-image" alt="" style="padding-top:0px;">
							</div>
							<div class="links-on-image">
								 <div class="add-links-wrap">
									<div class="add-links  clearfix custom-links">
										<a href="javascript:void(0);">Year: <?php echo $year->name;?></a><br>
									</div>
								 </div>
							</div>
					  </div>
				   </div>
				</li>
			<?php
			$item_html .= ob_get_clean();
		}
	}else{
		$item_html .= '<p class="woocommerce-info">Không tìm thấy mục nào khớp với lựa chọn của bạn.</p>';
	}
	$return['item_html'] 	= $item_html;
	$return['option_html']  = $option_html;
	echo json_encode($return);
	die();
}

function ajax_get_brands(){
	$brands = Categories_App::get_brands();
	$xhtml = '';
	ob_start();
	if( count( $brands ) > 0 ){
		foreach( $brands as $brand ){
			$taxonomy 		= $brand->taxonomy;
			$term_id 		= $brand->term_id;
			$thumbnail_id 	= get_field('hinh_anh', $taxonomy. '_' . $term_id);
			if( $thumbnail_id ) $thumbnail_id	= $thumbnail_id['ID'];
			
			
			//$thumbnail_id = get_woocommerce_term_meta( $brand->term_id, 'thumbnail_id', true );
			$image = wp_get_attachment_url( $thumbnail_id );
			
			$img_title = $brand->name;
			if( !$image ) $image = 'https://placeholdit.imgix.net/~text?txtsize=33&txt='.$img_title.'&w=337&h=215';
			?>
				<li class="show-links-onimage product">
				   <div class="product-image">
					  <div class="brand-item" data-id="<?php echo $brand->term_id;?>">
						 <div class="inner">
							<img width="300" height="300" src="<?php echo $image;?>" class=" wp-post-image" alt="">
						</div>
						<div class="links-on-image">
						 <div class="add-links-wrap">
							<div class="add-links  clearfix custom-links">
								<a href="javascript:void(0);">Brand: <?php echo $brand->name;?></a>
							</div>
						 </div>
					  </div>
					  </div>
					  
				   </div>
				</li>
			<?php
		}
	}else{
		?>
		<p class="woocommerce-info">Không tìm thấy mục nào khớp với lựa chọn của bạn.</p>
		<?php
	}
	echo ob_get_clean();
	die();
}