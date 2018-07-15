<?php 
class Categories_App {
	
	public static function product_cats( $options = array() ){
		$parrams = array(
			'taxonomy' => 'product_cat',
			'hide_empty' => false,
		);
		$parrams = array_merge( $parrams,$options );
		$product_cats = get_terms( $parrams );
		return $product_cats;
	}
	
	public static function get_brands(){
		
		$parrams = array(
			'taxonomy' => 'nhan-hieu',
			'hide_empty' => false,
		);
		$brands = get_terms( $parrams );
		return $brands;
	} 
	public static function get_models(){
		$parrams = array(
			'taxonomy' => 'mau-ma',
			'hide_empty' => false,
		);
		$models = get_terms( $parrams );
		return $models;
	}
	
	public static function get_brand_of_term( $term_id ){
		$parents = get_ancestors( $term_id, 'product_cat' );
		$brand_id = 0;
		if( count( $parents ) > 0 ){
			foreach( $parents as $parent_id ){
				$taxonomy 		= 'product_cat';
				$term_id 		= $parent_id;  
				$use_as 		= get_field('use_as', $taxonomy. '_' . $term_id);
				if( $use_as == 'nhanhieu' ){
					$brand_id = $parent_id;
				}
			}
		}
		return $brand_id;
	}
	
	public static function get_model_of_term( $term_id ){
		$parents = get_ancestors( $term_id, 'product_cat' );
		$model_id = 0;
		if( count( $parents ) > 0 ){
			foreach( $parents as $parent_id ){
				$taxonomy 		= 'product_cat';
				$term_id 		= $parent_id;  
				$use_as 		= get_field('use_as', $taxonomy. '_' . $term_id);
				if( $use_as == 'model' ){
					$model_id = $parent_id;
				}
			}
		}
		return $model_id;
	}
	
	public static function get_models_by_brand( $brand_id ){
		
		$products = get_posts(array(
		  'post_type' => 'product',
		  'numberposts' => -1,
		  'tax_query' => array(
			array(
			  'taxonomy' => 'nhan-hieu',
			  'field' => 'id',
			  'terms' => $brand_id, 
			  'include_children' => false
			)
		  )
		));
		$args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all');
		$multi_models = array();
		if( count( $products ) > 0 ){
			foreach( $products as $product ){
				$multi_models[] = wp_get_post_terms( $product->ID, 'mau-ma', $args );
			}
		}
		
		$models = array();
		if( count( $multi_models ) > 0 ){
			foreach( $multi_models as $multi_model_arr ){
				foreach( $multi_model_arr as $multi_model ){
					$models[$multi_model->term_id] = $multi_model;
				}
			}
		}
		return $models;
	}
	
	public static function get_years_by_model( $model_id ){
		
		$products = get_posts(array(
		  'post_type' => 'product',
		  'numberposts' => -1,
		  'tax_query' => array(
			array(
			  'taxonomy' => 'mau-ma',
			  'field' => 'id',
			  'terms' => $model_id, 
			  'include_children' => false
			),
			'meta_query' => array(
				array(
					'key'     => 'use_for_phu_tung',
					'value'   => 'YES',
					'compare' => '=',
				),
			),
		  )
		));
		$args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all');
		$multi_years = array();
		if( count( $products ) > 0 ){
			foreach( $products as $product ){
				$multi_years[] = wp_get_post_terms( $product->ID, 'nam', $args );
			}
		}
		$years = array();
		if( count( $multi_years ) > 0 ){
			foreach( $multi_years as $multi_year_arr ){
				foreach( $multi_year_arr as $multi_year ){
					$years[$multi_year->term_id] = $multi_year;
				}
			}
		}
		return $years;
	}
}