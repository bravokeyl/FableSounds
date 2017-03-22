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
    AND meta_key = 'bk_ac_serial_activation'
    LIMIT 1
    ", $serial));
  return ( $id ) ? intval( $id ) : 0;
}
$current_user = wp_get_current_user();
$activation_args = array(
  'post_type'      => 'fs_serial_numbers',
  'post_status'    => 'publish',
  'posts_per_page' => '-1',
  'meta_key'       => 'bk_sn_date',
  'meta_query'     => array(
    array(
      'key'     => 'bk_sn_user_login',
      'value'   => $current_user->user_login,
      'compare' => '='
    )
  ),
  'orderby' => 'meta_value',
  'order' => 'DESC'
);
$activation_qe = new WP_Query($activation_args);

if($activation_qe->have_posts()){?>

  <?php
   $accordion = 0;
   while ($activation_qe->have_posts()) {
    $activation_qe->the_post();
    $activation_acid = get_the_ID();
    $acproductsku = get_post_meta( $activation_acid, 'bk_sn_product_sku', true );
    $downloadcode = get_post_meta( $activation_acid, 'bk_sn_download_code', true );
    $acpid = wc_get_product_id_by_sku( $acproductsku );
    $product = wc_get_product($acpid);
    $downloadable_files = $product->get_files();
    $serial = $activation_qe->post->post_title;
    $acti = bk_get_act_id($serial);
    // $item_downloads = "s";
    ?>
    <div class="bk-accordion-panel" class="row">
      <div class="bk-accordion-product-title">
        <a href="#bk-accordion-<?php echo $accordion;?>" class="<?php if(0 == $accordion) echo "active";?> "><i class="fa fa-chevron-down"></i><?php if($acpid){ echo get_the_title($acpid); } ?></a>
      </div>
      <div id="bk-accordion-<?php echo $accordion;?>" class="bk-accordion-product-content">
        <div class="bk-accordion-item">
          <div class="first">Registration Date</div>
          <div>
            <?php
              $sn_date = get_post_meta($activation_acid,'bk_sn_date',true);
              try {
                $sn_date_obj = new DateTime($sn_date);
                $sn_date_formatted = $sn_date_obj->format('Y-m-d H:i:sP');
              } catch (Exception $e) {
                $sn_date_formatted = '';
              }
              echo $sn_date_formatted;
            ?>
          </div>
        </div>
        <div class="bk-accordion-item">
          <div class="first">Serial Number</div>
          <div><?php echo $activation_qe->post->post_title;?></div>
        </div>
        <div class="bk-accordion-item">
          <div class="first">Activation Code</div>
          <div><?php if($acti) echo get_the_title($acti);?></div>
        </div>
        <div class="bk-accordion-item">
          <div class="first pull-left">Download Code</div>
          <div><?php  echo $downloadcode; ?></div>
        </div>
        <div class="bk-accordion-item">
          <div class="first">Available Downloads</div>
          <div class="bk-download-files">
            <?php
            if ( ! empty( $downloadable_files ) ) {
                $fcount = count($downloadable_files);
                $fc = 1;
                foreach ( $downloadable_files as $key => $file ) {
                  $link = $file['file'];
                  $name = $file['name'];
                  // $download_link = add_query_arg( array(
      						// 	'download_file' => $file->get_product_id(),
      						// 	'order'         => $file->get_order_key(),
      						// 	'email'         => urlencode( $file->get_user_email() ),
      						// 	'key'           => $file->get_download_id(),
      						// ), trailingslashit( home_url() ) );
                  if(empty($name)){
                    $name = "Default File name";
                  }
                  // print_r($file);
                  echo "<a href='".esc_url($link)."' target='_blank' >".$name."</a>";
                  if($fc < $fcount){
                    echo "<span>&nbsp;|&nbsp;</span>";
                  }
                  $fc++;
                }
              }
           ?>
         </div>
       </div>
      </div>
    </div>
    <?php
    $accordion++;
  }?><?php
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
      'value'   => $current_user->user_login,
      'compare' => '='
    )
  ),
);
$hal_codes = array();
$halion_qe = new WP_Query($halion_args);
if($halion_qe->have_posts()){

  while ($halion_qe->have_posts()) {
    $halion_qe->the_post();
    $hal_acid = get_the_ID();
    $hal_code_type = get_post_meta($hal_acid,'bk_halion_code_type',true);
    $hal_code_type_s = strtolower($hal_code_type);
    $hal_code = get_the_title();
    $hal_date = get_post_meta($hal_acid,'bk_halion_date',true);
    $hal_codes[$hal_date][$hal_code_type_s] = $hal_code;
    // $hal_codes[$hal_date][$hal_code_type] = array(
    //   'type' => $hal_code_type,
    //   'id'   => $hal_acid,
    //   'code' => $hal_code,
    //   'date' => $hal_date,
    // );
  }
  wp_reset_postdata();
}?>
<?php if( 0 < count($hal_codes)){?>
<h5 style="margin-top:20px;">Your Broadway Big Band (HAlion) codes</h5>
<table class="row halion-table">
  <thead>
    <tr>
      <th>
        <?php _e("Brass Activation Code","bk");?>
      </th>
      <th>
        <?php _e("Reeds Activation Code","bk");?>
      </th>
      <th>
        <?php _e("Rhythm Activation Code","bk");?>
      </th>
      <th>
        <?php _e("Assigned Date","bk");?>
      </th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($hal_codes as $k => $v){ ?>
    <tr>
      <td style="text-transform: uppercase;">
        <?php echo $hal_codes[$k]['brass'];?>
      </td>
      <td style="text-transform: uppercase;">
        <?php echo $hal_codes[$k]['reeds'];?>
      </td>
      <td style="text-transform: uppercase;">
        <?php echo $hal_codes[$k]['rhythm'];?>
      </td>
      <td style="text-transform: uppercase;">
        <?php
        try {
          if(is_numeric($k)){
            $hc_date_obj = new DateTime();
            $hc_date = $hc_date_obj->setTimestamp($k);
          } else {
            $hc_date = new DateTime($k);
          }
          $hc_date_formatted = $hc_date->format('Y-m-d H:i:sP');
        } catch (Exception $e) {
          $hc_date_formatted = '';
        }
        echo $hc_date_formatted;?>
      </td>
    </tr>
    <?php } ?>
  </tbody>
</table>
<?php } ?>
