<?php

include(get_stylesheet_directory().'/bk/post-types.php');
include(get_stylesheet_directory().'/bk/halion-post-type.php');
//include(get_stylesheet_directory().'/bk/account-fields.php');
include(get_stylesheet_directory().'/bk/meta.php');
include(get_stylesheet_directory().'/bk/halion-meta.php');

function bk_assign_activation_code_after_registration($order_id){
  $order = new WC_Order( $order_id );

  $customer_obj = $order->get_user();
  $customer_email = sanitize_email($customer_obj->user_name);
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
      update_post_meta($activation_code_ids[$i], 'bk_ac_user_email', $customer_email);
      update_post_meta($activation_code_ids[$i], 'bk_ac_date', current_time('mysql'));
    }
  }

}
//add_action('woocommerce_order_status_completed','bk_assign_activation_code_after_registration');


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
		// 'orders'          => __( 'Orders', 'blade-child' ),
    'registered-keycodes'   => __( 'Your activation codes', 'blade-child' ),
    'register-keys'       => __( 'Register a new product', 'blade-child' ),
    'register-halion'       => __( 'Register HALion - powered BBB', 'blade-child' ),
		'edit-address'    => __( 'Addresses', 'blade-child' ),
		'payment-methods' => __( 'Payment Methods', 'blade-child' ),
		'edit-account'    => __( 'Account Details', 'blade-child' ),
		'customer-logout' => __( 'Logout', 'blade-child' ),
	);
  return $items;
}

