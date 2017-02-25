<?php
function bk_check_halion_keys($serial,$type){
  $code = array();
  $args = array(
    'post_type' => 'fs_halion_codes',
    'posts_per_page' => '1',
    'name' => $serial,
    'meta_query' => array(
      'relation' => 'AND',
      array(
        'key' => 'bk_halion_status',
        'value' => "nreg",
        'compare' => '='
      ),
      array(
        'key' => 'bk_halion_code_type',
        'value' => $type,
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
          $sep = '-';
          if( 8 == $i ){
            $sep = '';
          }
          $halion_serial[0] .= esc_attr(strtoupper($_POST[$brass_key])).$sep;
          $halion_serial[1] .= esc_attr(strtoupper($_POST[$reeds_key])).$sep;
          $halion_serial[2] .= esc_attr(strtoupper($_POST[$rythm_key])).$sep;
        }
      }

      if ( !empty( $halion_serial[0] ) && !empty( $halion_serial[1] ) && !empty( $halion_serial[2] ) ) {
        $brass_serial = bk_check_halion_keys($halion_serial[0],"brass");
        $reeds_serial = bk_check_halion_keys($halion_serial[1],"reeds");
        $rythm_serial = bk_check_halion_keys($halion_serial[2],"rythm");
        if(empty($brass_serial) || empty($reeds_serial) || empty($rythm_serial)){
          wc_add_notice( __( 'Invalid Serial Number, please check it.', 'fablesounds' ),'error' );
          wp_safe_redirect( wc_get_endpoint_url( 'register-halion' ) );
    			exit;
        } else {
          $bk_current_user = wp_get_current_user();
          update_post_meta(intval($brass_serial[0]),'bk_halion_status','reg');
          update_post_meta(intval($reeds_serial[0]),'bk_halion_status','reg');
          update_post_meta(intval($rythm_serial[0]),'bk_halion_status','reg');
          update_post_meta(intval($brass_serial[0]),'bk_halion_user_login',$bk_current_user->user_login);
          update_post_meta(intval($reeds_serial[0]),'bk_halion_user_login',$bk_current_user->user_login);
          update_post_meta(intval($rythm_serial[0]),'bk_halion_user_login',$bk_current_user->user_login);
          update_post_meta(intval($brass_serial[0]),'bk_halion_date',current_time('mysql'));
          update_post_meta(intval($reeds_serial[0]),'bk_halion_date',current_time('mysql'));
          update_post_meta(intval($rythm_serial[0]),'bk_halion_date',current_time('mysql'));

          wc_add_notice( __( 'Halion product serial number successfully registered.', 'fablesounds' ) );
          wp_safe_redirect( wc_get_endpoint_url( 'registered-keycodes' ) );
    			exit;
        }
      }
    }
  }
}
