<?php
function bk_mail_insufficient_activation_codes(){
  $admin_email = sanitize_email(get_option('admin_email'));
  $to = array( $admin_email, 'bravokeyl@gmail.com' );
  $subject = 'Insufficient Activation codes';
  $body = 'Activation codes are ran out, please add more codes.';
  $headers[] = 'Content-Type: text/html; charset=UTF-8';
  $headers[] = 'From: Fable Sounds <wordpress@fablesounds.com>';
  wp_mail( $to, $subject, $body, $headers );
}
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
        'sku' => '',
        'type' => 'upgrade',
        'not_eligible' => 'Not eligible to upgrade'
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
      if(!empty($atts['sku'])) {
        $eligible = bk_current_user_eligible_to_upgrade($atts['id'],$atts['sku']);
      }
      if($eligible){
      } else {
        $url = '<button type="button" class="disabled grve-bg-hover-none" disabled>'.$atts['not_eligible'].'</button>';
      }
    }
  }

  return $url;
}
add_shortcode( 'fable_cart', 'bk_add_to_cart' );

function bk_create_voucher(){

}

function bk_assign_voucher_to_user($username,$ac_id,$product_id,$product_sku){
  $voucher_id = -1;
  $author_id = 1;

  $bk_wclogger = new WC_Logger();


  $sku_arr = get_post_meta($product_id,'bk_eligible_products', true);
  if(is_array($sku_arr)){
    foreach($sku_arr as $key => $pid){
      $slug = 'activation-code-id-'.$ac_id.'-'.$pid;
    	$title = strtoupper($username)."-".$ac_id."-".$product_id."-".$product_sku."-".$pid;
      if( null == get_page_by_title( $title ) ) {
        $bk_wclogger->add('debug','Creating Voucher '.$title.' and assigning it to user '.$username.' : Product '.$product_sku);
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
          update_post_meta($voucher_id,'bk_voucher_product_sku', $sku_arr[$key] );
          update_post_meta($voucher_id,'bk_voucher_status','nused');
          update_post_meta($voucher_id,'bk_voucher_user_login', $username);
          update_post_meta($voucher_id,'bk_voucher_date', current_time('mysql'));
        }
      } else {
        $bk_wclogger->add('warning','Cannot create vocuher '.$title.' and user '.$username.' : Product '.$product_sku);
        $voucher_id = -2;
      }
    }
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
