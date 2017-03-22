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
	// $vouchers_product_sku = get_post_meta( $vouchers_product_id, '_sku', true );
  $vouchers_voucher_status =  get_post_meta( $post->ID, 'bk_voucher_status', true );
  $vouchers_voucher_date =  get_post_meta( $post->ID, 'bk_voucher_date', true );
  $vouchers_user_login = get_post_meta( $post->ID, 'bk_voucher_user_login', true );
  $vouchers_user = get_user_by( 'login', $vouchers_user_login );
	// wp_die(print_r($vouchers_user));
  switch ( $column ) {
    case 'product_sku' :
			if ( $vouchers_product_sku ) {
				echo $vouchers_product_sku;
			} else {
				echo '&ndash;';
			}
    break;
		case 'user_email' :
			if ( $vouchers_user ) {
				echo $vouchers_user->user_email;
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

add_action( 'restrict_manage_posts', 'bk_admin_vouchers_filter_restrict_manage_posts' );
function bk_admin_vouchers_filter_restrict_manage_posts(){
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    if ('fs_vouchers' == $type){
        $values = array(
            'Not Used' => 'nused',
            'Used' => 'used',
        );
        $current_user = isset($_GET['fs_user_login'])? $_GET['fs_user_login']:'';
        $current_seller = isset($_GET['fs_seller_name'])? $_GET['fs_seller_name']:'';
        $current_sku = isset($_GET['fs_product_sku'])? $_GET['fs_product_sku']:'';
        ?>
        <input type="text" name="fs_user_login" placeholder="Username" value="<?php echo $current_user;?>" style="max-width: 150px;"/>
        <!-- <input type="text" name="fs_seller_name" placeholder="Seller Name" value="<?php echo $current_seller;?>" style="max-width: 140px;"/> -->
        <input type="text" name="fs_product_sku" placeholder="SKU" value="<?php echo $current_sku;?>" style="max-width: 150px;"/>
        <select name="fs_serial_status">
        <option value=""><?php _e('Status', 'fablesounds'); ?></option>
        <?php
            $current_v = isset($_GET['fs_serial_status'])? $_GET['fs_serial_status']:'';
            foreach ($values as $label => $value) {
                printf
                    (
                        '<option value="%s"%s>%s</option>',
                        $value,
                        $value == $current_v? ' selected="selected"':'',
                        $label
                    );
                }
        ?>
        </select>
        <?php
    }
}

add_filter( 'parse_query', 'bk_vouchers_filter' );
function bk_vouchers_filter( $query ){
    global $pagenow;
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
		if ( 'fs_vouchers' == $type && is_admin() && $pagenow=='edit.php' ) {
	    $fs_sn_user = '';
	    $fs_sn_status = '';
	    $bk_meta_query = array(
	      'relation' => 'AND',
	    );
	    if ( 'fs_vouchers' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['fs_serial_status']) && $_GET['fs_serial_status'] != '') {
	        // $query->query_vars['meta_key'] = 'bk_sn_status';
	        $fs_sn_status = $_GET['fs_serial_status'];
	        // $query->query_vars['meta_value'] = $fs_sn_status;
	        $bk_meta_query[] = array(
	            'key'       => 'bk_voucher_status',
	            'value'     => $fs_sn_status,
	            'compare'   => '='
	        );
	    }
	    if ( 'fs_vouchers' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['fs_user_login']) && $_GET['fs_user_login'] != '') {

	        // $query->query_vars['meta_key'] = 'bk_sn_user_login';
	        $fs_sn_user = $_GET['fs_user_login'];
	        // $query->query_vars['meta_value'] = $fs_sn_user;
	        $bk_meta_query[] = array(
	            'key'       => 'bk_voucher_user_login',
	            'value'     => $fs_sn_user,
	            'compare'   => '='
	        );
	    }
	    if ( 'fs_vouchers' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['fs_product_sku']) && $_GET['fs_product_sku'] != '') {
	        $fs_product_sku = $_GET['fs_product_sku'];
	        $bk_meta_query[] = array(
	            'key'       => 'bk_voucher_product_sku',
	            'value'     => $fs_product_sku,
	            'compare'   => '='
	        );
	    }

	    $query->set('meta_query', $bk_meta_query);
	}//end post type check
}
