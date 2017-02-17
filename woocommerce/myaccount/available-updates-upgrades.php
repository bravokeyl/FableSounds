<h4><?php echo get_option('wc_settings_available_updates_upgrades_title','Available Updates/Upgrades');?></h4>
<p>
  <?php echo get_option('wc_settings_available_updates_upgrades_description','Enter your serial number below to register your product.');?>
</p>
<form action="" method="post">
  <p class="woocommerce-FormRow woocommerce-FormRow--first form-row">
    <label for="bk_serial_key"><?php _e( 'Register serial number', 'bk' ); ?> <span class="required">*</span></label>
    <input type="text" required class="woocommerce-Input woocommerce-Input--text input-text bk-small" name="bk_serial_key1" id="bk_serial_key1"
    value="" />  &nbsp;&ndash;&nbsp;
    <input type="text" required class="woocommerce-Input woocommerce-Input--text input-text bk-small"
    pattern="[A-Za-z0-9]{4}"title="Four digit alphanumeric" name="bk_serial_key2" id="bk_serial_key2" value="" />  &nbsp;&ndash;&nbsp;
    <input type="text" required class="woocommerce-Input woocommerce-Input--text input-text bk-small"
    pattern="[A-Za-z0-9]{4}"title="Four digit alphanumeric" name="bk_serial_key3" id="bk_serial_key3" value="" />  &nbsp;&ndash;&nbsp;
    <input type="text" required class="woocommerce-Input woocommerce-Input--text input-text bk-small"
    pattern="[A-Za-z0-9]{4}"title="Four digit alphanumeric" name="bk_serial_key4" id="bk_serial_key4" value="" />  &nbsp;&ndash;&nbsp;
    <input type="text" required class="woocommerce-Input woocommerce-Input--text input-text bk-small"
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
