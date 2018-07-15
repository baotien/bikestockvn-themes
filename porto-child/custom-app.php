<?php
	$includes = array(
		'ajax-app.php',
		'product-app.php',
		'categories-app.php',
		'helper-app.php',
		'shortcode-app.php',
		'hook-app.php'
	);
	foreach( $includes as $file){
		$dir = dirname( __FILE__ ).'/custom-app/';
		if( file_exists( $dir.$file ) ){
			include_once $dir.$file;	
		}
	}