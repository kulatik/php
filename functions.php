<?php
/*
 * sp functions and definitions
 */

if ( ! function_exists( 'sp_setup' ) ) :

	function sp_setup() {

		load_theme_textdomain( 'sp-theme', get_template_directory() . '/languages' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );


		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'sp-theme' ),
		) );

		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );


		add_theme_support( 'custom-background', apply_filters( 'sp_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		)));

		add_theme_support( 'customize-selective-refresh-widgets' );

		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;

add_action( 'after_setup_theme', 'sp_setup' );


function sp_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'sp_content_width', 640 );
}
add_action( 'after_setup_theme', 'sp_content_width', 0 );

//Register widget area.
function sp_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'sp-theme' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'sp-theme' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	));
}
add_action( 'widgets_init', 'sp_widgets_init' );

//Enqueue scripts and styles.
function sp_scripts() {
	
	wp_enqueue_style('bootstrap', get_template_directory_uri().'/css/bootstrap.css');
	wp_enqueue_style('cms-style', get_stylesheet_uri() );
	wp_enqueue_style('mediascreen', get_template_directory_uri().'/css/mediascreen.css');
	wp_enqueue_style('swiper', get_template_directory_uri().'/css/swiper.css');
	wp_enqueue_style('animate', get_template_directory_uri().'/css/animate.css');
	wp_enqueue_style('fontawesome', get_template_directory_uri().'/css/fontawesome-all.css');

	wp_enqueue_script('jquery');
	wp_enqueue_script('share', get_template_directory_uri().'/js/share.js');
	wp_enqueue_script('swiper', get_template_directory_uri().'/js/swiper.js');
	wp_enqueue_script('wow', get_template_directory_uri().'/js/wow.js');
	wp_enqueue_script('lazyload', get_template_directory_uri().'/js/lazyload.js');
	wp_enqueue_script('script', get_template_directory_uri().'/js/script.js');
	wp_enqueue_script('social-burger', get_template_directory_uri().'/scripts/social-burger.js');
	wp_localize_script('script', 'myajax', 
	array(
		'url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('myajax-nonce')
	)
);  
	

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'sp_scripts' );

//Include the TGM_Plugin_Activation class.
require get_template_directory() . '/inc/class-tgm-plugin-activation.php';

//Implement the Custom Header feature.
require get_template_directory() . '/inc/custom-header.php';

//Functions which enhance the theme by hooking into WordPress.
require get_template_directory() . '/inc/sp-class.php';

//Functions which enhance the theme by hooking into WordPress.
require get_template_directory() . '/inc/template-functions.php';

//Customizer additions.
require get_template_directory() . '/inc/customizer.php';

	
function burger_menu_scripts() {
 
 wp_enqueue_script( 'burger-menu-script', get_stylesheet_directory_uri() . '/scripts/burger.js', array( 'jquery' ) );
 
}
add_action( 'wp_enqueue_scripts', 'burger_menu_scripts' );







function shapeSpace_popular_posts($post_id) {
	$count_key = 'popular_posts';
	$count = get_post_meta($post_id, $count_key, true);
	if ($count == '') {
		$count = 0;
		delete_post_meta($post_id, $count_key);
		add_post_meta($post_id, $count_key, '0');
	} else {
		$count++;
		update_post_meta($post_id, $count_key, $count);
	}
}
function shapeSpace_track_posts($post_id) {
	if (!is_single()) return;
	if (empty($post_id)) {
		global $post;
		$post_id = $post->ID;
	}
	shapeSpace_popular_posts($post_id);
}
add_action('wp_head', 'shapeSpace_track_posts');

function post_like_table_create() {

	global $wpdb;
	$table_name = $wpdb->prefix. "post_like_table";
	global $charset_collate;
	$charset_collate = $wpdb->get_charset_collate();
	global $db_version;
	
	if( $wpdb->get_var("SHOW TABLES LIKE '" . $table_name . "'") != $table_name)
	{ $create_sql = "CREATE TABLE " . $table_name . " (
	id INT(11) NOT NULL auto_increment,
	postid INT(11) NOT NULL ,
	
	clientip VARCHAR(40) NOT NULL ,
	
	PRIMARY KEY (id))$charset_collate;";
	require_once(ABSPATH . "wp-admin/includes/upgrade.php");
	dbDelta( $create_sql );
	}
	
	
	
	//register the new table with the wpdb object
	if (!isset($wpdb->post_like_table))
	{
	$wpdb->post_like_table = $table_name;
	//add the shortcut so you can use $wpdb->stats
	$wpdb->tables[] = str_replace($wpdb->prefix, '', $table_name);
	}
	
	}
	add_action( 'init', 'post_like_table_create');
	
	// Add the JS
	function theme_name_scripts() {
	wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/post-like.js', array('jquery'), '1.0.0', true );
	wp_localize_script( 'script-name', 'MyAjax', array(
	// URL to wp-admin/admin-ajax.php to process the request
	'ajaxurl' => admin_url( 'admin-ajax.php' ),
	// generate a nonce with a unique ID "myajax-post-comment-nonce"
	// so that you can check it later when an AJAX request is sent
	'security' => wp_create_nonce( 'my-special-string' )
	));
	}
	add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );
	// The function that handles the AJAX request
	
	function get_client_ip() {
	$ip=$_SERVER['REMOTE_ADDR']; 
	return $ip;
	}
	
	function my_action_callback() {
	check_ajax_referer( 'my-special-string', 'security' );
	$postid = intval( $_POST['postid'] );
	$clientip=get_client_ip();
	$like=0;
	$dislike=0;
	$like_count=0;
	//check if post id and ip present
	global $wpdb;
	$row = $wpdb->get_results( "SELECT id FROM $wpdb->post_like_table WHERE postid = '$postid' AND clientip = '$clientip'");
	if(empty($row)){
	//insert row
	$wpdb->insert( $wpdb->post_like_table, array( 'postid' => $postid, 'clientip' => $clientip ), array( '%d', '%s' ) );
	//echo $wpdb->insert_id;
	$like=1;
	}
	if(!empty($row)){
	//delete row
	$wpdb->delete( $wpdb->post_like_table, array( 'postid' => $postid, 'clientip'=> $clientip ), array( '%d','%s' ) );
	$dislike=1;
	}
	
	//calculate like count from db.
	$totalrow = $wpdb->get_results( "SELECT id FROM $wpdb->post_like_table WHERE postid = '$postid'");
	$total_like=$wpdb->num_rows;
	$data=array( 'postid'=>$postid,'likecount'=>$total_like,'clientip'=>$clientip,'like'=>$like,'dislike'=>$dislike);
	echo json_encode($data);
	//echo $clientip;
	die(); // this is required to return a proper result
	}
	add_action( 'wp_ajax_my_action', 'my_action_callback' );
	add_action( 'wp_ajax_nopriv_my_action', 'my_action_callback' );