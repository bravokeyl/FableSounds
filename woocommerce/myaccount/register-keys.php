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
      'BLDR'  =>  'Broadway Lites',
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
