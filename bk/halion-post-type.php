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
  $columns['title'] = __( 'Halion code', 'fablesounds' );
  $columns['code_type'] = __( 'Type', 'fablesounds' );
	$columns['user_email'] = __( 'User Email', 'fablesounds' );
  $columns['user_name'] = __( 'User Name', 'fablesounds' );
  $columns['status']       = __( 'Status', 'fablesounds' );
  $columns['serial_date'] = __( 'Date', 'fablesounds' );

  return $columns;
}
function fs_render_halion_codes_columns( $column ) {
  global $post, $woocommerce;
  $halion_code_type =  get_post_meta( $post->ID, 'bk_halion_code_type', true );
  $halion_user_login = get_post_meta( $post->ID, 'bk_halion_user_login', true );
  $halion_user = get_user_by( 'login', $halion_user_login );
  switch ( $column ) {
    case 'code_type' :
			if ( $halion_code_type ) {
				echo ucwords($halion_code_type);
			} else {
				echo '&ndash;';
			}
    break;
		case 'user_email' :
			if ( $halion_user ) {
				echo sanitize_email($halion_user->user_email);
			} else {
				echo '&ndash;';
			}
    break;
    case 'user_name' :
			if ( $halion_user_login ) {
				echo $halion_user_login;
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
				try {
          $hc_date_obj = new DateTime();
          $hc_date = $hc_date_obj->setTimestamp($sndate);
          $hc_date_formatted = $hc_date->format('Y-m-d H:i:sP');
        } catch (Exception $e) {
          $hc_date_formatted = '';
        }
				echo esc_attr($hc_date_formatted);
			} else {
				echo '&ndash;';
			}
    break;
  }
}

add_action( 'restrict_manage_posts', 'bk_halion_filter_restrict_manage_posts' );
function bk_halion_filter_restrict_manage_posts(){
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    if ('fs_halion_codes' == $type){
        $values = array(
          'Not Registered' => 'nreg',
          'Registered' => 'reg',
        );
				$types = array(
          'Brass' => 'brass',
          'Reeds' => 'reeds',
					'Rythm' => 'rythm',
        );

        $current_user = isset($_GET['fs_user_login'])? $_GET['fs_user_login']:'';
        $current_seller = isset($_GET['fs_seller_name'])? $_GET['fs_seller_name']:'';
        ?>
        <input type="text" name="fs_user_login" placeholder="Username" value="<?php echo $current_user;?>" style="max-width: 150px;"/>
				<select name="bk_halion_status">
        <option value=""><?php _e('Status', 'fablesounds'); ?></option>
        <?php
            $current_v = isset($_GET['bk_halion_status'])? $_GET['bk_halion_status']:'';
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
				<select name="fs_halion_type">
        <option value=""><?php _e('Type', 'fablesounds'); ?></option>
        <?php
            $current_t = isset($_GET['fs_halion_type'])? $_GET['fs_halion_type']:'';
            foreach ($types as $label => $value) {
                printf
                    (
                        '<option value="%s"%s>%s</option>',
                        $value,
                        $value == $current_t? ' selected="selected"':'',
                        $label
                    );
                }
        ?>
        </select>
        <?php
    }
}


add_filter( 'parse_query', 'bk_halion_codes_filter' );
function bk_halion_codes_filter( $query ){
    global $pagenow;
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
		if ( 'fs_halion_codes' == $type && is_admin() && $pagenow=='edit.php') {
	    $fs_sn_user = '';
	    $fs_halion_status = '';
	    $bk_meta_query = array(
	      'relation' => 'AND',
	    );
	    if ( 'fs_halion_codes' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['bk_halion_status']) && $_GET['bk_halion_status'] != '') {
	        $fs_halion_status = $_GET['bk_halion_status'];
	        $bk_meta_query[] = array(
	            'key'       => 'bk_halion_status',
	            'value'     => $fs_halion_status,
	            'compare'   => '='
	        );
	    }
	    if ( 'fs_halion_codes' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['fs_user_login']) && $_GET['fs_user_login'] != '') {
	        $fs_sn_user = $_GET['fs_user_login'];
	        $bk_meta_query[] = array(
	            'key'       => 'bk_halion_user_login',
	            'value'     => $fs_sn_user,
	            'compare'   => '='
	        );
	    }
	    if ( 'fs_halion_codes' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['fs_halion_type']) && $_GET['fs_halion_type'] != '') {
	        $fs_halion_type = $_GET['fs_halion_type'];
	        $bk_meta_query[] = array(
	            'key'       => 'bk_halion_code_type',
	            'value'     => $fs_halion_type,
	            'compare'   => '='
	        );
	    }

	    $query->set('meta_query', $bk_meta_query);
		}//end post type meta
}
