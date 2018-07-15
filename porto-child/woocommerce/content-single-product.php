<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * @version     3.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<?php
	/**
	 * woocommerce_before_single_product hook.
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );
	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
global $porto_layout;
$post_class = join( ' ', get_post_class() );
if ($porto_layout === 'widewidth' || $porto_layout === 'wide-left-sidebar' || $porto_layout === 'wide-right-sidebar') {
    $post_class .= 'm-t-lg m-b-xl';
    if (porto_get_wrapper_type() !=='boxed')
        $post_class .= ' m-r-md m-l-md';
}
$loai_san_pham = get_post_meta( get_the_ID(),'loai_san_pham',true );

?>
<!-- woocommerce_get_product_schema DEPRECATED with NO altertative in 3.0.0 -->
<div itemscope itemtype="<?php //echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" class="<?php echo $post_class ?>">
    <div class="product-summary-wrap">
        <div class="row">
            <?php if( get_field('image_hotspot') ): ?>
                <?php
                    $image_hotspots = get_field('image_hotspot');
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
                        $json           = str_replace("\\","",$json);
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
							<thead>
								<tr style="color: #333;">
									<th width="10%" style="border-top-left-radius: 5px;">No</th>
									<th width="25%">Part No</th>
									<th width="35%">Name</th>
									<th width="10%">Sug.Q</th>
									<th width="20%" style="border-top-right-radius: 5px;">Price</th>
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
											<td width="25%"><span class="pro-sku"><?php echo $sku;?></span></td>
											<td width="35%"><span class="pro-title"><?php echo $p_data_title;?></span></td>
											<td width="10%"><span class="pro-qty"><?php echo $sg_qty;?></span></td>
											<td width="20%"><span class="pro-price"><?php echo $price;?></td>
										</tr>
										
									   <?php
									   
									}
								?>
							</tbody>
						</table>
                        <?php  endif;?>
                       
                    </div>
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
                <?php endif; ?>
            <?php else: ?>
                <div class="col-md-4 summary-before">
                    <?php
                        do_action( 'woocommerce_before_single_product_summary' );
                    ?>
                </div>

                <div class="col-md-5 summary entry-summary">
                    <?php
                        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
                        add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );

                        do_action( 'woocommerce_single_product_summary' );
                    ?>
                </div>
				<div class="col-md-3 summary-text-note">
                    <div class="wpb_wrapper vc_column-inner">
						<h2 style="text-align: left" class="vc_custom_heading widget-title">Chính sách đặt hàng</h2>
						<div class="wpb_text_column wpb_content_element ">
							<div class="wpb_wrapper">
								<ol>
									<li><strong>Để xác nhận đơn hàng, bạn được đề nghị chuyển khoản</strong></li>
									<li><strong>Chúng tôi sẽ liên hệ với bạn trong vòng 24 giờ</strong></li>
									<li><strong>Hàng về chúng tôi sẽ ship COD đến tận nhà bạn nếu bạn có nhu cầu</strong></li>
									<li><strong>Phí ship/COD bạn thanh toán theo cước Bưu điện.<br></strong></li>
								</ol>
							</div>
						</div>
					</div>
                </div>
            <?php endif; ?>
        </div>
    </div><!-- .summary -->
	<?php
		/**
		 * woocommerce_after_single_product_summary hook.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		 if( $loai_san_pham != 'cha' ){
			do_action( 'woocommerce_after_single_product_summary' );
		 }
		
	?>
	<meta itemprop="url" content="<?php the_permalink(); ?>" />
</div><!-- #product-<?php the_ID(); ?> -->
<?php do_action( 'woocommerce_after_single_product' ); ?>

<?php if( $loai_san_pham == 'cha' ): ?>
<style type="text/css">
	.quickview-add-to-tmp-cart {
		cursor: pointer;
	}
	.quickview-add-to-tmp-cart.h-hover, .quickview-add-to-tmp-cart:hover {
		background: #fec700;
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
    jQuery( '.hs-poly-svg' ).ready( function(){
        console.log('zo');
        console.log( jQuery('.imp-shape'));
    });
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
            
            var pro_id      = jQuery(this).attr('data-id');
            var sku          = jQuery(this).find('.pro-sku').text();
            var no          = jQuery(this).find('.pro-no').text();
            var title       = jQuery(this).find('.pro-title').text();
            var qty       = jQuery(this).find('.pro-qty').text();
            var price       = jQuery(this).find('ins .woocommerce-Price-amount').html();
            
            
    
            if( typeof price != 'string' ){
                price  = jQuery(this).find('.woocommerce-Price-amount').html();
            }
            
            var xhtml = '';
            xhtml+= '<tr  class="data_add_to_cart to-tmp-cart-id-'+pro_id+'" data-id="'+pro_id+'">';
                    xhtml+= '<td >'+no+'</td>';
                    xhtml+= '<td >'+sku+'</td>';
                    xhtml+= ' <td class="product_name" style="width: 325px;">'+title+'</td>';
                    xhtml+= ' <td class="qty" style="width: 70px;">';
                    xhtml+= '<input name="pro_qtys[]" class="qty_value" type="number"  value="'+qty+'" min="0" style="width: 45px;">';
                    xhtml+= '<input name="pro_ids[]" type="hidden"  value="'+pro_id+'" min="0" style="display: none;">';
                    xhtml+= '</td>';
                    xhtml+= '<td class="price" style="width: 80px;">'+price+'</td>';
                    xhtml+= ' <td><i class="fa fa-trash-o close_button" data-id="'+pro_id+'"></i></td>';
            xhtml+= '</tr>';
            
            if( jQuery('.to-tmp-cart-id-'+pro_id).length > 0 ){
                
            }else{
				jQuery('#to-tmp-cart-form').show();
                jQuery('#tbody_cart_parts').append(xhtml);
            }
			
			
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
<?php endif;?>


