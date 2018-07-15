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
		global $wpdb;
		$sql = "SELECT DISTINCT `nhan_hieu` FROM `chi_tiet_dong_xe`";
		$results = $wpdb->get_results($sql);
		$brands = array();
		if( count($results) > 0 ){
			foreach( $results as $result ){
				$brands[] = $result->nhan_hieu;
			}
		}
		return $brands;
		
		$parrams = array(
			'taxonomy' => 'nhan-hieu',
			'hide_empty' => false,
		);
		$brands = get_terms( $parrams );
		return $brands;
	} 
	public static function get_models($parent_id = 0){
		$parrams = array(
			'taxonomy' => 'product_cat',
			'hide_empty' => false,
			'parent' => $parent_id,
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
	
		global $wpdb;
		$sql = "SELECT `dong_xe` FROM `chi_tiet_dong_xe` WHERE `nhan_hieu` = '".$brand_id."'";
		$results = $wpdb->get_results($sql);
		$models = array();
		if( count($results) > 0 ){
			foreach( $results as $result ){
				$cat = get_term_by( 'name', $result->dong_xe, 'product_cat' );
				if($cat){
					$models[] 	= $cat;
				}
			}
		}
		$models = $models;
		return $models;
	}
	
	public static function get_years_by_model( $model_id ){
	
		$cat = get_term($model_id,'product_cat');
		$cat_name = $cat->name;
		global $wpdb;
		$sql = "SELECT `nam` FROM `chi_tiet_dong_xe` WHERE `dong_xe` = '".$cat_name."'";
		$years = $wpdb->get_results($sql);
		$multi_years = array();
		if( count($years) > 0 ){
			foreach( $years as $year ){
				$multi_years[] = $year->nam;
			}
		}
		return $multi_years;
		
		die();
		
		$products = get_posts(array(
		  'post_type' => 'product',
		  'numberposts' => -1,
		  'tax_query' => array(
			array(
			  'taxonomy' => 'product_cat',
			  'field' => 'id',
			  'terms' => $model_id, 
			  'include_children' => false
			),
			'meta_query' => array(
				array(
					'key'     => 'loai_san_pham',
					'value'   => 'cha',
					'compare' => '=',
				),
			)
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