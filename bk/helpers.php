<?php
function bk_add_to_cart( $atts ) {
  $atts = shortcode_atts( array(
        'label' => 'Add to Cart',
        'id' => '',
        'type' => 'upgrade'
  ), $atts, 'fable_cart' );

  $pid = intval($atts['id']);
  $class = "grve-btn grve-btn-medium grve-round grve-bg-primary-1";
  $href= '';
  $eligible= true;

  if( "new" === $atts['type'] ){
    $href = 'href="/cart/?add-to-cart='.$pid.'"';
  } else {
    $href = 'href="/my-account/"';
    $atts['label'] = "Login to upgrade";
  }

  if( ! is_user_logged_in() ){
    $atts['label'] = "Login to upgrade";
    $url = '<a '.$href.' class="'.$class.'">'.$atts['label'].'</button>';
  } else{
    $href = 'href="/cart/?add-to-cart='.$pid.'"';
    if($eligible){
      $url = '<a data-quantity="1" '.$href;
      $url .= 'data-product_id="'.$pid.'" class="'.$class.'">><span class="grve-item">';
      $url .= '<i class="grve-menu-icon fa fa-shopping-cart"></i></span>'.$atts['label'].'</a>';
    } else {
      $url = '<button type="button" class="disabled grve-bg-hover-none" disabled>Not eligible to upgrade</button>';
    }
  }

  return $url;
}
add_shortcode( 'fable_cart', 'bk_add_to_cart' );
