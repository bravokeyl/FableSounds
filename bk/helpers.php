<?php
function bk_add_to_cart( $atts ) {
  $atts = shortcode_atts( array(
        'label' => 'Add to Cart',
        'id' => ''
  ), $atts, 'fable_cart' );
  $pid = intval($atts['id']);
  $url = '<a rel="nofollow" href="/cart/?add-to-cart='.$pid.'" data-quantity="1"';
  $url .= 'data-product_id="'.$pid.'" class="grve-btn grve-btn-medium grve-round grve-bg-primary-1 grve-bg-hover-black
product_type_simple add_to_cart_button">><span class="grve-item">';
  $url .= '<i class="grve-menu-icon fa fa-shopping-cart"></i></span>'.$atts['label'].'</a>';

  return $url;
}
add_shortcode( 'fable_cart', 'bk_add_to_cart' );
