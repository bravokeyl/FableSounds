<?php
function bk_get_sku($pid) {
  return false;
}
function bk_product_upgrade_update($product_id) {

  $is_product_upgrade = get_post_meta($product_id,'bk_product_upgrade_update',true);

  if( "yes" == $is_product_upgrade ){
    return true;
  }

  return false;
}

function bk_add_to_cart( $atts ) {
  $atts = shortcode_atts( array(
        'label' => 'Add to Cart',
        'id' => '',
        'type' => 'upgrade'
  ), $atts, 'fable_cart' );

  $url = '';
  $pid = intval($atts['id']);
  $class = "grve-btn grve-btn-medium grve-round grve-bg-primary-1";
  $href = '';
  $eligible = false;
  $label = $atts['label'];

  if( "new" === $atts['type'] ){
    $href = 'href="/cart/?add-to-cart='.$pid.'"';
  } else {
    $href = 'href="/my-account/"';
    $label = "Login to upgrade";
  }

  if( ! is_user_logged_in() ){
    $url = '<a '.$href.' class="'.$class.'">'.$label.'</a>';
  } else {

    $label = $atts['label'];
    $href = 'href="/cart/?add-to-cart='.$pid.'"';

    $url = '<a data-quantity="1" '.$href;
    $url .= 'data-product_id="'.$pid.'" class="'.$class.'">><span class="grve-item">';
    $url .= '<i class="grve-menu-icon fa fa-shopping-cart"></i></span>'.$label.'</a>';

    if( "new" == $atts['type'] ){
    }else {
      if($eligible){
      } else {
        $url = '<button type="button" class="disabled grve-bg-hover-none" disabled>Not eligible to upgrade</button>';
      }
    }
  }

  return $url;
}
add_shortcode( 'fable_cart', 'bk_add_to_cart' );

function bk_create_voucher(){

}
function bk_assign_voucher_to_user($username,$ac_id,$sku){
  $voucher_id = -1;
  $author_id = 1;
	$slug = 'activation-code-id-'.$ac_id;
	$title = strtoupper($username)."-".$ac_id."-".$sku;
  if( null == get_page_by_title( $title ) ) {
    $voucher_id = wp_insert_post(
			array(
				'comment_status'	=>	'closed',
				'ping_status'		=>	'closed',
				'post_author'		=>	$author_id,
				'post_name'		=>	$slug,
				'post_title'		=>	$title,
				'post_status'		=>	'publish',
				'post_type'		=>	'fs_vouchers'
			)
		);
    if($voucher_id){
      update_post_meta($voucher_id,'bk_voucher_status','used');
      update_post_meta($voucher_id,'bk_voucher_product_sku', $sku);
      update_post_meta($voucher_id,'bk_voucher_user_login', $username);
      update_post_meta($voucher_id,'bk_voucher_date', current_time('mysql'));
    }
  } else {
    $voucher_id = -2;
  }

  return $voucher_id;
}

function bk_get_user_product_vouchers($username,$sku){
  $args = array(
    'post_type' => 'fs_vouchers',
    'post_status' => 'publish',
    'posts_per_page' => '-1',
    'meta_query' => array(
      'relation' => 'AND',
      array(
        'key'   => 'bk_voucher_status',
        'value' => 'nused',
        'compare' => '='
      ),
      array(
        'key'   => 'bk_voucher_user_login',
        'value' => $username,
        'compare' => '='
      ),
      array(
        'key'   => 'bk_voucher_product_sku',
        'value' => $sku,
        'compare' => '='
      ),
    )
  );
  $vquery = new WP_Query($args);
  $vouchers_found = $vquery->found_posts;

  return $vouchers_found;
}
/* Handle upgrades */
function bk_current_user_eligible_to_upgrade($product_id,$sku) {

  global $woocommerce;
  $current_user= wp_get_current_user();
  $customer_email = $current_user->email;
  $user_id = $current_user->ID;
  $user_login = $current_user->user_login;
  if($user_id) { //0 means not logged in or guest user
    $vouchers = bk_get_user_product_vouchers($user_login,$sku);
    // if ( wc_customer_bought_product( $customer_email, $user_id, $product_id) ){
    //   return true;
    // }
    if($vouchers > 0){
      return true;
    }
  }

  return false;
}

add_action('woocommerce_add_to_cart','bk_check_add_to_cart',10,6);
function bk_check_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

  $product = new WC_Product($product_id);
  $sku = $product->get_sku();
  $is_product_upgrade = bk_product_upgrade_update($product_id);

  if($is_product_upgrade){
    $eligible = bk_current_user_eligible_to_upgrade($product_id,$sku);
    if($eligible) {

    } else {
      wc_add_notice( "You are not eligible to upgrade. Please register a product or buy a new one.", 'error' );
      wp_safe_redirect( wc_get_endpoint_url( 'my-account' ) );
      exit;
    }
  }

}

add_filter('really_simple_csv_importer_save_meta', function($meta, $post, $is_update) {
    foreach ($meta as $key => $value) {
      if('bk_voucher_imp_date' == $key) {
        if (strpos($value, ',') !== false) {
            $_value = preg_split("/,+/", $value);
            $meta[$key] = $_value;
        }
      }
    }
    return $meta;
}, 10, 3);
