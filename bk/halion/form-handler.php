<?php
function bk_check_halion_keys($halion_serial){
  $code = array();
  $args = array(
    'post_type' => 'fs_halion_codes',
    // 'name'      => $serial,
    'posts_per_page' => '1',
    'meta_query' => array(
      'relation' => 'AND',
      array(
        'key' => 'bk_halion_status',
        'value' => "nreg",
        'compare' => '='
      ),
      array(
        'key' => 'bk_halion_brass_code',
        'value' => $halion_serial[0],
        'compare' => '='
      ),
      array(
        'key' => 'bk_halion_reeds_code',
        'value' => $halion_serial[1],
        'compare' => '='
      ),
      array(
        'key' => 'bk_halion_rythm_code',
        'value' => $halion_serial[2],
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
add_action( 'template_redirect', 'bk_register_halion_keys'  );
function bk_register_halion_keys(){

  if ( 'POST' == strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
    if ( empty( $_POST[ 'action' ] ) || 'bk_register_halion_keys' !== $_POST[ 'action' ] ||
    empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'bk_register_halion_keys' ) ) {
      return;
    }else {

      $halion_keys = $_POST;
      if(is_array($halion_keys) && !empty( $halion_keys )){
        $halion_serial = array("","","");
        for($i=1;$i<9;$i++) {
          $brass_key = "bk_old_halion_key1".$i;
          $reeds_key = "bk_old_halion_key2".$i;
          $rythm_key = "bk_old_halion_key3".$i;
          $halion_serial[0] .= esc_attr(strtoupper($_POST[$brass_key]));
          $halion_serial[1] .= esc_attr(strtoupper($_POST[$reeds_key]));
          $halion_serial[2] .= esc_attr(strtoupper($_POST[$rythm_key]));
        }
      }
      // wp_die(print_r($halion_serial));
      if ( !empty( $halion_serial ) ) {
        $serial_found = bk_check_halion_keys($halion_serial);
        if(empty($serial_found)){
          wc_add_notice( __( 'Invalid Serial Number, please check it.', 'bk' ),'error' );
          wp_safe_redirect( wc_get_endpoint_url( 'register-keys' ) );
    			exit;
        } else {
          $bk_current_user = wp_get_current_user();
          update_post_meta(intval($serial_found[0]),'bk_halion_status','reg');
          // update_post_meta(intval($serial_found[0]),'bk_halion_codes', $halion_serial);
          // update_post_meta(intval($serial_found[0]),'bk_halion_brass_code', $halion_serial[0]);
          // update_post_meta(intval($serial_found[0]),'bk_halion_reeds_code', $halion_serial[0]);
          // update_post_meta(intval($serial_found[0]),'bk_halion_rythm_code', $halion_serial[0]);
          // update_post_meta(intval($serial_found[0]),'bk_sn_product_sku',$products_dropdown_val);
          update_post_meta(intval($serial_found[0]),'bk_halion_user_email',$bk_current_user->user_email);
          update_post_meta(intval($serial_found[0]),'bk_halion_date',current_time('mysql'));

          wc_add_notice( __( 'Serial Number successfully registered.', 'bk' ) );
          wp_safe_redirect( wc_get_endpoint_url( 'registered-keycodes' ) );
    			exit;
        }
      }
    }
  }
}
