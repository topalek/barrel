<?php
if(isset($_POST['adress'])):
$home_dir = explode('wp-content', dirname(__FILE__), 2);
$home_dir = $home_dir[0];

require( $home_dir . '/wp-blog-header.php' );

require_once(dirname(__FILE__). '/GoogleMaper.php');

	$GoogleMaper = new GoogleMaper(array('address' 	=> trim( $_POST['adress'] )));
	echo $GoogleMaper->map;
	
endif;