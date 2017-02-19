<?php
function fs_serial_numbers() {

	$labels = array(
		'name'                  => _x( 'Serial Numbers', 'Post Type General Name', 'fablesounds' ),
		'singular_name'         => _x( 'Serial Number', 'Post Type Singular Name', 'fablesounds' ),
		'menu_name'             => __( 'Serial Numbers', 'fablesounds' ),
		'name_admin_bar'        => __( 'Serial Number', 'fablesounds' ),
		'archives'              => __( 'Item Archives', 'fablesounds' ),
		'attributes'            => __( 'Item Attributes', 'fablesounds' ),
		'parent_item_colon'     => __( 'Parent Item:', 'fablesounds' ),
		'all_items'             => __( 'All Serials', 'fablesounds' ),
		'add_new_item'          => __( 'Add New Serial', 'fablesounds' ),
		'add_new'               => __( 'Add New', 'fablesounds' ),
		'new_item'              => __( 'New Serial', 'fablesounds' ),
		'edit_item'             => __( 'Edit Serial', 'fablesounds' ),
		'update_item'           => __( 'Update Serial', 'fablesounds' ),
		'view_item'             => __( 'View Serial', 'fablesounds' ),
		'view_items'            => __( 'View Serials', 'fablesounds' ),
		'search_items'          => __( 'Search Serial', 'fablesounds' ),
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
		'label'                 => __( 'Serial Number', 'fablesounds' ),
		'description'           => __( 'Serial numbers used by third parties', 'fablesounds' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'excerpt'),
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 25,
		'menu_icon'             => 'dashicons-welcome-widgets-menus',
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
	register_post_type( 'fs_serial_numbers', $args );

}
add_action( 'init', 'fs_serial_numbers', 0 );


add_action( 'manage_fs_serial_numbers_posts_custom_column', 'fs_render_serial_numbers_columns');
add_filter( 'manage_fs_serial_numbers_posts_columns',  'fs_serial_numbers_columns' );
function fs_serial_numbers_columns($existing_columns){

  $columns                = array();
  $columns['cb']          = $existing_columns['cb'];
  $columns['title'] = __( 'Serial Number', 'fablesounds' );
  $columns['seller_name']        = __( 'Seller Name', 'fablesounds' );
  $columns['product_id']      = __( 'Product SKU', 'fablesounds' );
  $columns['user_id'] = __( 'User Login', 'fablesounds' );
  //$columns['products']    = __( 'Product IDs', 'fablesounds' );
  $columns['status']       = __( 'Status', 'fablesounds' );
  $columns['serial_date'] = __( 'Date', 'fablesounds' );
	$columns['distributed'] = __( 'Distributed', 'fablesounds' );

  return $columns;
}
function fs_render_serial_numbers_columns( $column ) {
  global $post, $woocommerce;
  switch ( $column ) {
    case 'seller_name' :
			$snseller =  get_post_meta( $post->ID, 'bk_sn_seller_name', true );
			if ( $snseller ) {
				echo esc_attr($snseller);
			} else {
				echo '&ndash;';
			}
    break;
    case 'product_id' :
			$snpid =  get_post_meta( $post->ID, 'bk_sn_product_sku', true );
			if ( $snpid ) {
				echo esc_attr($snpid);
			} else {
				echo '&ndash;';
			}
    break;
    case 'user_id' :
			$snuid = get_post_meta( $post->ID, 'bk_sn_user_name', true );
			if ( $snuid ) {
				echo $snuid;
			} else {
				echo '&ndash;';
			}
    break;
    case 'status' :
		  $status = get_post_meta( $post->ID, 'bk_sn_status', true );
			if ( $status == 'reg' ) {
				echo "Registered";
			}else {
				echo "Not Registered";
			}
    break;
    case 'serial_date' :
			$sndate = get_post_meta( $post->ID, 'bk_sn_date', true );
			if ( $sndate ) {
				echo esc_attr($sndate);
			} else {
				echo '&ndash;';
			}
    break;
		case 'distributed' :
			$sndate = get_post_meta( $post->ID, 'bk_sn_distributed', true );
			if ( $sndate ) {
				echo esc_attr($sndate);
			} else {
				echo '&ndash;';
			}
    break;
  }
}