function bk_custom_endpoints() {
    add_rewrite_endpoint( 'register-keys', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'register-halion', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'registered-keycodes', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'bk_custom_endpoints' );

function bk_custom_query_vars( $vars ) {
    $vars[] = 'register-keys';
    $vars[] = 'register-halion';
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
add_action('woocommerce_account_register-halion_endpoint','bk_register_halion_endpoint');
function bk_register_halion_endpoint(){
  wc_get_template('myaccount/register-halion.php');
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
    'status' => 'completed'
  );
  // $address_billing = "";
  $order = wc_create_order($args);
  $pid = wc_get_product_id_by_sku($sku);
  if ( is_wp_error( $order ) ) {
      return false;
  } else {
    $order->add_product( get_product( $pid ), 1 );
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
      // wp_die(print_r($bk_serial_key));
      $bk_serial_key_val     = ! empty( $bk_serial_key )? esc_attr($bk_serial_key) : '';

      if ( !empty( $bk_serial_key_val ) ) {
        $products_dropdown_val = ! empty( $_POST['bk_serial_key1'] )? esc_attr(strtoupper($_POST['bk_serial_key1'])): '';
        $serial_found = bk_check_serial_number($bk_serial_key_val,$products_dropdown_val);
        // wp_die(print_r($serial_found));
        if(empty($serial_found)){
          wc_add_notice( __( 'Invalid Serial Number, please check it.', 'bk' ),'error' );
          wp_safe_redirect( wc_get_endpoint_url( 'register-keys' ) );
    			exit;
        } else {
          $bk_current_user = wp_get_current_user();
          update_post_meta(intval($serial_found[0]),'bk_sn_status','reg');
          //update_post_meta(intval($serial_found[0]),'bk_sn_product_sku',$products_dropdown_val);
          update_post_meta(intval($serial_found[0]),'bk_sn_user_login',$bk_current_user->user_login);
          update_post_meta(intval($serial_found[0]),'bk_sn_date',current_time('mysql'));
          $activation_code_id = bk_get_unused_activation_codes(1);
          if(!empty($activation_code_id)) {
            update_post_meta($activation_code_id[0], 'bk_ac_status', "used");
            update_post_meta($activation_code_id[0], 'bk_ac_serial_activation', get_the_title( $serial_found[0]));
            update_post_meta($activation_code_id[0], 'bk_ac_product_sku', $products_dropdown_val);
            update_post_meta($activation_code_id[0], 'bk_ac_user_login', $bk_current_user->user_login);
            update_post_meta($activation_code_id[0], 'bk_ac_date', current_time('mysql'));
            bk_create_order($products_dropdown_val);
            wc_add_notice( __( 'Serial Number successfully registered.', 'bk' ) );
          } else {
            $to = get_option('admin_email');
            $subject = 'No activation codes';
            $body = 'No activation codes but the user '.$bk_current_user->user_login.' entered correct serial number';
            $headers = array('Content-Type: text/html; charset=UTF-8');
            wp_mail( $to, $subject, $body, $headers );
            wc_add_notice( __( 'Serial Number successfully registered and activation codes will be emailed to you.', 'bk' ) );
          }
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

include(get_stylesheet_directory().'/bk/halion/form-handler.php');


function bk_extra_register_fields() {
    ?>
    <p class="form-row form-row-first">
    <label for="reg_billing_first_name"><?php _e( 'First name', 'fablesounds' ); ?> <span class="required">*</span></label>
    <input type="text" required class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
    </p>

    <p class="form-row form-row-last">
    <label for="reg_billing_last_name"><?php _e( 'Last name', 'fablesounds' ); ?> <span class="required">*</span></label>
    <input type="text" required class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
    </p>

    <div class="clear"></div>

    <p class="form-row form-row-first">
    <label for="billing_country"><?php _e( 'Country', 'fablesounds' ); ?> <span class="required">*</span></label>
    <input type="text" required class="input-text" name="billing_country" id="billing_country" value="<?php if ( ! empty( $_POST['billing_country'] ) ) esc_attr_e( $_POST['billing_country'] ); ?>" />
    </p>

    <p class="form-row form-row-last">
    <label for="reg_billing_phone"><?php _e( 'Phone', 'fablesounds' ); ?></label>
    <input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php if ( ! empty( $_POST['billing_phone'] ) ) esc_attr_e( $_POST['billing_phone'] ); ?>" />
    </p>

    <div class="clear"></div>
    <?php
}

//add_action( 'woocommerce_register_form_start', 'bk_extra_register_fields' );


function bk_save_extra_register_fields( $customer_id ) {
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
}
add_action( 'woocommerce_created_customer', 'bk_save_extra_register_fields' );


function bk_add_serial_to_line_item( $order_data, $order ) {
    $order_data['serial_data'] = array();
    $quantity = intval($order_data['total_line_items_quantity']);

    $cemail = sanitize_email($order_data['billing_address']['email']);

    $serials = bk_get_unused_activation_codes($quantity);
    $serial_index = 0;


    foreach ( $order->get_items() as $item_id => $item ) {
			$product     = $order->get_product_from_item( $item );
			$product_id  = null;
			$product_sku = null;

			if ( is_object( $product ) ) {
				$product_id  = ( isset( $product->variation_id ) ) ? $product->variation_id : $product->id;
				$product_sku = $product->get_sku();
			}

      $serial_id = $serials[$serial_index];

      update_post_meta( $serial_id, 'bk_ac_status', "used" );
      update_post_meta( $serial_id, 'bk_ac_product_sku', $product_sku );
      update_post_meta( $serial_id, 'bk_ac_user_email', $cemail );
      update_post_meta( $serial_id, 'bk_ac_date', current_time('mysql') );

      update_post_meta( $serial_id, 'order_data', $order_data );

      $serial = get_the_title($serial_id);
      $serial_data = array(
        "product_id" => $product_id,
        "product_sku" => $product_sku,
        "serial" => $serial
      );

			$order_data['serial_data'][] = $serial_data;

      $serial_index++;
		}

    return $order_data;
}
add_filter( 'woocommerce_api_order_response', 'bk_add_serial_to_line_item', 10, 2 );


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
}
add_filter( 'wp_mail_from', function() {
    return 'wordpress@fablesounds.com';
});
