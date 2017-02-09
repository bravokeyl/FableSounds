<?php
add_action('woocommerce_edit_account_form','bk_extra_account_fields');
add_action('woocommerce_save_account_details','bk_extra_save_account_details');

function bk_extra_account_fields(){
  $user_id = get_current_user_id();
  $user = get_userdata( $user_id );

  if ( !$user )
    return;

  //$bk_serial_key = get_user_meta( $user_id, 'ram', true );
  ?>
  <p>
    Register your keys that you got from third party stores
    to get activation codes by filling the following field.
    After you fill the field, you can see them in the <a href="<?php echo esc_url(home_url('/my-account/register-keys/'));?>">Registered keys section.</a>
  </p>
  <p class="woocommerce-FormRow woocommerce-FormRow--first form-row form-row-first">
		<label for="bk_serial_key"><?php _e( 'Register Serial Key', 'bk' ); ?> <span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="bk_serial_key" id="bk_serial_key"
    value="" />
	</p>
  <p class="woocommerce-FormRow woocommerce-FormRow--first form-row form-row-last">
		<label for="bk_product_sku"><?php _e( 'Product SKU', 'bk' ); ?> <span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="bk_product_sku" id="bk_product_sku"
    value="" />
	</p>
	<div class="clear"></div>
  <?php
}
function bk_extra_save_account_details($user_id){
  $rkey = esc_html($_POST[ 'bk_serial_key' ]);
  $pid = esc_html($_POST[ 'bk_product_sku' ]);
  if(!empty($rkey)){
    $args = array(
      'post_type'      => 'fs_activation_codes',
      'post_status'    => 'publish',
      'posts_per_page' => '1',
      'name'           => $rkey,
      'meta_key'       => 'bk_ac_status',
      'meta_query'     => array(
        array(
          'key'     => 'bk_ac_status',
          'value'   => 1,
          'compare' => '='
        )
      ),
    );
    $q = new WP_Query($args);
    if($q->have_posts()){
      while ($q->have_posts()) {
        $q->the_post();
        $acid = get_the_ID();
        // wp_die(print_r($acid));
        $d = current_time('mysql');
        update_post_meta($acid,'bk_ac_status',0);
        update_post_meta($acid,'bk_ac_user_email',$user_id);
        update_post_meta($acid,'bk_ac_date',$d);
        update_post_meta($acid,'bk_ac_product_sku',$pid);
      }
      wp_reset_postdata();
    }
    // wp_die(print_r($q));
  }
}
