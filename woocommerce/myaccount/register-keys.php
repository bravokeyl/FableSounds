<h4><?php echo get_option('wc_settings_register_new_product_title');?></h4>
<div>
  <?php echo get_option('wc_settings_register_new_product_description');?>
</div>
<form action="" method="post" class="bk-form">
  <p class="woocommerce-FormRow woocommerce-FormRow--first form-row">
    <label for="bk_serial_key"><?php _e( 'Register serial number', 'bk' ); ?> <span class="required">*</span></label>
    <input type="text" required maxLength="5" class="serial-input woocommerce-Input woocommerce-Input--text input-text bk-small" name="bk_serial_key1" id="bk_serial_key1"
    value="" />  &nbsp;&ndash;&nbsp;
    <input type="text" maxLength="4" required class="serial-input woocommerce-Input woocommerce-Input--text input-text bk-small"
    pattern="[A-Za-z0-9]{4}"title="Four digit alphanumeric" name="bk_serial_key2" id="bk_serial_key2" value="" />  &nbsp;&ndash;&nbsp;
    <input type="text" maxLength="4" required class="serial-input woocommerce-Input woocommerce-Input--text input-text bk-small"
    pattern="[A-Za-z0-9]{4}"title="Four digit alphanumeric" name="bk_serial_key3" id="bk_serial_key3" value="" />  &nbsp;&ndash;&nbsp;
    <input type="text" maxLength="4" required class="serial-input woocommerce-Input woocommerce-Input--text input-text bk-small"
    pattern="[A-Za-z0-9]{4}"title="Four digit alphanumeric" name="bk_serial_key4" id="bk_serial_key4" value="" />  &nbsp;&ndash;&nbsp;
    <input type="text" maxLength="4" required class="serial-input woocommerce-Input woocommerce-Input--text input-text bk-small"
    pattern="[A-Za-z0-9]{4}"title="Four digit alphanumeric" name="bk_serial_key5" id="bk_serial_key5" value="" />
  </p>
  <div class="clear"></div>
  <p>
    <?php wp_nonce_field( 'save_register_keys_details' ); ?>
    <input type="submit" class="woocommerce-Button button" value="<?php esc_attr_e( 'Register Product', 'fablesounds' ); ?>" />
    <input type="hidden" name="action" value="save_register_keys_details" />
  </p>
</form>
<div class="clear"></div>
