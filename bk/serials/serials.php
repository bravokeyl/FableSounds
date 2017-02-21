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
  $columns['product_id']      = __( 'Product SKU', 'fablesounds' );
  $columns['dealer_price'] = __( 'Dealer Price', 'fablesounds' );
  $columns['seller_name']        = __( 'Seller Name', 'fablesounds' );
  $columns['user_id'] = __( 'User Login', 'fablesounds' );
  $columns['status']       = __( 'Status', 'fablesounds' );
  $columns['download_code'] = __( 'Download Code', 'fablesounds' );
	$columns['serial_distributed'] = __( 'Is Distributed?', 'fablesounds' );
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
				echo 'Direct';
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
			$snuid = get_post_meta( $post->ID, 'bk_sn_user_login', true );
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
    case 'download_code' :
			$sndate = get_post_meta( $post->ID, 'bk_download_code', true );
			if ( $sndate ) {
				echo esc_attr($sndate);
			} else {
				echo '&ndash;';
			}
    break;
    case 'dealer_price' :
			$sndate = get_post_meta( $post->ID, 'bk_dealer_price', true );
			if ( $sndate ) {
				echo esc_attr($sndate);
			} else {
				echo '&ndash;';
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
		case 'serial_distributed' :
			$sndistributed = get_post_meta( $post->ID, 'bk_sn_distributed', true );
			if ( $sndistributed ) {
				echo 'Yes';
			} else {
				echo 'No';
			}
    break;

  }
}

add_action( 'restrict_manage_posts', 'bk_admin_serials_filter_restrict_manage_posts' );
function bk_admin_serials_filter_restrict_manage_posts(){
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    if ('fs_serial_numbers' == $type){
        $values = array(
            'Not Registered' => 'nreg',
            'Registered' => 'reg',
        );
				$dvalues = array(
            'No' => '0',
            'Yes' => '1',
        );
        $current_user = isset($_GET['fs_user_login'])? $_GET['fs_user_login']:'';
        $current_seller = isset($_GET['fs_seller_name'])? $_GET['fs_seller_name']:'';
        $current_sku = isset($_GET['fs_product_sku'])? $_GET['fs_product_sku']:'';
        ?>
        <input type="text" name="fs_user_login" placeholder="Username" value="<?php echo $current_user;?>" style="max-width: 140px;"/>
        <input type="text" name="fs_seller_name" placeholder="Seller Name" value="<?php echo $current_seller;?>" style="max-width: 140px;"/>
        <input type="text" name="fs_product_sku" placeholder="SKU" value="<?php echo $current_sku;?>" style="max-width: 80px;"/>
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
				<select name="fs_serial_distributed">
        <option value=""><?php _e('Distributed', 'fablesounds'); ?></option>
        <?php
            $current_d = isset($_GET['fs_serial_distributed'])? $_GET['fs_serial_distributed']:'';
            foreach ($dvalues as $l => $v) {
                printf
                    (
                        '<option value="%s"%s>%s</option>',
                        $v,
                        $v == $current_d? ' selected="selected"':'',
                        $l
                    );
                }
        ?>
        </select>
        <?php
    }
}


add_filter( 'parse_query', 'bk_serials_filter' );
function bk_serials_filter( $query ){
    global $pagenow;
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
    $fs_sn_user = '';
    $fs_sn_status = '';
    $bk_meta_query = array(
      'relation' => 'AND',
    );
    if ( 'fs_serial_numbers' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['fs_serial_status']) && $_GET['fs_serial_status'] != '') {
        $fs_sn_status = $_GET['fs_serial_status'];
        $bk_meta_query[] = array(
            'key'       => 'bk_sn_status',
            'value'     => $fs_sn_status,
            'compare'   => '='
        );
    }
    if ( 'fs_serial_numbers' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['fs_user_login']) && $_GET['fs_user_login'] != '') {
        $fs_sn_user = $_GET['fs_user_login'];
        $bk_meta_query[] = array(
            'key'       => 'bk_sn_user_login',
            'value'     => $fs_sn_user,
            'compare'   => '='
        );
    }
    if ( 'fs_serial_numbers' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['fs_product_sku']) && $_GET['fs_product_sku'] != '') {
        $fs_product_sku = $_GET['fs_product_sku'];
        $bk_meta_query[] = array(
            'key'       => 'bk_sn_product_sku',
            'value'     => $fs_product_sku,
            'compare'   => '='
        );
    }
    if ( 'fs_serial_numbers' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['fs_seller_name']) && $_GET['fs_seller_name'] != '') {
        $fs_sn_seller = $_GET['fs_seller_name'];
        $bk_meta_query[] = array(
            'key'       => 'bk_sn_seller_name',
            'value'     => $fs_sn_seller,
            'compare'   => '='
        );
    }
		if ( 'fs_serial_numbers' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['fs_serial_distributed']) && $_GET['fs_serial_distributed'] != '') {
        $fs_sn_dist = $_GET['fs_serial_distributed'];
        $bk_meta_query[] = array(
            'key'       => 'bk_sn_distributed',
            'value'     => $fs_sn_dist,
            'compare'   => '='
        );
    }
		// wp_die(print_r($bk_meta_query));
    $query->set('meta_query', $bk_meta_query);
}
