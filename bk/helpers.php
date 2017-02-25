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
function bk_mail_insufficient_serial_codes($sku,$username){
  $admin_email = sanitize_email(get_option('admin_email'));
  $to = array( $admin_email, 'bravokeyl@gmail.com' );
  $subject = 'No Serial codes for product '.$sku;
  $body = 'User '.$username.' tried to buy product '.$sku.' but serial numbers codes ran out, please add more codes.';
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

function bk_assign_serial_number( $sku,$codes_count = '1' ){
  $code = array();
  $args = array(
    'post_type' => 'fs_serial_numbers',
    'posts_per_page' => 1,
    'meta_query' => array(
      'relation' => 'AND',
      array(
        'key' => 'bk_sn_status',
        'value' => "nreg",
        'compare' => '='
      ),
      array(
        'key' => 'bk_sn_distributed',
        'value' => '0',
        'compare' => '='
      ),
      array(
        'key' => 'bk_sn_product_sku',
        'value' => $sku,
        'compare' => '='
      ),
      array(
        'key' => 'bk_sn_activation_code_count',
        'value' => $codes_count,
        'compare' => '='
      ),
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
        $eligible = bk_current_user_eligible_to_upgrade($pid);
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

function bk_get_user_product_vouchers($username,$sku,$product_bought){
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
      // array(
      //   'key'   => 'bk_voucher_product_bought',
      //   'value' => $sku,
      //   'compare' => '='
      // )
    )
  );
  $vquery = new WP_Query($args);
  $vouchers_found = $vquery->found_posts;

  return $vouchers_found;
}

/* Handle upgrades */
function bk_current_user_eligible_to_upgrade($product_id) {

  global $woocommerce;
  $current_user= wp_get_current_user();
  $customer_email = $current_user->email;
  $user_id = $current_user->ID;
  $user_login = $current_user->user_login;
  // $product_bought = get_post_meta($product_id);
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
  $user = wp_get_current_user();
  if($user->ID){
    $user_name = $user->user_login;
  } else {
    $user_name = "Guest";
  }
  $product = new WC_Product($product_id);
  $sku = $product->get_sku();
  $asku = get_post_meta($product_id,'_activation_sku',true);
  $codes_available = bk_get_unused_activation_codes( 1, $asku );

  $bk_add_to_cart_logger = new WC_Logger();
  $bk_add_to_cart_logger->add('fablesounds', 'Debug: '.$user_name.' is trying to add product with SKU : '.$sku.' (activation SKU: '.$asku.') to the cart.');

  $serials_available = bk_assign_serial_number($sku);
  $is_product_upgrade = bk_product_upgrade($product_id);
  $product_url = get_post_meta($product_id,'bk_product_url',true);

  if(empty($product_url)) {
    $product_url = esc_url(home_url('/my-account'));
  }

  if($codes_available && (0 < count($codes_available)) ){
    if($serials_available && (0 < count($serials_available)) ){
      if($is_product_upgrade){
        $eligible = bk_current_user_eligible_to_upgrade($product_id);
        if(!$eligible) {
          $bk_add_to_cart_logger->add('fablesounds', 'Info: Ineligible to buy product SKU: '.$sku.' (activation SKU: '.$asku.'), user: '.$user_name);
          wc_add_notice( "You are not eligible to upgrade. Please register a product or buy a new one.", 'error' );
          wp_safe_redirect(esc_url($product_url));
          exit;
        } // Ineligible to upgrade
      } // end is product upgrade
    } else {
      // No serials available for product
      $bk_add_to_cart_logger->add('fablesounds', 'Error: No serial codes found for product SKU: '.$sku.' (activation SKU: '.$asku.'), user: '.$user_name);
      bk_mail_insufficient_serial_codes($sku,$user_name);
      wc_add_notice( "This product is out of stock. We are notified. Please check back later.", 'error' );
      wp_safe_redirect(esc_url(home_url('/my-account')));
      exit;
    }
  } else {
    // No activation codes available
    $bk_add_to_cart_logger->add('fablesounds', 'Error: No activation codes found for product SKU '.$sku.' (activation SKU: '.$asku.'), user: '.$user_name);
    $admin_email = sanitize_email(get_option('admin_email'));
    $to = array( $admin_email, 'bravokeyl@gmail.com' );
    $subject = 'No Activation codes';
    $body = 'User '.$user_name.' tried to buy '.$sku.' but activation codes ran out, please add more codes.';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: Fable Sounds <wordpress@fablesounds.com>';
    wp_mail( $to, $subject, $body, $headers );
    wc_add_notice( "This product is out of stock. We are notified. Please check back later.", 'error' );
    wp_safe_redirect(esc_url(home_url('/my-account')));
    exit;
  }
}

function bk_change_voucher_status($product_id,$username){
  $args = array(
    'post_type' => 'fs_vouchers',
    'post_status' => 'publish',
    'posts_per_page' => '1',
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
        'value' => $product_id,
        'compare' => '='
      ),
    )
  );
  $bk_vLogger = new WC_Logger();
  $vquery = new WP_Query($args);
  $bk_vLogger->add('debug','Changing Voucher status for voucher : '.$vquery->request.' found for user : '.$username.' Product ID: '.$product_id);
  if($vquery->have_posts()){
    while($vquery->have_posts()){
      $vquery->the_post();
      $vid = get_the_ID();

      $bk_vLogger->add('debug','Changing Voucher status for voucher : '.$vid.' found for user : '.$username.' Product ID: '.$product_id);
      update_post_meta($vid,'bk_voucher_status','used');
      update_post_meta($vid,'bk_voucher_used_date', current_time('mysql'));
    }
    wp_reset_postdata();
    return true;
  } else {
    $bk_apiLogger->add('debug','Unable to change the status for voucher as no voucher found for product id: '.$product_id.' Username '.$username);
    return false;
  }

}

add_action('woocommerce_customer_save_address','bk_log_customer_address',10,2);
function bk_log_customer_address( $user_id, $load_address) {
  $account_logger = new WC_Logger();
  $user = get_user_by('id',$user_id);
  $email = get_option( 'admin_email', '' );
  $subject = "Customer Saved/Changed Address";
  // format email
  $message = 'Username: ' . $user->user_login . "\n";
  $message .= 'User email: ' . $user->user_email . "\n";
  $message .= 'User first name: ' . $user->user_firstname . "\n";
  $message .= 'User last name: ' . $user->user_lastname . "\n";
  $message .= "\n";
  $message .= "\n";
  // make sure we have all of the required data
  if ( empty ( $email ) ) {
    return;
  }
  // send email
  // wp_mail( $email, $subject, $message );
  $account_logger->add('account-address','User: '.$user->user_login.'('.$user->user_email.') saved their address');

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
