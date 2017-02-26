<?php
function bk_halion_voucher_to_user($username,$ac_id,$product_id,$product_sku){
  $voucher_id = -1;
  $author_id = 1;

  $bk_wclogger = new WC_Logger();
  $sku_arr = get_post_meta($product_id,'bk_eligible_products', true);
  if(is_array($sku_arr)){
    foreach($sku_arr as $key => $pid){
      $slug = strtolower($username).'-voucher-code-id-'.$ac_id.'-'.$pid;
    	$title = strtoupper($username)."-".$ac_id."-".$product_id."-".$product_sku."-for-".$pid;
      if( null == get_page_by_title( $title ) ) {
        $bk_wclogger->add('fablesounds','Debug: Creating Halion Voucher '.$title.' and assigning it to user '.$username.' : Product '.$product_sku);
        $voucher_id = wp_insert_post(
    			array(
    				'comment_status'	=>	'closed',
    				'ping_status'		=>	'closed',
    				'post_author'		=>	$author_id,
    				'post_name'		=>	$slug,
    				'post_title'		=>	$title,
    				'post_status'		=>	'publish',
    				'post_type'		=>	'fs_vouchers'
    			)
    		);

        if($voucher_id){
          update_post_meta($voucher_id,'bk_voucher_product_sku', $sku_arr[$key] );
          update_post_meta($voucher_id,'bk_voucher_product_bought', $product_id );
          update_post_meta($voucher_id,'bk_voucher_status','nused');
          update_post_meta($voucher_id,'bk_voucher_user_login', $username);
          update_post_meta($voucher_id,'bk_voucher_date', current_time('mysql'));
        }
      } else {
        $bk_wclogger->add('fablesounds','Error: Cannot create Halion vocuher '.$title.' and user '.$username.' : Product '.$product_sku);
        $voucher_id = -2;
      }
    }
  }

  return $voucher_id;
}
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
          $username = $bk_current_user->user_login;
          update_post_meta(intval($brass_serial[0]),'bk_halion_status','reg');
          update_post_meta(intval($reeds_serial[0]),'bk_halion_status','reg');
          update_post_meta(intval($rythm_serial[0]),'bk_halion_status','reg');
          update_post_meta(intval($brass_serial[0]),'bk_halion_user_login',$username);
          update_post_meta(intval($reeds_serial[0]),'bk_halion_user_login',$username);
          update_post_meta(intval($rythm_serial[0]),'bk_halion_user_login',$username);
          update_post_meta(intval($brass_serial[0]),'bk_halion_date',current_time('mysql'));
          update_post_meta(intval($reeds_serial[0]),'bk_halion_date',current_time('mysql'));
          update_post_meta(intval($rythm_serial[0]),'bk_halion_date',current_time('mysql'));

          $product_sku = 'BHFB';
          $ac_id = 'HAL-000';
          $product_id = wc_get_product_id_by_sku($product_sku);
          bk_halion_voucher_to_user($username,$ac_id,$product_id,$product_sku);
          wc_add_notice( __( 'Halion product serial number successfully registered.', 'fablesounds' ) );
          wp_safe_redirect( wc_get_endpoint_url( 'registered-keycodes' ) );
    			exit;
        }
      }
    }
  }
}
