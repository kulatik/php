<?php
$result = wp_get_recent_posts( [
	'numberposts'      => 3,
	'offset'           => 0,
	'category'         => langauge,
	'orderby'          => 'post_date',
	'order'            => 'DESC',
	'include'          => '',
	'exclude'          => '',
	'meta_key'         => '',
	'meta_value'       => '',
	'post_type'        => 'post',
	'post_status'      => 'draft, publish, future, pending, private',
	'suppress_filters' => true,
], OBJECT );
foreach( $result as $post ){
	setup_postdata( $post );
	the_title(); 
}
wp_reset_postdata();