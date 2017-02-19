<?php
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

add_action( 'manage_fs_activation_codes_posts_custom_column', 'fs_render_activation_codes_columns');
add_filter( 'manage_fs_activation_codes_posts_columns',  'fs_activation_codes_columns' );
function fs_activation_codes_columns($existing_columns){

  $columns                = array();
  $columns['cb']          = $existing_columns['cb'];
  $columns['title']       = __( 'Activation Code', 'fablesounds' );
	$columns['serial_activation']       = __( 'Serial Code', 'fablesounds' );
  $columns['user_email']     = __( 'User Email', 'fablesounds' );
	$columns['user_name']     = __( 'User Login', 'fablesounds' );
  // $columns['seller_name']        = __( 'Seller Name', 'fablesounds' );
  $columns['product_id']      = __( 'Product SKU', 'fablesounds' );
  //$columns['products']    = __( 'Product IDs', 'fablesounds' );
  $columns['status']       = __( 'Status', 'fablesounds' );
  $columns['serial_date'] = __( 'Assigned Date', 'fablesounds' );

  return $columns;
}
function fs_render_activation_codes_columns( $column ) {
  global $post, $woocommerce;
	$cuname = get_post_meta( $post->ID, 'bk_ac_user_login', true );
	$user = get_user_by( 'login', $cuname );
  switch ( $column ) {

    case 'serial_activation' :
      $cpid = get_post_meta( $post->ID, 'bk_ac_serial_activation', true );
      if ( $cpid ) {
        print_r($cpid);
      } else {
        echo '&ndash;';
      }
    break;
		case 'product_id' :
      $cpid = get_post_meta( $post->ID, 'bk_ac_product_sku', true );
			// $order = wc_get_order($cpid);
      if ( $cpid ) {
        print_r($cpid);
      } else {
        echo '&ndash;';
      }
    break;
    case 'user_email' :
      if ( !empty($cuname) ) {
        echo $user->user_email;
      } else {
        echo '&ndash;';
      }
    break;
		case 'user_name' :
      if ( !empty($cuname) ) {
        echo $user->display_name;
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


add_filter('manage_users_columns', 'bk_add_user_id_column');
function bk_add_user_id_column($columns) {
    $columns['user_eligible'] = 'User Upgrades';
    return $columns;
}

add_action('manage_users_custom_column',  'bk_show_user_id_column_content', 10, 3);
function bk_show_user_id_column_content($value, $column_name, $user_id) {
    $user = get_user_meta($user_id,'fs_capabilities',true);

		// switch ($column_name) {
    //     case 'phone' :
    //         return get_the_author_meta( 'phone', $user_id );
    //         break;
    //     case 'xyz' :
    //         return '';
    //         break;
    //     default:
    // }
    // return $val;

		if ( 'user_eligible' == $column_name ) {
			return wc_get_customer_order_count($user_id);
		}
    return $value;
}