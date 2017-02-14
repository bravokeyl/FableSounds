<?php
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

/* Handle upgrades and updates */
function bk_current_user_eligible_to_upgrade($product_id) {

  $product_ids= array ('2258','2253','2242');
  global $woocommerce;
  $current_user= wp_get_current_user();
  $customer_email = $current_user->email;
  $user_id = $current_user->ID;

  // foreach($product_ids as $item){
    if ( wc_customer_bought_product( $customer_email, $user_id, $product_id) ){
      return true;
    }
  // }
  return false;
}

add_action('woocommerce_add_to_cart','bk_check_add_to_cart',10,6);
function bk_check_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
  $eligible = bk_current_user_eligible_to_upgrade($product_id);
  if($eligible) {

  } else {
    wc_add_notice( "You are not eligible to upgrade. Please register a product or buy a new one.", 'error' );
    wp_safe_redirect( wc_get_endpoint_url( 'my-account' ) );
    exit;
  }

}
