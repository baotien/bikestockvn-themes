<?php 
if( !function_exists('pr') ){
	function pr( $array ){
		echo '<pre>';
		print_r($array);
		echo '</pre>';
	}
}