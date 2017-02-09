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

function fs_activation_codes() {

	$labels = array(
		'name'                  => _x( 'Activation Codes', 'Post Type General Name', 'fablesounds' ),
		'singular_name'         => _x( 'Activation Code', 'Post Type Singular Name', 'fablesounds' ),
		'menu_name'             => __( 'Activation Codes', 'fablesounds' ),
		'name_admin_bar'        => __( 'Activation Code', 'fablesounds' ),
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
		'label'                 => __( 'Activation Code', 'fablesounds' ),
		'description'           => __( 'Activation codes', 'fablesounds' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'excerpt' ),
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
	register_post_type( 'fs_activation_codes', $args );

}
add_action( 'init', 'fs_activation_codes', 0 );


add_action( 'manage_fs_serial_numbers_posts_custom_column', 'fs_render_serial_numbers_columns');
add_filter( 'manage_fs_serial_numbers_posts_columns',  'fs_serial_numbers_columns' );
function fs_serial_numbers_columns($existing_columns){

  $columns                = array();
  $columns['cb']          = $existing_columns['cb'];
  $columns['title'] = __( 'Serial Number', 'fablesounds' );
  $columns['seller_name']        = __( 'Seller Name', 'fablesounds' );
  $columns['product_id']      = __( 'Product ID', 'fablesounds' );
  $columns['user_id'] = __( 'User ID', 'fablesounds' );
  //$columns['products']    = __( 'Product IDs', 'fablesounds' );
  $columns['status']       = __( 'Status', 'fablesounds' );
  $columns['serial_date'] = __( 'Date', 'fablesounds' );

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
			$snpid =  get_post_meta( $post->ID, 'bk_sn_product_id', true );
			if ( $snpid ) {
				echo intval($snpid);
			} else {
				echo '&ndash;';
			}
    break;
    case 'user_id' :
			$snuid = get_post_meta( $post->ID, 'bk_sn_user_id', true );
			if ( $snuid ) {
				echo intval($snuid);
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
  }
}

add_action( 'manage_fs_activation_codes_posts_custom_column', 'fs_render_activation_codes_columns');
add_filter( 'manage_fs_activation_codes_posts_columns',  'fs_activation_codes_columns' );
function fs_activation_codes_columns($existing_columns){

  $columns                = array();
  $columns['cb']          = $existing_columns['cb'];
  $columns['title']       = __( 'Activation Code', 'fablesounds' );
  $columns['user_id']     = __( 'User Data', 'fablesounds' );
  // $columns['seller_name']        = __( 'Seller Name', 'fablesounds' );
  $columns['product_id']      = __( 'Product ID', 'fablesounds' );
  //$columns['products']    = __( 'Product IDs', 'fablesounds' );
  $columns['status']       = __( 'Status', 'fablesounds' );
  $columns['serial_date'] = __( 'Assigned Date', 'fablesounds' );

  return $columns;
}
function fs_render_activation_codes_columns( $column ) {
  global $post, $woocommerce;

  switch ( $column ) {

    case 'product_id' :
      $cpid = intval( get_post_meta( $post->ID, 'bk_ac_product_id', true ) );
			$order = wc_get_order($cpid);
      if ( $cpid ) {
        print_r($cpid);
      } else {
        echo '&ndash;';
      }
    break;
    case 'user_id' :
      $cuid = intval( get_post_meta( $post->ID, 'bk_ac_user_id', true ) );
			$user = get_user_by( 'ID', $cuid );
      if ( !empty($cuid) ) {
        echo $user->user_email."(".$user->display_name.")";
      } else {
        echo '&ndash;';
      }
    break;
    case 'status' :
      $cstatus = esc_html( get_post_meta( $post->ID, 'bk_ac_status', true ) );
      if ( $cstatus == 'used') {
        echo "Used";
      } elseif( $cstatus == 'nused' ) {
        echo "Not Used";
      }
    break;
    case 'serial_date' :
      $cdate = esc_html( get_post_meta( $post->ID, 'bk_ac_date', true ) );
      if ( $cdate ) {
        echo $cdate;
      } else {
        echo '&ndash;';
      }
    break;

  }
}
