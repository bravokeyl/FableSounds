<h4><?php echo get_option('wc_settings_available_updates_upgrades_title','Available Updates/Upgrades');?></h4>
<div>
  <?php echo get_option('wc_settings_available_updates_upgrades_description','Enter your serial number below to register your product.');?>
</div>
<div class="clear"></div>
<?php
$current_user = wp_get_current_user();
$activation_args = array(
  'post_type'      => 'fs_vouchers',
  'post_status'    => 'publish',
  'posts_per_page' => '-1',
  'meta_query'     => array(
    'relation' => 'AND',
    array(
      'key'     => 'bk_voucher_user_login',
      'value'   => $current_user->user_login,
      'compare' => '='
    ),
    array(
      'key'     => 'bk_voucher_status',
      'value'   => 'nused',
      'compare' => '='
    ),
  ),
);
$activation_qe = new WP_Query($activation_args);

if($activation_qe->have_posts()){?>
  <br/>
  <h4 class="upgrades-table-title">Available Upgrades / Updates</h4>
  <table class="row">
    <thead>
      <tr>
        <th>
          <?php _e("Thumbnail","bk");?>
        </th>
        <th>
          <?php _e("Product Name","bk");?>
        </th>
        <th>
          <?php _e("Price","bk");?>
        </th>
        <th>

        </th>
      </tr>
    </thead>
    <tbody>
  <?php
  $i = 1;
  while ($activation_qe->have_posts()) {
    $activation_qe->the_post();
    $activation_acid = get_the_ID();
    $abcsku = get_post_meta( $activation_acid, 'bk_voucher_product_sku', true );
    $acproduct_id = wc_get_product_id_by_sku($abcsku);
    global $woocommerce;
    $cart_url = $woocommerce->cart->get_cart_url();
    $product = wc_get_product( $acproduct_id );
    $order_thumb = $product->get_image();
    $price = $product->get_price();
    ?>
        <tr>
          <td class="product-thumb">
            <?php echo $order_thumb;?>
          </td>
          <td>
            <?php echo get_the_title($acproduct_id);?>
          </td>
          <td>
            <?php echo get_woocommerce_currency_symbol().$price; ?>
          </td>
          <td>
            <?php
            $c_url = add_query_arg( array(
                'add-to-cart' => $acproduct_id,
            ), $cart_url );
             ?>
            <a href="<?php echo esc_url($c_url);?>">Add to cart</a>
          </td>
        </tr>
    <?php
    $i++;
  }?>
  </tbody>
</table><?php
  wp_reset_postdata();
} else {
  echo "<p>No product upgrades/updates available.</p>";
}?>
