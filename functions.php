<?php

include(get_stylesheet_directory().'/bk/post-types.php');
include(get_stylesheet_directory().'/bk/halion-post-type.php');
//include(get_stylesheet_directory().'/bk/account-fields.php');
include(get_stylesheet_directory().'/bk/meta.php');
include(get_stylesheet_directory().'/bk/halion-meta.php');

function bk_assign_activation_code_after_registration($order){
  //$order = new WC_Order( $order_id );

  $customer_obj = $order->get_user();
  $customer_email = sanitize_email($customer_obj->user_email);
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
		'orders'          => __( 'Orders', 'blade-child' ),
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

  if ( 'POST' == strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
    if ( empty( $_POST[ 'action' ] ) || 'save_register_keys_details' !== $_POST[ 'action' ] ||
    empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'save_register_keys_details' ) ) {
      return;
    }else {
      $bk_serial_key = strtoupper($_POST['bk_serial_key1'])."-".strtoupper($_POST['bk_serial_key2'])."-".strtoupper($_POST['bk_serial_key3'])."-".strtoupper($_POST['bk_serial_key4'])."-".strtoupper($_POST['bk_serial_key5']);
      // wp_die(print_r($bk_serial_key));
      $bk_serial_key_val     = ! empty( $bk_serial_key )? esc_attr($bk_serial_key) : '';
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

add_action( 'woocommerce_register_form_start', 'bk_extra_register_fields' );


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
}
add_action( 'woocommerce_created_customer', 'bk_save_extra_register_fields' );


function bk_add_serial_to_line_item( $order_data, $order ) {
    $order_data['serial_data'] = array();

    foreach ( $order->get_items() as $item_id => $item ) {
			$product     = $order->get_product_from_item( $item );
			$product_id  = null;
			$product_sku = null;

			if ( is_object( $product ) ) {
				$product_id  = ( isset( $product->variation_id ) ) ? $product->variation_id : $product->id;
				$product_sku = $product->get_sku();
			}
      //bk_assign_activation_code_after_registration($order);

      $quantity = intval($order_data['total_line_items_quantity']);
      $customer = $order_data['customer'];
      $customer_email = $customer->email;
      $activation_code_ids = bk_get_unused_activation_codes($quantity);

      for($i=0;$i<sizeof($activation_code_ids);$i++){
        update_post_meta($activation_code_ids[$i], 'bk_ac_status', "used");
        update_post_meta($activation_code_ids[$i], 'bk_ac_product_sku', $product_sku);
        update_post_meta($activation_code_ids[$i], 'bk_ac_user_email', $customer_email);
        update_post_meta($activation_code_ids[$i], 'bk_ac_date', current_time('mysql'));
      }

      $serial = "98290-56771-04051-40477-".wp_rand(1000,9999);
      $serial_data = array(
        "product_id" => $product_id,
        "product_sku" => $product_sku,
        "serial" => $serial
      );
			$order_data['serial_data'][] = $serial_data;
		}

    return $order_data;
}
add_filter( 'woocommerce_api_order_response', 'bk_add_serial_to_line_item', 10, 2 );
