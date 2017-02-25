<?php
function fs_halion_codes() {

	$labels = array(
		'name'                  => _x( 'Halion Codes', 'Post Type General Name', 'fablesounds' ),
		'singular_name'         => _x( 'Halion Code', 'Post Type Singular Name', 'fablesounds' ),
		'menu_name'             => __( 'Halion Codes', 'fablesounds' ),
		'name_admin_bar'        => __( 'Halion Code', 'fablesounds' ),
		'archives'              => __( 'Item Archives', 'fablesounds' ),
		'attributes'            => __( 'Item Attributes', 'fablesounds' ),
		'parent_item_colon'     => __( 'Parent Item:', 'fablesounds' ),
		'all_items'             => __( 'All Codes', 'fablesounds' ),
		'add_new_item'          => __( 'Add New Code', 'fablesounds' ),
		'add_new'               => __( 'Add New', 'fablesounds' ),
		'new_item'              => __( 'New Code', 'fablesounds' ),
		'edit_item'             => __( 'Edit Code', 'fablesounds' ),
		'update_item'           => __( 'Update Code', 'fablesounds' ),
		'view_item'             => __( 'View Code', 'fablesounds' ),
		'view_items'            => __( 'View Codes', 'fablesounds' ),
		'search_items'          => __( 'Search Code', 'fablesounds' ),
		'not_found'             => __( 'Not found', 'fablesounds' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'fablesounds' ),
		'featured_image'        => __( 'Featured Image', 'fablesounds' ),
		'set_featured_image'    => __( 'Set featured image', 'fablesounds' ),
		'remove_featured_image' => __( 'Remove featured image', 'fablesounds' ),
		'use_featured_image'    => __( 'Use as featured image', 'fablesounds' ),
		'insert_into_item'      => __( 'Insert into item', 'fablesounds' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'fablesounds' ),
		'items_list'            => __( 'Items list', 'fablesounds' ),
		'items_list_navigation' => __( 'Items list navigation', 'fablesounds' ),
		'filter_items_list'     => __( 'Filter items list', 'fablesounds' ),
	);
	$args = array(
		'label'                 => __( 'Halion Code', 'fablesounds' ),
		'description'           => __( 'Halion codes', 'fablesounds' ),
		'labels'                => $labels,
		'supports'              => array( 'title' ),
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 25,
		'menu_icon'             => 'dashicons-editor-code',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'capability_type'       => 'page',
		'show_in_rest'          => false,
    'rewrite'            => false,
    'query_var'          => false,
    'publicly_queryable' => false,
    'public'             => false
	);
	register_post_type( 'fs_halion_codes', $args );

}
add_action( 'init', 'fs_halion_codes', 0 );


add_action( 'manage_fs_halion_codes_posts_custom_column', 'fs_render_halion_codes_columns');
add_filter( 'manage_fs_halion_codes_posts_columns',  'fs_halion_codes_columns' );
function fs_halion_codes_columns($existing_columns){

  $columns                = array();
  $columns['cb']          = $existing_columns['cb'];
  $columns['title'] = __( 'Serial Number', 'fablesounds' );
  $columns['user_email'] = __( 'User Email', 'fablesounds' );
  $columns['user_name'] = __( 'User Name', 'fablesounds' );
  $columns['status']       = __( 'Status', 'fablesounds' );
  $columns['serial_date'] = __( 'Date', 'fablesounds' );

  return $columns;
}
function fs_render_halion_codes_columns( $column ) {
  global $post, $woocommerce;
  $halion_brass_code =  get_post_meta( $post->ID, 'bk_halion_brass_code', true );
  $halion_reeds_code =  get_post_meta( $post->ID, 'bk_halion_reeds_code', true );
  $halion_rythm_code =  get_post_meta( $post->ID, 'bk_halion_rythm_code', true );
  $halion_user_email = get_post_meta( $post->ID, 'bk_halion_user_email', true );
  $halion_user= get_user_by( 'email', $halion_user_email );
  switch ( $column ) {
    case 'user_email' :
			if ( $halion_user_email ) {
				echo sanitize_email($halion_user_email);
			} else {
				echo '&ndash;';
			}
    break;
    case 'user_name' :
			if ( $halion_user ) {
				echo esc_attr($halion_user->display_name);
			} else {
				echo '&ndash;';
			}
    break;
    case 'status' :
		  $status = get_post_meta( $post->ID, 'bk_halion_status', true );
			if ( $status == 'reg' ) {
				echo "Registered";
			}else {
				echo "Not Registered";
			}
    break;
    case 'serial_date' :
			$sndate = get_post_meta( $post->ID, 'bk_halion_date', true );
			if ( $sndate ) {
				echo esc_attr($sndate);
			} else {
				echo '&ndash;';
			}
    break;
  }
}
