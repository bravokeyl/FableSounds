<?php
function bk_mail_insufficient_activation_codes($count='0'){
  $admin_email = sanitize_email(get_option('admin_email'));
  $to = array( $admin_email, 'bravokeyl@gmail.com' );
  $subject = 'Low on Activation codes';
  if( 0 == $count) {
    $subject = 'Insufficient Activation codes';
  }
  $body = 'Activation codes ran out, please add more codes.';
  $headers[] = 'Content-Type: text/html; charset=UTF-8';
  $headers[] = 'From: Fable Sounds <wordpress@fablesounds.com>';
  wp_mail( $to, $subject, $body, $headers );
}
function bk_activation_codes_available(){
  $args = array(
    'post_type' => 'fs_activation_codes',
    'posts_per_page' => '-1',
    'meta_query' => array(
      array(
        'key' => 'bk_ac_status',
        'value' => "nused",
        'compare' => '='
      )
    ),
    'post_status' => 'publish'
  );

  $query = new WP_Query($args);
  $count = $query->found_posts;
  if(0 == intval($count)) {
    bk_mail_insufficient_activation_codes(0);
    return false;
  }
  return true;
}
function bk_get_sku($pid) {
  return false;
}
function bk_create_serial_number($sku,$order_id,$ac_id,$username){
  $serial_id = -1;
  $author_id = 1;
  $slug = $sku.'-'.$order_id.'-'.$ac_id.'-'.wp_rand(1000,9999);
  $title = strtoupper($sku)."-".$order_id."-".$ac_id."-".wp_rand(1000,9999);
  $serial_id = wp_insert_post(
    array(
      'comment_status'	=>	'closed',
      'ping_status'		=>	'closed',
      'post_author'		=>	$author_id,
      'post_name'		=>	$slug,
      'post_title'		=>	$title,
      'post_status'		=>	'publish',
      'post_type'		=>	'fs_serial_numbers'
    )
  );
  if($serial_id){
    update_post_meta($serial_id,'bk_sn_product_sku', $sku );
    update_post_meta($serial_id,'bk_sn_status','nreg');
    update_post_meta($serial_id,'bk_sn_seller_name', "Direct Buy");
    update_post_meta($serial_id,'bk_sn_user_login', $username);
    update_post_meta($serial_id,'bk_sn_date', current_time('mysql'));
  }
}

function bk_assign_serial_number($quantity){
  $code = array();
  $args = array(
    'post_type' => 'fs_serial_numbers',
    'posts_per_page' => $quantity,
    'meta_query' => array(
      'relation' => 'AND',
      array(
        'key' => 'bk_sn_status',
        'value' => "nreg",
        'compare' => '='
      ),
      array(
        'key' => 'bk_sn_seller_name',
        'value' => '',
        'compare' => '='
      )
    ),
    'post_status' => 'publish'
  );

  $query = new WP_Query($args);

  // wp_die(print_r($query->request));
  if($query->have_posts()){
    while($query->have_posts()){
      $query->the_post();
      array_push($code, get_the_ID());
    }
    wp_reset_postdata();
  }

  return $code;
}

function bk_product_upgrade($product_id) {
  $is_product_upgrade = get_post_meta($product_id,'bk_product_upgrade',true);
  if( "yes" == $is_product_upgrade ){
    return true;
  }
  return false;
}

function bk_add_to_cart( $atts ) {
  $atts = shortcode_atts( array(
        'label' => 'Add to Cart',
        'id' => '0',
        'eligible_product_id' => '',
        'type' => 'upgrade',
        'nlabel' => 'Login to find out if you are eligible',
        'sku' => ''
  ), $atts, 'fable_cart' );

  $url = '';
  $pid = intval($atts['id']);
  $class = "grve-btn grve-btn-medium grve-round grve-bg-primary-1";
  $href = '';
  $eligible = false;
  $label = $atts['label'];
  $eligible_product_id = $atts['eligible_product_id'];
  if( "new" === $atts['type'] ){
    $href = 'href="/cart/?add-to-cart='.$pid.'"';
  } else {
    $href = 'href="/my-account/"';
    $label = $atts['nlabel'];
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
        $eligible = bk_current_user_eligible_to_upgrade($pid,$atts['sku']);
      }
      if($eligible){
      } else {

        $eligible_message = "Not Eligible";
        $eligible_message = get_post_meta($pid,'bk_product_message',true);
        $url = '<div type="button" class="disabled grve-bg-hover-none" disabled>'.$eligible_message.'</div>';
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
      $slug = 'voucher-code-id-'.$ac_id.'-'.$pid;
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
          update_post_meta($voucher_id,'bk_voucher_product_bought', $product_id );
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
    $vouchers = bk_get_user_product_vouchers($user_login,$product_id);
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
  $codes_available = bk_activation_codes_available();
  $product = new WC_Product($product_id);
  $sku = $product->get_sku();
  $is_product_upgrade = bk_product_upgrade($product_id);
  $product_url = get_post_meta($product_id,'bk_product_url',true);
  if(empty($product_url)) {
    $product_url = esc_url(home_url('/my-account'));
  }
  if($codes_available){
    if($is_product_upgrade){
      $eligible = bk_current_user_eligible_to_upgrade($product_id,$sku);
      if($eligible) {

      } else {
        wc_add_notice( "You are not eligible to upgrade. Please register a product or buy a new one.", 'error' );
        wp_safe_redirect(esc_url($product_url));
        exit;
      }
    }
  } else {
    $admin_email = sanitize_email(get_option('admin_email'));
    $to = array( $admin_email, 'bravokeyl@gmail.com' );
    $subject = 'No Activation codes';
    $user = wp_get_current_user();

    if($user){
      $user_name = $user->user_login;
    }else {
      $user_name = "Guest";
    }

    $body = 'User '.$user_name.' tried to buy '.$sku.' but activation codes ran out, please add more codes.';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: Fable Sounds <wordpress@fablesounds.com>';
    wp_mail( $to, $subject, $body, $headers );
    wc_add_notice( "Sorry for the inconvenience : Activation codes are unavailable. We are notified.", 'error' );
    wp_safe_redirect(esc_url(home_url('/my-account')));
    exit;
  }
}


// add_filter('really_simple_csv_importer_save_meta', function($meta, $post, $is_update) {
//     foreach ($meta as $key => $value) {
//       if('bk_voucher_imp_date' == $key) {
//         if (strpos($value, ',') !== false) {
//             $_value = preg_split("/,+/", $value);
//             $meta[$key] = $_value;
//         }
//       }
//     }
//     return $meta;
// }, 10, 3);
