<?php /*Registered Keys*/?>
<h4>Registered Keys</h4>
<p>Here you can see all the registered keys of products that you bought from third party!</p>
<?php
$args = array(
  'post_type'      => 'fs_serial_numbers',
  'post_status'    => 'publish',
  'posts_per_page' => '-1',
  'meta_key'       => 'bk_sn_user_id',
  'meta_query'     => array(
    array(
      'key'     => 'bk_sn_user_id',
      'value'   => get_current_user_id(),
      'compare' => '='
    )
  ),
);
$qe = new WP_Query($args);
// print_r($q);
if($qe->have_posts()){?>
  <table class="row">
    <thead>
      <tr>
        <th>
          <?php _e("Product ID","bk");?>
        </th>
        <th>
          <?php _e("Seller","bk");?>
        </th>
        <th>
          <?php _e("Serial Number ","bk");?>
        </th>
      </tr>
    </thead>
    <tbody>
  <?php while ($qe->have_posts()) {
    $qe->the_post();
    $acid = get_the_ID();
    ?>
        <tr>
          <td>
            <?php echo $acid;?>
          </td>
          <td>
            <?php esc_html(get_post_meta( $acid, 'bk_sn_seller_name', true ))?>
          </td>
          <td>
            <?php echo $qe->post->post_title;?>
          </td>
        </tr>

    <?php
  }?>
  </tbody>
</table><?php
  wp_reset_postdata();
}
?>
<h4>Activation Codes</h4>
<p>Here you can see all the activation codes that you have</p>
<?php
$activation_args = array(
  'post_type'      => 'fs_activation_codes',
  'post_status'    => 'publish',
  'posts_per_page' => '-1',
  'meta_key'       => 'bk_ac_user_id',
  'meta_query'     => array(
    array(
      'key'     => 'bk_ac_user_id',
      'value'   => get_current_user_id(),
      'compare' => '='
    )
  ),
);
$activation_qe = new WP_Query($activation_args);
// print_r($q);
if($activation_qe->have_posts()){?>
  <table class="row">
    <thead>
      <tr>
        <th>
          <?php _e("Product ID","bk");?>
        </th>
        <th>
          <?php _e("Activation Code","bk");?>
        </th>
        <th>
          <?php _e("Assigned Date","bk");?>
        </th>
      </tr>
    </thead>
    <tbody>
  <?php while ($activation_qe->have_posts()) {
    $activation_qe->the_post();
    $activation_acid = get_the_ID();
    ?>
        <tr>
          <td>
            <?php echo $activation_acid;?>
          </td>
          <td>
            <?php echo $activation_qe->post->post_title;?>
          </td>
          <td>
            <?php echo get_post_meta($activation_acid,'bk_ac_date',true);?>
          </td>
        </tr>
    <?php
  }?>
  </tbody>
</table><?php
  wp_reset_postdata();
}
?>
<h4>Register New Keys</h4>
<p>
  Register your keys that you got from third party stores
  to get activation codes by filling the following field.
</p>
<form action="" method="post">
  <p class="woocommerce-FormRow woocommerce-FormRow--first form-row form-row-first">
    <label for="bk_serial_key"><?php _e( 'Register Serial Key', 'bk' ); ?> <span class="required">*</span></label>
    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="bk_serial_key" id="bk_serial_key"
    value="" />
  </p>
  <p class="woocommerce-FormRow woocommerce-FormRow--first form-row form-row-last">
    <label for="bk_product_sku"><?php _e( 'Product', 'bk' ); ?> <span class="required">*</span></label>
    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="bk_product_sku" id="bk_product_sku"
    value="" />
  </p>
  <div class="clear"></div>
  <p>
    <?php wp_nonce_field( 'save_register_keys_details' ); ?>
    <input type="submit" class="woocommerce-Button button" value="<?php esc_attr_e( 'Save changes', 'fablesounds' ); ?>" />
    <input type="hidden" name="action" value="save_register_keys_details" />
  </p>
</form>
