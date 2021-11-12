<?php
/**
 * MU AZ Index
 *
 * This plugin is to manage and display Marshall University's A-Z Index
 *
 * @package  MU AZ Index
 *
 * Plugin Name:  MU AZ Index
 * Plugin URI: https://www.marshall.edu
 * Description: This plugin is to manage and display Marshall University's A-Z Index
 * Version: 1.0
 * Author: Christopher McComas
 */

if ( ! class_exists( 'ACF' ) ) {
	return new WP_Error( 'broke', __( 'Advanced Custom Fields is required for this plugin.', 'my_textdomain' ) );
}

require plugin_dir_path( __FILE__ ) . '/acf-fields.php';

/**
 * Register a custom post type called "mu-weblink".
 *
 * @see get_post_type_labels() for label keys.
 */
function mu_program_page_type() {
	$labels = array(
		'name'                  => _x( 'Links', 'Post type general name', 'textdomain' ),
		'singular_name'         => _x( 'Link', 'Post type singular name', 'textdomain' ),
		'menu_name'             => _x( 'Links', 'Admin Menu text', 'textdomain' ),
		'name_admin_bar'        => _x( 'Link', 'Add New on Toolbar', 'textdomain' ),
		'add_new'               => __( 'Add New', 'textdomain' ),
		'add_new_item'          => __( 'Add New Link', 'textdomain' ),
		'new_item'              => __( 'New Link', 'textdomain' ),
		'edit_item'             => __( 'Edit Link', 'textdomain' ),
		'view_item'             => __( 'View Link', 'textdomain' ),
		'all_items'             => __( 'All Links', 'textdomain' ),
		'search_items'          => __( 'Search Links', 'textdomain' ),
		'parent_item_colon'     => __( 'Parent Links:', 'textdomain' ),
		'not_found'             => __( 'No Links found.', 'textdomain' ),
		'not_found_in_trash'    => __( 'No Links found in Trash.', 'textdomain' ),
		'featured_image'        => _x( 'Link Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
		'set_featured_image'    => _x( 'Set hero image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
		'remove_featured_image' => _x( 'Remove hero image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
		'use_featured_image'    => _x( 'Use as hero image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
		'archives'              => _x( 'Link archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
		'insert_into_item'      => _x( 'Insert into Link', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
		'uploaded_to_this_item' => _x( 'Uploaded to this Link', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
		'filter_items_list'     => _x( 'Filter Links list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
		'items_list_navigation' => _x( 'Links list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
		'items_list'            => _x( 'Links list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
	);

	$args = array(
		'labels'              => $labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'query_var'           => true,
		'rewrite'             => array( 'slug' => '/link' ),
		'capability_type'     => 'post',
		'has_archive'         => true,
		'hierarchical'        => true,
		'supports'            => array( 'title' ),
		'show_in_rest'        => true,
		'exclude_from_search' => false,
		'menu_icon'           => 'dashicons-admin-links',
	);

	register_post_type( 'mu-weblink', $args );
}

/**
 * Flush rewrites whenever the plugin is activated.
 */
function mu_azindex_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'mu_azindex_activate' );

/**
 * Flush rewrites whenever the plugin is deactivated, also unregister 'mu-weblink' post type.
 */
function mu_azindex_deactivate() {
	unregister_post_type( 'mu-weblink' );
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'mu_azindex_deactivate' );

/**
 * Proper way to enqueue scripts and styles
 */
function mu_azindex_plugin_scripts() {
	wp_enqueue_style( 'mu-azindex', plugin_dir_url( __FILE__ ) . 'css/mu-azindex.css', '', true );
}
add_action( 'wp_enqueue_scripts', 'mu_azindex_plugin_scripts' );

/**
 * Remove YoastSEO metaboxes from Profiles
 */
function remove_yoast_metabox_mu_azindex() {
	remove_meta_box( 'wpseo_meta', 'mu-weblink', 'normal' );
}
add_action( 'add_meta_boxes', 'remove_yoast_metabox_mu_azindex', 11 );


/**
 * Edit columns displayed in the Dashboard for Link post types
 *
 * @param array $columns The array of columns.
 * @return array
 */
function mu_azindex_edit_columns( $columns ) {
	unset( $columns['wpseo-score'] );
	unset( $columns['wpseo-score-readability'] );
	unset( $columns['wpseo-title'] );
	unset( $columns['wpseo-metadesc'] );
	unset( $columns['wpseo-focuskw'] );
	unset( $columns['wpseo-links'] );
	unset( $columns['wpseo-linked'] );
	unset( $columns['date'] );
	unset( $columns['modified'] );
	$columns['mu_azindex_link_url'] = __( 'URL', 'your_text_domain' );
	$columns['date']                = 'Date';
	$columns['modified']            = 'Modified';
	return $columns;
}
add_filter( 'manage_edit-mu-weblink_columns', 'mu_azindex_edit_columns' );

/**
 * Getting the data to display for each column.
 *
 * @param string  $column The string name of the column.
 * @param integer $post_id The integer Post ID.
 */
function mu_azindex_custom_columns( $column, $post_id ) {
	switch ( $column ) {
		case 'mu_azindex_link_url':
			echo esc_attr( get_field( 'mu_azindex_link_url', $post_id ) );
			break;
	}
}
add_action( 'manage_mu-weblink_posts_custom_column', 'mu_azindex_custom_columns', 10, 2 );

/**
 * Redirect link page
 */
function mu_azindex_redirect() {
	if ( is_singular( 'mu-weblink' ) ) {
		global $post;

		wp_redirect( esc_url( get_field( 'mu_azindex_link_url', $post->ID ) ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'mu_azindex_redirect' );

/**
 * Add 'alpha' to the acceptable URL parameters
 *
 * @param array $vars The array of acceptable URL parameters.
 * @return array
 */
function mu_azindex_url_parameters( $vars ) {
	$vars[] = 'alpha';
	return $vars;
}
add_filter( 'query_vars', 'mu_azindex_url_parameters' );

/**
 * Shortcode to display the AZ Index Starting Letter list
 *
 * @param array  $atts The array of attributes included with the shortcode.
 * @param string $content The HTML string for the shortcode.
 * @return string
 */
function mu_azindex_letters_shortcode( $atts, $content = null ) {
	$data = shortcode_atts(
		array(
			'bg_image' => 'https://www.marshall.edu/wp-content/uploads/brand.jpg',

		),
		$atts
	);

	global $wpdb;

	$letters = $wpdb->get_results( 'SELECT DISTINCT LEFT(post_title, 1) as letter FROM ' . $wpdb->get_blog_prefix() . 'posts WHERE post_type = "mu-weblink" ORDER BY letter;' );

	$html = '<div class="flex flex-wrap space-x-1 justify-center">';
	foreach ( $letters as $letter ) {
		$html .= '<a href="?alpha=' . $letter->letter . '" class="mb-1 text-base lg:text-lg mb-2 py-2 px-3 bg-gray-100 text-gray-700 hover:bg-white hover:text-gray-800 no-underline hover:underline">' . $letter->letter . '</a>';
	}
	$html .= '</div>';
	return $html;

}
add_shortcode( 'mu_azindex_letters', 'mu_azindex_letters_shortcode' );

/**
 * Shortcode to display the AZ Index
 *
 * @param array  $atts The array of attributes included with the shortcode.
 * @param string $content The HTML string for the shortcode.
 * @return string
 */
function mu_azindex_listings_shortcode( $atts, $content = null ) {
	$data = shortcode_atts(
		array(
			'class' => '',

		),
		$atts
	);

	if ( get_query_var( 'alpha' ) ) {
		$alpha_letter = get_query_var( 'alpha' );
	} else {
		$alpha_letter = 'A';
	}

	$args = array(
		'post_type'      => 'mu-weblink',
		'posts_per_page' => -1,
		'orderby'        => array(
			'title' => 'ASC',
		),
		'extend_where'   => "(post_title like '" . $alpha_letter . "%')",
	);

	$alpha_query = new WP_Query( $args );

	$html = '<div class="' . esc_attr( $data['class'] ) . '">';
	if ( $alpha_query->have_posts() ) {
		$html .= '<ul>';
		while ( $alpha_query->have_posts() ) {
			$alpha_query->the_post();
			$html .= '<li><a href="' . get_field( 'mu_azindex_link_url', get_the_id() ) . '">' . get_the_title() . '</a></li>';
		}
		$html .= '</ul>';
	} else {
		$html .= '<p>Sorry no links were found starting with the letter "' . esc_attr( $alpha_letter ) . '"';
	}
	$html .= '</div>';
	return $html;

}
add_shortcode( 'mu_azindex_listing', 'mu_azindex_listings_shortcode' );

// function mu_azindex_setup() {
// 	$links_json  = file_get_contents( plugin_dir_path( __FILE__ ) . 'initial_links.json' );
// 	$links_array = json_decode( $links_json, true );
// 	require_once( ABSPATH . 'wp-admin/includes/post.php' );
// 	foreach ( $links_array as $link ) {

// 		if ( ! post_exists( $link['LinkName'], '', '', 'mu-weblink', '' ) ) {
// 			$new_post = array(
// 				'post_title'   => $link['LinkName'],
// 				'post_content' => '',
// 				'post_type'    => 'mu-weblink',
// 				'post_status'  => 'publish',
// 				'meta_input'   => array(
// 					'mu_azindex_link_url' => $link['LinkUrl'],
// 				),
// 			);
// 			wp_insert_post( $new_post );
// 		}
// 	}

// }
// add_shortcode( 'mu_azindex_insert', 'mu_azindex_setup' );

add_action( 'init', 'mu_program_page_type' );
