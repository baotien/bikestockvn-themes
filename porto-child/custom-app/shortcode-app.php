<?php
add_shortcode( 'ADVANCE_WOO_SEARCH', 'advance_searh_woo_short_code' );
function advance_searh_woo_short_code(){
		$brands = Categories_App::get_brands();
		$models = Categories_App::get_models();
		ob_start();
		?>
		<style type="text/css">
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
		<div style="margin-top: 30px;margin-bottom:30px;" class="row">
			<div class="col-lg-12">
				<div class="top_search_bar_label">E-catelogue Search Bar</div>
			</div>
			<div class="col-lg-3">
	            <select name="brand_id" class="form-control brand" id="brand">
	                <option value="">Nhãn Hiệu</option>
	                <?php
						if( count($brands ) > 0 ){
							foreach( $brands as $brand ){
								echo '<option value="'.$brand->term_id.'">'.$brand->name.'</option>';
							}
						}
					?>  
	            </select>
		  </div>
		   <div class="col-lg-3">
			  <select name="model_id" class="form-control model" id="model" placeholder="Model...">
				 <option value="">Model...</option>
			  </select>
			  <a class="back_arrow" title="Click Here to go back to Model." id="back_to_model"> 
			  <i class="fa fa-backward"></i></a>
		  </div>
		  <div class="col-lg-2">
			  <select name="year_selection" class="form-control model" id="year" placeholder="Model...">
				 <option value="">Year...</option>
			  </select>
			  <a class="back_arrow" title="Click Here to go back to Models." id="back_to_year"> 
			  <i class="fa fa-backward"></i></a>
		  </div>
		 
		  
		  <div class="col-lg-4">
			  <select name="illustration_id" class="form-control illustration" id="illustration">
				 <option value="">Catalog...</option>
			  </select>
			  <a class="back_arrow" title="Click Here to go back to Illustration." id="back_to_illus"> 
			  <i class="fa fa-backward"></i></a>
		  </div>
		</div>
		
		<div class="row" style="margin-bottom:20px;">
			<div class="col-lg-8"></div>
			<div class="col-lg-4">
				<div class="input-group">
				 <input class="form-control spear_part_search_input " title="After Enter Part Number please press Enter Key." placeholder="Search By Part Number...." id="search_by_spearpart"> 
				  <span class="input-group-btn">
					<button class="btn btn-default" id="search_part" type="button">TÌM KIẾM</button>
				  </span>
				</div><!-- /input-group -->
			</div>
	    </div>
		
		<div class="row" style="margin-bottom:20px;">
			<div class="col-lg-12">
				<div class="archive-products advance_searh_woo_wrapper">
					<ul class="products grid pcols-lg-4 pcols-md-3 pcols-xs-2 pcols-ls-1 pwidth-lg-4 pwidth-md-3 pwidth-xs-2 pwidth-ls-1">
					
					</ul>
				</div>
			</div>
		</div>
		<script>
			jQuery( document ).ready( function(){
				var ajax_url = '<?php echo home_url();?>/wp-admin/admin-ajax.php';
				
				/* SELECTED PARRAMS */
				
				get_brands();

				
				
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
								jQuery('#tbody_cart_parts').html('<span class="text-center tmp-cart-msg">Sản phẩm đã được thêm vào Giỏ Hàng</span>');
								jQuery('.btn-cart-action-link').removeClass('hide');
								jQuery('#btn-add-all-to-cart').addClass('hide');
							}
							remove_loading_ajax();
						});
					}else{
						alert('Vui lòng chọn ít nhất một sản phẩm!');
					}
					
					
				});
				
				

				jQuery('body').on('hover','.hotspot-item, .point_style .show_popup_content',function(){
					var dta_id = jQuery(this).attr('data-id');
					jQuery('.hotspot-item').removeClass('active');
					jQuery('.hotspot-item.hotspot-item-id-'+dta_id).addClass('active');

					jQuery('.point_style .show_popup_content').removeClass('p_active');
					jQuery('.point_style a[data-id="'+dta_id+'"]').addClass('p_active');
				});
				
				
				
				jQuery('body').on('click','.product-item',function(){
					var pro_id = jQuery(this).attr('data-id');
					if( pro_id ){
						jQuery('#illustration option[value="'+pro_id+'"]').prop('selected',true);
						get_product_item( pro_id );
					}
					
				});
				
				
				
				jQuery('body').on('click','.model-item',function(){
					var model_id = jQuery(this).attr('data-id');
					jQuery('#model option[value="'+model_id+'"]').prop('selected',true);
					get_years( model_id );
				});
				
				jQuery('body').on('click','.year-item',function(){
					var year_id = jQuery(this).attr('data-id');
					var model_id = jQuery('#model').val();
					var brand_id = jQuery('#brand').val();
					jQuery('#year option[value="'+year_id+'"]').prop('selected',true);
					get_products( brand_id,model_id,year_id );
				});
				
				jQuery('body').on('click','.brand-item',function(){
					jQuery('#illustration').html( '<option value="">Catalog...</option>' );
					jQuery('#model').html( '<option value="">Model...</option>' );
					var brand_id = jQuery(this).attr('data-id');
					if( brand_id ){
						get_models( brand_id );
						jQuery('#brand option[value="'+brand_id+'"]').prop('selected',true);
					}else{
						get_brands();
					}
				});
				
				jQuery('body').on('change','#brand',function(){
					jQuery('#illustration').html( '<option value="">Catalog...</option>' );
					jQuery('#model').html( '<option value="">Model...</option>' );
					var brand_id = jQuery(this).val();
					if( brand_id ){
						get_models( brand_id );
					}else{
						get_brands();
					}
				});
				jQuery('body').on('change','#year',function(){
					var year_id  = jQuery(this).val();
					if( year_id ){
						var year_id  = jQuery(this).val();
						var model_id = jQuery('#model').val();
						var brand_id = jQuery('#brand').val();
						jQuery('#year option[value="'+year_id+'"]').prop('selected',true);
						get_products( brand_id,model_id,year_id );
					}else{
						jQuery('#back_to_year').trigger('click')
					}		
				});
				jQuery('body').on('change','#model',function(){
					var model_id = jQuery(this).val();
					if( model_id ){
						get_years( model_id );
					}else{
						jQuery('#back_to_model').trigger('click')
					}
				});
				
				jQuery('body').on('change','#illustration',function(){
					var pro_id = jQuery(this).val();
					if( pro_id ){
						get_product_item( pro_id );
					}else{
						jQuery('#back_to_illus').trigger('click')
					}
				});
				
				jQuery('body').on('click','#back_to_illus',function(){
					jQuery('#illustration option').prop('selected',false);
					var year_id =  jQuery('#year').val();
					if( year_id ){
						jQuery('#year option[value="'+year_id+'"]').prop('selected',true);
						var year_id =  jQuery('#year').val();
						var model_id = jQuery('#model').val();
						var brand_id = jQuery('#brand').val();
						jQuery('#year option[value="'+year_id+'"]').prop('selected',true);
						get_products( brand_id,model_id,year_id );
					}
					
				});
				
				jQuery('body').on('click','#back_to_year',function(){
					jQuery('#year option').prop('selected',false);
					var model_id 	= jQuery('#model').val();
					if( model_id ){
						jQuery('#model option[value="'+model_id+'"]').prop('selected',true);
						get_years( model_id );
						
					}
					
				});
				
				jQuery('body').on('click','#back_to_model',function(){
					jQuery('#model option').prop('selected',false);
					jQuery('#year').html('<option value="">Year...</option>');
					jQuery('#illustration').html('<option value="">Catalog...</option>');
					var brand_id = jQuery('#brand').val();
					if( brand_id ){
						get_models( brand_id );
					}else{
						get_brands();
					}
				});
				
				jQuery('body').on('click','#search_part',function(){
					add_loading_ajax();
					var s = jQuery('.spear_part_search_input').val();
					if( s.length > 0 ){
						var dta = {
							'action' 	: 'ajax_search_by_part',
							's' 		: s
						};
						jQuery.ajax({
							type: "POST",
							url: ajax_url,
							data: dta,
							dataType: "html"
						})
						.done(function( respon ) {
							jQuery('.archive-products .products').html( respon );
							remove_loading_ajax();
						});
					}else{
						remove_loading_ajax();
						alert( 'Vui lòng nhập ký tự' );
						
					}
				});
				
				
				/* MAIN */
				
				function get_product_item( pro_id ){
					add_loading_ajax();
					var dta = {
						'action' 	: 'ajax_get_product_item',
						'pro_id' 	: pro_id
					};
					jQuery.ajax({
						type: "POST",
						url: ajax_url,
						data: dta,
						dataType: "html"
					})
					.done(function( respon ) {
						jQuery('.archive-products .products').html( respon );
						remove_loading_ajax();
						jQuery('.scrollbar-inner').scrollbar();
					});
				}
				
				function get_brands(){
					add_loading_ajax();
					var dta = {
						'action' 	: 'ajax_get_brands',
					};
					jQuery.ajax({
						type: "POST",
						url: ajax_url,
						data: dta,
						dataType: "html"
					})
					.done(function( respon ) {
						jQuery('.archive-products .products').html( respon );
						remove_loading_ajax();
					});
				}
				
				function get_products(  brand_id,model_id, year_id ){
					add_loading_ajax();
					jQuery('#illustration').html( '<option value="">Loading...</option>' );
					var dta = {
						'action' 	: 'ajax_get_products',
						'brand_id'  : brand_id,
						'model_id'  : model_id,
						'year_id'   : year_id
					};
					jQuery.ajax({
						type: "POST",
						url: ajax_url,
						data: dta,
						dataType: "json"
					})
					.done(function( respon ) {
						jQuery('#illustration').html( respon.option_html );
						jQuery('.archive-products .products').html( respon.item_html );
						remove_loading_ajax();
					});
				}

				

				
				/* FORM */

				function get_years( model_id ){
					add_loading_ajax();
					jQuery('#year').html( '<option value="">Loading...</option>' );
					var dta = {
						'action' 	: 'ajax_get_years',
						'model_id'  : model_id
					};
					jQuery.ajax({
						type: "POST",
						url: ajax_url,
						data: dta,
						dataType: "json"
					})
					.done(function( respon ) {
						jQuery('#year').html( respon.option_html );
						jQuery('.archive-products .products').html( respon.item_html );
						remove_loading_ajax();						
					});
				}
				function get_models( brand_id ){
					add_loading_ajax();
					jQuery('#model').html( '<option value="">Loading...</option>' );
					var dta = {
						'action' 	: 'ajax_get_models',
						'brand_id'  : brand_id
					};
					jQuery.ajax({
						type: "POST",
						url: ajax_url,
						data: dta,
						dataType: "json"
					})
					.done(function( respon ) {
						jQuery('#model').html( respon.option_html );
						jQuery('.archive-products .products').html( respon.item_html );
						remove_loading_ajax();
						
						
					});
				}
				
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