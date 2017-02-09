<?php
// add_action( 'template_redirect', 'bk_save_register_keys_details'  );
// function bk_save_register_keys_details(){
  if ( 'POST' == strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
    if ( empty( $_POST[ 'action' ] ) || 'save_register_keys_details' !== $_POST[ 'action' ] ||
    empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'save_register_keys_details' ) ) {
      return;
    }else {
      $bk_serial_key_val     = ! empty( $_POST['bk_serial_key'] )? esc_attr($_POST['bk_serial_key']) : '';
      $products_dropdown_val = ! empty( $_POST['products_dropdown'] )? esc_attr($_POST['products_dropdown']): '';

      if ( !empty( $bk_serial_key_val ) ) {
        $serial_found = bk_check_serial_number($bk_serial_key_val);
        if(empty($serial_found)){
          echo '<div class="grve-woo-message grve-bg-red">Invalid Serial Number, please check it.</div>';
        } else {
          $bk_current_user = wp_get_current_user();
          update_post_meta(intval($serial_found[0]),'bk_sn_status','reg');
          update_post_meta(intval($serial_found[0]),'bk_sn_product_sku',$products_dropdown_val);
          update_post_meta(intval($serial_found[0]),'bk_sn_user_email',$bk_current_user->user_email);
          update_post_meta(intval($serial_found[0]),'bk_sn_date',current_time('mysql'));
          echo '<div class="grve-woo-message grve-bg-green">Serial Number successfully registered.</div>';
        }
      }
    }
  }
// }
?>
<h4>Registered Keys</h4>
<p>Here you can see all the registered keys of products that you bought from third party!</p>
<?php
$current_user = wp_get_current_user();
$args = array(
  'post_type'      => 'fs_serial_numbers',
  'post_status'    => 'publish',
  'posts_per_page' => '-1',
  'meta_key'       => 'bk_sn_user_email',
  'meta_query'     => array(
    array(
      'key'     => 'bk_sn_user_email',
      'value'   => $current_user->user_email,
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
    <label for="bk_product_sku"><?php _e( 'Product', 'bk' ); ?> <span class="required">*</span></label>
    <?php
    //Save Database query by hardcoding
    $products_dropdown = array(
      'BGDR'  =>  'Broadway Gig',
      'BlDR'  =>  'Broadway Lites',
      'BKFDR' =>  'Broadway Big Band – Kontakt Edition'
    );

    echo "<select name='products_dropdown'>";
    foreach( $products_dropdown as $key => $product ){
        echo "<option value = '" . esc_attr( $key ) . "'>" . esc_html( $product ) . "</option>";
    }
    echo "</select>";?>
  </p>
  <p class="woocommerce-FormRow woocommerce-FormRow--first form-row form-row-last">
    <label for="bk_serial_key"><?php _e( 'Register Serial Key', 'bk' ); ?> <span class="required">*</span></label>
    <input type="text" required class="woocommerce-Input woocommerce-Input--text input-text" name="bk_serial_key" id="bk_serial_key"
    value="" />
  </p>
  <div class="clear"></div>
  <p>
    <?php wp_nonce_field( 'save_register_keys_details' ); ?>
    <input type="submit" class="woocommerce-Button button" value="<?php esc_attr_e( 'Save changes', 'fablesounds' ); ?>" />
    <input type="hidden" name="action" value="save_register_keys_details" />
  </p>
</form>

<?php
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
