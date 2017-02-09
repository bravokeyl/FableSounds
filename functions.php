<?php

include(get_stylesheet_directory().'/bk/post-types.php');
//include(get_stylesheet_directory().'/bk/account-fields.php');
include(get_stylesheet_directory().'/bk/meta.php');

function bk_assign_activation_code_after_registration($order_id){
  $order = new WC_Order( $order_id );

  // $customer_obj = $order->get_user();
  // $customer_email = sanitize_email($customer_obj->user_email);
  // $customer_login = $customer_obj->user_login;

  $order_ids = array();
  $order_items = $order->get_items();
  // wp_die(print_r($order_items));
  foreach ( $order_items as $item ) {
      $product_id = $item['product_id'];
      $pr = wc_get_product( $product_id );
	  	$psku = $pr->get_sku();
      // if($item['qty']) {
        array_push($order_ids,$psku);
      // }
      // $product_name = $item['name'];
      // $order_ids[$product_id] = $product_name;
  }
  // wp_die(print_r($order_ids));
  $order_num = sizeof($order_ids);

  if( $order_num > 0){
    $activation_code_ids = bk_get_unused_activation_codes($order_num);
    for($i=0;$i<sizeof($activation_code_ids);$i++){
      update_post_meta($activation_code_ids[$i], 'bk_ac_status', "used");
      update_post_meta($activation_code_ids[$i], 'bk_ac_product_sku', $order_ids[$i]);
      update_post_meta($activation_code_ids[$i], 'bk_ac_user_email', $order->get_user_id());
      update_post_meta($activation_code_ids[$i], 'bk_ac_date', current_time('mysql'));
    }
  }

}
add_action('woocommerce_order_status_completed','bk_assign_activation_code_after_registration');


function bk_get_unused_activation_codes($number){
  $code = array();
  $args = array(
    'post_type' => 'fs_activation_codes',
    'posts_per_page' => $number,
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
  //wp_die(print_r($query));
  if($query->have_posts()){
    while($query->have_posts()){
      $query->the_post();
      array_push($code, get_the_ID());
    }
    wp_reset_postdata();
  }

  return $code;
}


add_filter( 'woocommerce_account_menu_items', 'bk_upgrade_product_menu_item' );
function bk_upgrade_product_menu_item($items) {
  $items = array(
		'dashboard'       => __( 'Dashboard', 'blade-child' ),
		'orders'          => __( 'Orders', 'blade-child' ),
    'register-keys'       => __( 'Register a new product', 'blade-child' ),
    'registered-keycodes'   => __( 'Your activation codes', 'blade-child' ),
		'edit-address'    => __( 'Addresses', 'blade-child' ),
		'payment-methods' => __( 'Payment Methods', 'blade-child' ),
		'edit-account'    => __( 'Account Details', 'blade-child' ),
		'customer-logout' => __( 'Logout', 'blade-child' ),
	);
  return $items;
}

function bk_custom_endpoints() {
    add_rewrite_endpoint( 'register-keys', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'registered-keycodes', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'bk_custom_endpoints' );

function bk_custom_query_vars( $vars ) {
    $vars[] = 'register-keys';
    $vars[] = 'register-keycodes';
    return $vars;
}
add_filter( 'query_vars', 'bk_custom_query_vars', 0 );

add_action('woocommerce_account_register-keys_endpoint','bk_register_keys_endpoint');
function bk_register_keys_endpoint(){
  wc_get_template('myaccount/register-keys.php');
}
add_action('woocommerce_account_registered-keycodes_endpoint','bk_register_keycodes_endpoint');
function bk_register_keycodes_endpoint(){
  wc_get_template('myaccount/registered-keycodes.php');
}

function bk_register_keys_endpoint_title( $title ) {
    global $wp_query;

    $is_endpoint = isset( $wp_query->query_vars['register-keys'] );
    $is_registered = isset( $wp_query->query_vars['registered-keycodes'] );
    if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
        $title = __( 'Register a new product', 'bk' );

        remove_filter( 'the_title', 'bk_upgrade_endpoint_title' );
    }elseif( $is_registered && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ){
       $title = __( 'Your activation codes', 'bk' );

       remove_filter( 'the_title', 'bk_upgrade_endpoint_title' );
    }

    return $title;
}

add_filter( 'the_title', 'bk_register_keys_endpoint_title' );

function bk_check_serial_number($serial){
  $code = array();
  $args = array(
    'post_type' => 'fs_serial_numbers',
    'name'      => $serial,
    'posts_per_page' => '1',
    'meta_query' => array(
      array(
        'key' => 'bk_sn_status',
        'value' => "nreg",
        'compare' => '='
      )
    ),
    'post_status' => 'publish'
  );

  $query = new WP_Query($args);

  if($query->have_posts()){
    while($query->have_posts()){
      $query->the_post();
      array_push($code, get_the_ID());
    }
    wp_reset_postdata();
  }

  return $code;
}


add_action( 'template_redirect', 'bk_save_register_keys_details'  );
function bk_save_register_keys_details(){
  // wp_die(print_r("Hola!"));
  if ( 'POST' == strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
    if ( empty( $_POST[ 'action' ] ) || 'save_register_keys_details' !== $_POST[ 'action' ] ||
    empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'save_register_keys_details' ) ) {
      return;
    }else {
      $bk_serial_key_val     = ! empty( $_POST['bk_serial_key'] )? esc_attr($_POST['bk_serial_key']) : '';
      $products_dropdown_val = ! empty( $_POST['products_dropdown'] )? esc_attr($_POST['products_dropdown']): '';

      if ( !empty( $bk_serial_key_val ) ) {
        $serial_found = bk_check_serial_number($bk_serial_key_val);
        if(empty($serial_found)){
          wc_add_notice( __( 'Invalid Serial Number, please check it.', 'bk' ),'error' );
          wp_safe_redirect( wc_get_endpoint_url( 'register-keys' ) );
    			exit;
        } else {
          $bk_current_user = wp_get_current_user();
          update_post_meta(intval($serial_found[0]),'bk_sn_status','reg');
          update_post_meta(intval($serial_found[0]),'bk_sn_product_sku',$products_dropdown_val);
          update_post_meta(intval($serial_found[0]),'bk_sn_user_email',$bk_current_user->user_email);
          update_post_meta(intval($serial_found[0]),'bk_sn_date',current_time('mysql'));
          $activation_code_id = bk_get_unused_activation_codes(1);
          update_post_meta($activation_code_id[0], 'bk_ac_status', "used");
          update_post_meta($activation_code_id[0], 'bk_ac_serial_activation', get_the_title( $serial_found[0]));
          update_post_meta($activation_code_id[0], 'bk_ac_product_sku', $products_dropdown_val);
          update_post_meta($activation_code_id[0], 'bk_ac_user_email', $bk_current_user->user_email);
          update_post_meta($activation_code_id[0], 'bk_ac_date', current_time('mysql'));
          wc_add_notice( __( 'Serial Number successfully registered.', 'bk' ) );
          wp_safe_redirect( wc_get_endpoint_url( 'registered-keycodes' ) );
    			exit;
        }
      }
    }
  }
}

function bk_get_product_name_by_sku($productsku){
  switch(strtoupper($productsku)) {
    case "BGDR":
      $product_name = "Broadway Gig";
      break;
    case "BLDR":
      $product_name = "Broadway Lites";
      break;
    case "BKFDR":
      $product_name = "Broadway Big Band – Kontakt Edition";
      break;
    default:
      $product_name = "NA";
  }

  return $product_name;
}
