<h4>Registered Serials</h4>
<p>Here you can see all the registered serials of products that you bought from third party!</p>
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
            <?php
            $productsku = get_post_meta( $acid, 'bk_sn_product_sku', true );
            echo bk_get_product_name_by_sku($productsku);
            ?>
          </td>
          <td>
            <?php echo esc_html(get_post_meta( $acid, 'bk_sn_seller_name', true ))?>
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
  'meta_key'       => 'bk_ac_user_email',
  'meta_query'     => array(
    array(
      'key'     => 'bk_ac_user_email',
      'value'   => $current_user->user_email,
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
          <?php _e("Serial Number","bk");?>
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
    $acproductsku = get_post_meta( $activation_acid, 'bk_ac_product_sku', true );
    ?>
        <tr>
          <td>
            <?php echo bk_get_product_name_by_sku($acproductsku);?>
          </td>
          <td>
            <?php echo $activation_qe->post->post_title;?>
          </td>
          <td>
            <?php echo get_post_meta($activation_acid,'bk_ac_serial_activation',true);?>
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
