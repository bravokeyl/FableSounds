<h4><?php echo get_option('wc_settings_registered_products_title');?></h4>
<div>
  <?php echo get_option('wc_settings_registered_products_description');?>
</div>
<?php
function bk_get_act_id($serial){
  global $wpdb;
  $id = $wpdb->get_var( $wpdb->prepare("
    SELECT post_id
    FROM $wpdb->postmeta
    WHERE meta_value = '%s'
    LIMIT 1
    ", $serial));
  if(!empty($results)){
    return $results;
  }
  return ( $id ) ? intval( $id ) : 0;
}
$current_user = wp_get_current_user();
$activation_args = array(
  'post_type'      => 'fs_serial_numbers',
  'post_status'    => 'publish',
  'posts_per_page' => '-1',
  'meta_key'       => 'bk_sn_user_login',
  'meta_query'     => array(
    array(
      'key'     => 'bk_sn_user_login',
      'value'   => $current_user->user_login,
      'compare' => '='
    )
  ),
);
$activation_qe = new WP_Query($activation_args);

if($activation_qe->have_posts()){?>
  <table class="row">
    <thead>
      <tr>
        <th>
          <?php _e("Product Name","bk");?>
        </th>
        <th>
          <?php _e("Serial Number","bk");?>
        </th>
        <th>
          <?php _e("Activation Code","bk");?>
        </th>
        <th>
          <?php _e("Download Code","bk");?>
        </th>
        <th>
          <?php _e("Registration Date","bk");?>
        </th>
      </tr>
    </thead>
    <tbody>
  <?php while ($activation_qe->have_posts()) {
    $activation_qe->the_post();
    $activation_acid = get_the_ID();
    $acproductsku = get_post_meta( $activation_acid, 'bk_sn_product_sku', true );
    $downloadcode = get_post_meta( $activation_acid, 'bk_sn_download_code', true );
    ?>
        <tr>
          <td>
            <?php
            $acpid = wc_get_product_id_by_sku( $acproductsku );
            if($acpid){
              echo get_the_title($acpid);
            }
            $serial = $activation_qe->post->post_title;
            $acti = bk_get_act_id($serial);
            ?>
          </td>
          <td>
            <?php echo $activation_qe->post->post_title;?>
          </td>
          <td>
            <?php if($acti) echo get_the_title($acti);?>
          </td>
          <td>
            <?php echo $downloadcode;?>
          </td>
          <td>
            <?php
            $sn_date = get_post_meta($activation_acid,'bk_sn_date',true);
            try {
              $sn_date_obj = new DateTime($sn_date);
              $sn_date_formatted = $sn_date_obj->format('Y-m-d H:i:sP');
            } catch (Exception $e) {
              $sn_date_formatted = '';
            }

            echo $sn_date_formatted;?>
          </td>
        </tr>
    <?php
  }?>
  </tbody>
</table><?php
  wp_reset_postdata();
} else {
  echo "<p>There are no products registered on this account.</p>";
}

$current_user = wp_get_current_user();
$halion_args = array(
  'post_type'      => 'fs_halion_codes',
  'post_status'    => 'publish',
  'posts_per_page' => '-1',
  'meta_key'       => 'bk_halion_user_login',
  'meta_query'     => array(
    array(
      'key'     => 'bk_halion_user_login',
      'value'   => $current_user->user_email,
      'compare' => '='
    )
  ),
);
$halion_qe = new WP_Query($halion_args);
// print_r($q);
if($halion_qe->have_posts()){?>
  <h4>Your HALION products</h4>
  <p>Here you can see all the activation codes of products that you bought.</p>
  <table class="row halion-table">
    <thead>
      <tr>
        <th>
          <?php _e("Brass","bk");?>
        </th>
        <th>
          <?php _e("Reeds","bk");?>
        </th>
        <th>
          <?php _e("Rythm","bk");?>
        </th>
        <th>
          <?php _e("Assigned Date","bk");?>
        </th>
      </tr>
    </thead>
    <tbody>
  <?php while ($halion_qe->have_posts()) {
    $halion_qe->the_post();
    $hal_acid = get_the_ID();
    ?>
        <tr>
          <td>
            <?php echo get_post_meta($hal_acid,'bk_halion_brass_code',true);?>
          </td>
          <td>
            <?php echo get_post_meta($hal_acid,'bk_halion_reeds_code',true);?>
          </td>
          <td>
            <?php echo get_post_meta($hal_acid,'bk_halion_rythm_code',true);?>
          </td>
          <td>
            <?php echo get_post_meta($hal_acid,'bk_halion_date',true);?>
          </td>
        </tr>
    <?php
  }?>
  </tbody>
</table><?php
  wp_reset_postdata();
}
