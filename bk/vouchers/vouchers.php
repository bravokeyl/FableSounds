<?php
function fs_vouchers() {

	$labels = array(
		'name'                  => _x( 'Vouchers', 'Post Type General Name', 'fablesounds' ),
		'singular_name'         => _x( 'Voucher', 'Post Type Singular Name', 'fablesounds' ),
		'menu_name'             => __( 'Vouchers', 'fablesounds' ),
		'name_admin_bar'        => __( 'Voucher', 'fablesounds' ),
		'archives'              => __( 'Item Archives', 'fablesounds' ),
		'attributes'            => __( 'Item Attributes', 'fablesounds' ),
		'parent_item_colon'     => __( 'Parent Item:', 'fablesounds' ),
		'all_items'             => __( 'All Vouchers', 'fablesounds' ),
		'add_new_item'          => __( 'Add New Voucher', 'fablesounds' ),
		'add_new'               => __( 'Add New', 'fablesounds' ),
		'new_item'              => __( 'New Voucher', 'fablesounds' ),
		'edit_item'             => __( 'Edit Voucher', 'fablesounds' ),
		'update_item'           => __( 'Update Voucher', 'fablesounds' ),
		'view_item'             => __( 'View Voucher', 'fablesounds' ),
		'view_items'            => __( 'View Vouchers', 'fablesounds' ),
		'search_items'          => __( 'Search Voucher', 'fablesounds' ),
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
		'label'                 => __( 'Voucher', 'fablesounds' ),
		'description'           => __( 'Vouchers', 'fablesounds' ),
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
	register_post_type( 'fs_vouchers', $args );

}
add_action( 'init', 'fs_vouchers', 0 );

add_action( 'manage_fs_vouchers_posts_custom_column', 'fs_render_vouchers_columns');
add_filter( 'manage_fs_vouchers_posts_columns',  'fs_vouchers_columns' );
function fs_vouchers_columns($existing_columns){

  $columns                = array();
  $columns['cb']          = $existing_columns['cb'];
  $columns['title'] = __( 'Voucher', 'fablesounds' );
  $columns['product_sku'] = __( 'Product SKU', 'fablesounds' );
  $columns['user_email'] = __( 'User Email', 'fablesounds' );
  $columns['user_name'] = __( 'User Name', 'fablesounds' );
  $columns['status']       = __( 'Status', 'fablesounds' );
  $columns['voucher_date'] = __( 'Date', 'fablesounds' );

  return $columns;
}
function fs_render_vouchers_columns( $column ) {
  global $post, $woocommerce;
  $vouchers_product_sku =  get_post_meta( $post->ID, 'bk_voucher_product_sku', true );
  $vouchers_voucher_status =  get_post_meta( $post->ID, 'bk_voucher_status', true );
  $vouchers_voucher_date =  get_post_meta( $post->ID, 'bk_voucher_date', true );
  $vouchers_user_login = get_post_meta( $post->ID, 'bk_voucher_user_login', true );
  $vouchers_user = get_user_by( 'login', $vouchers_user_login );
  switch ( $column ) {
    case 'product_sku' :
			if ( $vouchers_product_sku ) {
				print_r(get_the_title($vouchers_product_sku));
			} else {
				echo '&ndash;';
			}
    break;
		case 'user_email' :
			if ( $vouchers_user ) {
				echo sanitize_email($vouchers_user->user_email);
			} else {
				echo '&ndash;';
			}
    break;
    case 'user_name' :
			if ( $vouchers_user_login ) {
				echo esc_attr($vouchers_user_login);
			} else {
				echo '&ndash;';
			}
    break;
    case 'status' :
			if ( $vouchers_voucher_status == 'used' ) {
				echo "Used";
			}else {
				echo "Not Used";
			}
    break;
    case 'voucher_date' :
			if ( $vouchers_voucher_date ) {
				echo esc_attr($vouchers_voucher_date);
			} else {
				echo '&ndash;';
			}
    break;
  }
}
