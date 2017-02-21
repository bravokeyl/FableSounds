<?php
add_action('woocommerce_payment_complete', 'bk_assign_vouchers');
function bk_assign_vouchers($order_id){
  $order = new WC_Order( $order_id );
  $username = "Guest";
  $user = $order->get_user();
  $items = $order->get_items();
  $username = $user->user_login;
  $quantity = intval($order->get_item_count());
  $serials = bk_get_unused_activation_codes($quantity);
  $serial_index = 0;
  if(count($serials) == $quantity ) {
    foreach ( $items as $item_id => $item ) {
      $product     = $order->get_product_from_item( $item );
      $product_id  = null;
      $product_sku = null;

      if ( is_object( $product ) ) {
        $product_id  = ( isset( $product->variation_id ) ) ? $product->variation_id : $product->id;
        $product_sku = $product->get_sku();
      }
      bk_assign_voucher_to_user($username,$serials[$serial_index],$product_id,$product_sku);
      $serial_index++;
    }
  } else {
    //Email to Admin "Shortage of Activation codes"
    bk_mail_insufficient_activation_codes();
  }
  bk_add_user_to_list($username);
  return $order_id;
}

// add_action('woocommerce_payment_complete', 'bk_add_user_to_list',12);
function bk_add_user_to_list($username){
  $icontact_id = get_contact_id($user_name);
  if($icontact_id) {

  }else {
    $icontact_res = add_user_to_icontact($email, $firstName, $lastName, $user_name);
    $icontact_id = get_contact_id($user_name);
    $bk_wclogger = new WC_Logger();
    $bk_wclogger->add('info','Adding user to icontact: icontact id - '.$icontact_id);
    update_user_meta($user->ID,'bk_icontact_id',$icontact_id);
    // global $icontact_lists;
    // add_user_to_list($icontact_id,$icontact_lists[$products_dropdown_val]);
  }

}

function bk_add_serial_to_line_item( $order_data, $order ) {
    $order_data['serial_data'] = array();
    $quantity = intval($order_data['total_line_items_quantity']);

    $cemail = sanitize_email($order_data['billing_address']['email']);
    $bk_apiLogger = new WC_Logger();
    $bk_order_id = $order_data['id'];
    $bk_customer_id = get_post_meta( $bk_order_id, '_customer_user', true );
    $customer_obj = get_user_by('id',$bk_customer_id);
    $customer_username = $customer_obj->user_login;

    if( 'completed' == $order_data['status'] ){
      $bk_apiLogger->add('debug','Continuata Webhook Fired: Order updates with status '.$order_data['status']);
      $serials = bk_get_unused_activation_codes($quantity);
      $bk_apiLogger->add('debug','Continuata Webhook Fired: Serials found : '.count($serials).' Quantity required: '.$quantity);
      if($quantity == count($serials)){
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
          $bk_apiLogger->add('debug','Continuata Webhook Fired: Order '.$order_data['order_number'].' : '.$product_sku.' - '.$cemail.' - Serial ID:'. $serial_id);

          update_post_meta( $serial_id, 'bk_ac_status', "used" );
          update_post_meta( $serial_id, 'bk_ac_product_sku', $product_sku );
          update_post_meta( $serial_id, 'bk_ac_user_email', $cemail );
          update_post_meta( $serial_id, 'bk_ac_user_login', $customer_username );
          update_post_meta( $serial_id, 'bk_ac_date', current_time('mysql') );

          update_post_meta( $serial_id, 'order_data', $order_data );

          $continuata_sku = get_post_meta($product_id,'_continuata_sku',true);

          $serial = get_the_title($serial_id);
          $serial_data = array(
            "product_id" => $product_id,
            "product_sku" => $continuata_sku,
            "serial" => $serial
          );

    			$order_data['serial_data'][] = $serial_data;

          // bk_create_serial_number($product_sku,$order_data['order_number'],$serial_id,$customer_username);
          $nr_serial = bk_assign_serial_number($quantity);
          if($quantity == count($nr_serial)){
            update_post_meta( $nr_serial[$serial_index], 'bk_ac_status', "used" );
            update_post_meta( $nr_serial[$serial_index], 'bk_ac_product_sku', $product_sku );
            update_post_meta( $nr_serial[$serial_index], 'bk_ac_user_email', $cemail );
            update_post_meta( $nr_serial[$serial_index], 'bk_ac_user_login', $customer_username );
            update_post_meta( $nr_serial[$serial_index], 'bk_ac_date', current_time('mysql') );
          } else {
            $admin_email = sanitize_email(get_option('admin_email'));
            $to = array( $admin_email, 'bravokeyl@gmail.com' );
            $subject = 'Not enough serial numbers';
            $body = 'Not enough serial numbers.';
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $headers[] = 'From: Fable Sounds <wordpress@fablesounds.com>';
            wp_mail( $to, $subject, $body, $headers );
          }
          $serial_index++;
    		} // foreach
        return $order_data;
      } else {
        $bk_apiLogger->add('error','Continuata Webhook Fired: Order id '.$order_data['order_number']);
        $bk_apiLogger->add('error','Continuata Webhook Fired: Shortage of Activation codes');
        $bk_apiLogger->add('error','Continuata Webhook Fired: Activation codes Required: '.$quantity.' : Available '.count($serials));
        bk_mail_insufficient_activation_codes();
        return false;
      }
    } else {
      $bk_apiLogger->add('debug','Webhook Fired: Order updates with status '.$order_data['status']);
    }

    return false;
}
add_filter( 'woocommerce_api_order_response', 'bk_add_serial_to_line_item', 10, 2 );


add_action('woocommerce_payment_complete', 'bk_check_codes_quantity',15);
function bk_check_codes_quantity(){
  $all = '-1';
  $serials = bk_get_unused_activation_codes($all);
  $count = count($serials);
  if(20 > $count) {
    bk_mail_insufficient_activation_codes($count);
  }
}
