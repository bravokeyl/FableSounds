<?php

include(get_stylesheet_directory().'/bk/serials/serials.php');
include(get_stylesheet_directory().'/bk/codes/codes.php');
include(get_stylesheet_directory().'/bk/halion-post-type.php');
include(get_stylesheet_directory().'/bk/vouchers/vouchers.php');
include(get_stylesheet_directory().'/bk/meta.php');
include(get_stylesheet_directory().'/bk/halion-meta.php');
include(get_stylesheet_directory().'/bk/icontact.php');
include(get_stylesheet_directory().'/bk/helpers.php');
include(get_stylesheet_directory().'/bk/woo-settings.php');

function bk_get_unused_activation_codes($number,$sku){
  $code = array();
  $args = array(
    'post_type' => 'fs_activation_codes',
    'posts_per_page' => $number,
    'meta_query' => array(
      'relation' => 'AND',
      array(
        'key' => 'bk_ac_status',
        'value' => "nused",
        'compare' => '='
      ),
      array(
        'key' => 'bk_ac_product_sku',
        'value' => $sku,
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
  $registered_products = get_option('wc_settings_registered_products_title', 'Registered Products');
  $register_new = get_option('wc_settings_register_new_product_title','Register a new product');
  $registered_halion = get_option('wc_settings_register_halion_title','Register HALion - powered BBB');
  $available_upgrades_updates = get_option('wc_settings_available_updates_upgrades_title','Available Updates/Upgrades');

  $registered_products = empty($registered_products) ? 'Registered Products': $registered_products;
  $register_new = empty($register_new) ? 'Register a new product': $register_new;
  $registered_halion = empty($registered_halion) ?'Register HALion - powered BBB': $registered_halion;
  $available_upgrades_updates = empty($available_upgrades_updates) ?'Available Updates/Upgrades': $available_upgrades_updates;


  $items = array(
		'dashboard'       => __( 'Dashboard', 'blade-child' ),
		// 'orders'          => __( 'Orders', 'blade-child' ),
    'registered-keycodes'   => $registered_products,
    'register-keys'       => $register_new,
    'register-halion'       => $registered_halion,
    'available-updates-upgrades' => $available_upgrades_updates,
		'edit-address'    => __( 'Address', 'blade-child' ),
		//'payment-methods' => __( 'Payment Methods', 'blade-child' ),
		'edit-account'    => __( 'Account Details', 'blade-child' ),
		'customer-logout' => __( 'Logout', 'blade-child' ),
	);
  return $items;
}

function bk_custom_endpoints() {
    add_rewrite_endpoint( 'register-keys', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'register-halion', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'registered-keycodes', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'available-updates-upgrades', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'bk_custom_endpoints' );

function bk_custom_query_vars( $vars ) {
    $vars[] = 'register-keys';
    $vars[] = 'register-halion';
    $vars[] = 'register-keycodes';
    $vars[] = 'available-updates-upgrades';
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
add_action('woocommerce_account_register-halion_endpoint','bk_register_halion_endpoint');
function bk_register_halion_endpoint(){
  wc_get_template('myaccount/register-halion.php');
}
add_action('woocommerce_account_available-updates-upgrades_endpoint','bk_available_updates_upgrades_endpoint');
function bk_available_updates_upgrades_endpoint(){
  wc_get_template('myaccount/available-updates-upgrades.php');
}

function bk_register_keys_endpoint_title( $title ) {
    global $wp_query;

    $is_endpoint = isset( $wp_query->query_vars['register-keys'] );
    $is_registered = isset( $wp_query->query_vars['registered-keycodes'] );
    $is_halion = isset( $wp_query->query_vars['register-halion'] );
    if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
        $title = __( 'Register a new product', 'bk' );

        remove_filter( 'the_title', 'bk_register_keys_endpoint_title' );
    }elseif( $is_registered && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ){
       $title = __( 'Your activation codes', 'bk' );

       remove_filter( 'the_title', 'bk_register_keys_endpoint_title' );
    }elseif( $is_halion && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ){
       $title = __( 'Register HALion - powered BBB', 'bk' );

       remove_filter( 'the_title', 'bk_register_keys_endpoint_title' );
    }

    return $title;
}

add_filter( 'the_title', 'bk_register_keys_endpoint_title' );

function bk_check_serial_number($serial,$sku){
  $code = array();
  $args = array(
    'post_type' => 'fs_serial_numbers',
    'name'      => $serial,
    'posts_per_page' => '1',
    'meta_query' => array(
      'relation' => 'AND',
      array(
        'key' => 'bk_sn_status',
        'value' => "nreg",
        'compare' => '='
      ),
      array(
        'key' => 'bk_sn_product_sku',
        'value' => $sku,
        'compare' => '='
      ),
      array(
        'key' => 'bk_sn_distributed',
        'value' => '1',
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

function bk_create_order($sku){
  $args = array(
    'status' => 'processing'
  );
  // $address_billing = "";
  $order = wc_create_order($args);
  $pid = wc_get_product_id_by_sku($sku);
  if ( is_wp_error( $order ) ) {
      return false;
  } else {
    $order->add_product( get_product( $pid ), 1 );
    $order->update_status('completed', 'Programatically changing order status');
    update_post_meta($order->id, '_customer_user', get_current_user_id() );
    update_post_meta($order->id, 'bk_order_type', 'register' );
    return $order;
  }
  // $order->set_address( $address_billing, 'billing' );
}

add_action( 'template_redirect', 'bk_save_register_keys_details'  );
function bk_save_register_keys_details(){

  if ( 'POST' == strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
    if ( empty( $_POST[ 'action' ] ) || 'save_register_keys_details' !== $_POST[ 'action' ] ||
    empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'save_register_keys_details' ) ) {
      return;
    }else {
      $bk_serial_key = strtoupper($_POST['bk_serial_key1'])."-".strtoupper($_POST['bk_serial_key2'])."-".strtoupper($_POST['bk_serial_key3'])."-".strtoupper($_POST['bk_serial_key4'])."-".strtoupper($_POST['bk_serial_key5']);
      $bk_serial_key_val     = ! empty( $bk_serial_key )? esc_attr($bk_serial_key) : '';

      if ( !empty( $bk_serial_key_val ) ) {
        $bk_registerLogger = new WC_Logger();

        $products_dropdown_val = ! empty( $_POST['bk_serial_key1'] )? esc_attr(strtoupper($_POST['bk_serial_key1'])): '';
        $serial_found = bk_check_serial_number($bk_serial_key_val,$products_dropdown_val);
        $activation_code_id = bk_get_unused_activation_codes(1,$products_dropdown_val);
        if( 0 < count($activation_code_id )){
          if(empty($serial_found)){
            wc_add_notice( __( 'Invalid Serial Number, please check it.', 'bk' ),'error' );
            wp_safe_redirect( wc_get_endpoint_url( 'register-keys' ) );
      			exit;
          } else {
            $bk_current_user = wp_get_current_user();
            $username = $bk_current_user->user_login;
            $bk_registerLogger->add('fablesounds','Debug: User: '.$username.' is registering a key '.$bk_serial_key);

            update_post_meta(intval($serial_found[0]),'bk_sn_status','reg');
            update_post_meta(intval($serial_found[0]),'bk_sn_user_login',$username);
            update_post_meta(intval($serial_found[0]),'bk_sn_date',current_time('mysql'));

            if(!empty($activation_code_id)) {
              $bk_registerLogger->add('fablesounds','Debug: Activation code given to '.$username.' for registering a key '.$bk_serial_key.' : '.$activation_code_id[0]);
              update_post_meta($activation_code_id[0], 'bk_ac_status', "used");
              update_post_meta($activation_code_id[0], 'bk_ac_serial_activation', get_the_title( $serial_found[0]));
              update_post_meta($activation_code_id[0], 'bk_ac_product_sku', $products_dropdown_val);
              update_post_meta($activation_code_id[0], 'bk_ac_user_login', $bk_current_user->user_login);
              update_post_meta($activation_code_id[0], 'bk_ac_date', current_time('mysql'));
              $selected_product_id = wc_get_product_id_by_sku( $products_dropdown_val );
              $voucher_id = bk_assign_voucher_to_user($username,$activation_code_id[0],$selected_product_id,$products_dropdown_val);

              bk_create_order($products_dropdown_val);
              $icontact_id = get_user_meta($bk_current_user->ID,'bk_icontact_id',true);
              global $icontact_lists;
              add_user_to_list($icontact_id,$icontact_lists[$products_dropdown_val]);
              wc_add_notice( __( 'Serial Number successfully registered.', 'bk' ) );
            } else {
              $to = get_option('admin_email');
              $subject = 'No activation codes';
              $body = 'No activation codes but the user '.$username.' entered correct serial number';
              $headers = array('Content-Type: text/html; charset=UTF-8');
              wp_mail( $to, $subject, $body, $headers );
              wc_add_notice( __( 'Serial Number successfully registered and activation codes will be emailed to you.', 'bk' ) );
            }

            wp_safe_redirect( wc_get_endpoint_url( 'registered-keycodes' ) );
            exit;
          }
        }else {
          $to = get_option('admin_email');
          $subject = 'No activation codes';
          $body = 'No activation codes but the user '.$username.' entered correct serial number';
          $headers = array('Content-Type: text/html; charset=UTF-8');
          wp_mail( $to, $subject, $body, $headers );
          wc_add_notice( __( 'We are unable to complete your registration at this time. Please contact us at contact@fablesounds.email for help or try again later.', 'bk' ),'error' );
          wp_safe_redirect( wc_get_endpoint_url( 'my-account' ) );
          exit;
        }
      }
    }
  }
}

include(get_stylesheet_directory().'/bk/halion/form-handler.php');

function bk_save_extra_register_fields( $customer_id ) {
    $firstName = sanitize_text_field( $_POST['billing_first_name'] );
    $lastName = sanitize_text_field( $_POST['billing_first_name'] );
    $email = sanitize_text_field( $_POST['email'] );
    $user = get_user_by('email',$email);
    $user_name = $user->user_login;
    // wp_die(print_r($user));
    if ( isset( $_POST['billing_phone'] ) ) {
       update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
    }
    if ( isset( $_POST['billing_country'] ) ) {
       update_user_meta( $customer_id, 'billing_country', sanitize_text_field( $_POST['billing_country'] ) );
    }
    if ( isset( $_POST['billing_first_name'] ) ) {
       update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
       update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
    }
    if ( isset( $_POST['billing_last_name'] ) ) {
       update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
       update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
    }
    if ( isset( $_POST['billing_company'] ) ) {
       update_user_meta( $customer_id, 'billing_company', sanitize_text_field( $_POST['billing_company'] ) );
    }
    if ( isset( $_POST['billing_address_1'] ) ) {
       update_user_meta( $customer_id, 'billing_address_1', sanitize_text_field( $_POST['billing_address_1'] ) );
    }
    if ( isset( $_POST['billing_address_2'] ) ) {
       update_user_meta( $customer_id, 'billing_address_2', sanitize_text_field( $_POST['billing_address_2'] ) );
    }
    if ( isset( $_POST['billing_city'] ) ) {
       update_user_meta( $customer_id, 'billing_city', sanitize_text_field( $_POST['billing_city'] ) );
    }
    if ( isset( $_POST['billing_state'] ) ) {
       update_user_meta( $customer_id, 'billing_state', sanitize_text_field( $_POST['billing_state'] ) );
    }
    if ( isset( $_POST['billing_postcode'] ) ) {
       update_user_meta( $customer_id, 'billing_postcode', sanitize_text_field( $_POST['billing_postcode'] ) );
    }

    $icontact_res = add_user_to_icontact($email, $firstName, $lastName, $user_name);
    $icontact_id = get_contact_id($user_name);
    $bk_wclogger = new WC_Logger();
    $bk_wclogger->add('info','Adding user to icontact: icontact id - '.$icontact_id);
    update_user_meta($user->ID,'bk_icontact_id',$icontact_id);
}
add_action( 'woocommerce_created_customer', 'bk_save_extra_register_fields' );

add_filter( 'woocommerce_checkout_fields' , 'bk_woo_no_order_notes' );
function bk_woo_no_order_notes( $fields ) {
   unset($fields['order']['order_comments']);
   return $fields;
}

function bk_validate_extra_register_fields( $username, $email, $validation_errors ) {
  if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
     $validation_errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: First name is required!', 'woocommerce' ) );
  }
  if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
     $validation_errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'woocommerce' ) );
  }
  if ( isset( $_POST['billing_address_1'] ) && empty( $_POST['billing_address_1'] ) ) {
     $validation_errors->add( 'billing_address_1_error', __( '<strong>Error</strong>: Address is required!.', 'woocommerce' ) );
  }
  if ( isset( $_POST['billing_country'] ) && empty( $_POST['billing_country'] ) ) {
     $validation_errors->add( 'billing_country_error', __( '<strong>Error</strong>: City is required!.', 'woocommerce' ) );
  }
  if ( isset( $_POST['billing_city'] ) && empty( $_POST['billing_city'] ) ) {
     $validation_errors->add( 'billing_city_error', __( '<strong>Error</strong>: City is required!.', 'woocommerce' ) );
  }

  if ( isset( $_POST['billing_postcode'] ) && empty( $_POST['billing_postcode'] ) ) {
     $validation_errors->add( 'billing_postcode_error', __( 'PostCode / Zip is required!.', 'woocommerce' ) );
  }
  return $validation_errors;
}

add_action( 'woocommerce_register_post', 'bk_validate_extra_register_fields', 10, 3 );

function bk_custom_reg_form(){
  global $woocommerce;
  $checkout = $woocommerce->checkout();

  foreach ($checkout->checkout_fields['billing'] as $key => $field) :
    if($key == 'billing_phone'){
      $field['required'] = false;
    }
    if(($key == 'billing_company')){
      $field['class'] = array(
        'form-row-first'
      );
    }
    if(!($key == 'billing_email')){
      //print_r($field);
      woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
    }

  endforeach;
}
add_action('woocommerce_register_form','bk_custom_reg_form');

add_action('wp_enqueue_scripts','bk_enqueue_scripts');
function bk_enqueue_scripts(){
  $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
  $assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
  $frontend_script_path = $assets_path . 'js/frontend/';
  // make sure to enqueue this only on login reg page
  wp_enqueue_script( 'wc-country-select', $frontend_script_path . 'country-select' . $suffix . '.js' );
  if(is_account_page()){
    wp_enqueue_script( 'bk-js', get_stylesheet_directory_uri().'/bk/js/bk.js', array('jquery'),'', true );
  }
}
add_filter( 'wp_mail_from', function() {
    return 'wordpress@fablesounds.com';
});


add_filter('woocommerce_return_to_shop_redirect','bk_return_empty_cart_shop_url');
function bk_return_empty_cart_shop_url() {
  return esc_url(home_url('/'));
}

include(get_stylesheet_directory().'/bk/codes.php');

add_filter('woocommerce_max_webhook_delivery_failures','bk_max_webhook_failures');
function bk_max_webhook_failures(){
  return 1000;
}
