<?php 
class Product_App {
	public static function get_products_by_category( $cat_id ,$taxonomy  = 'nam'){
		if( !$taxonomy ) $taxonomy = 'nam';
		$args = array(
			'posts_per_page' => -1,
			'post_type' => 'product',
			'tax_query' => array(
				array(
					'taxonomy' => $taxonomy,
					'field' => 'term_id',
					'terms' => $cat_id,
				)
			),
			'meta_query' => array(
				array(
					'key'     => 'image_hotspot',
					'value'   => '',
					'compare' => '!=',
				),
			),
		);
			
		$args['orderby'] 	= 'title';
		$args['order'] 		= 'DESC';
		$products = get_posts( $args );
		return $products;
	}
	public static function get_products_by_all_fields($brand_id,$model_id,$year_id = 0){
		
		$args = array(
				'posts_per_page' => -1,
				'post_type' => 'product',
				'tax_query' => array(
				   'relation' => 'AND',
					// array(
					  // 'taxonomy' => 'nhan-hieu',
					  // 'field'    => 'term_id',
					  // 'terms'    => $brand_id,
					  // 'operator' => 'IN',
					// ),
					array(
					  'taxonomy' => 'product_cat',
					  'field'    => 'term_id',
					  'terms'    => $model_id,
					  'operator' => 'IN',
					)
				),
				'meta_query' => array(
					array(
						'key'     => 'loai_san_pham',
						'value'   => 'cha',
						'compare' => '=',
					),
				)
			);
			
		if( $year_id ){
			$args['tax_query'][2] = array(
					  'taxonomy' => 'nam',
					  'field'    => 'term_id',
					  'terms'    => $year_id,
					  'operator' => 'IN',
			);
		}
		// pr($args);
		// $query = new WP_Query( $args );
		// pr($query);
		// die();
		
		$args['orderby'] 	= 'title';
		$args['order'] 		= 'DESC';

		$products = get_posts($args);
		return $products;
	}
}
