<h4>Register old HALION Product Serial</h4>
<p>
  Register your old Halion powered version Broadway Big Band serials that you got so as
  to be able to be cross-grade to Broadway Big Band – Kontakt Edition by filling the following field.
</p>
<form action="" method="post">
  <p class="woocommerce-FormRow woocommerce-FormRow--first form-row">
    <label for="bk_old_halion_brass_key"><?php _e( 'Enter Activation code 1 : Brass', 'bk' ); ?> <span class="required">*</span></label>
  <?php
    for($i=1;$i<9;$i++){ ?>
      <input type="text" required class="woocommerce-Input woocommerce-Input--text input-text bk-xsmall"
      pattern="[A-Za-z0-9]{4}" title="Four digit alphanumeric" name="bk_old_halion_key1<?php echo $i;?>"
      id="bk_old_halion_key1<?php echo $i;?>" value="" />  &nbsp;&ndash;&nbsp;
    <?php
    }
  ?>
  </p>
  <p class="woocommerce-FormRow woocommerce-FormRow--first form-row">
    <label for="bk_old_halion_reeds_key"><?php _e( 'Enter Activation code 2 : Reeds', 'bk' ); ?> <span class="required">*</span></label>
  <?php
    for($i=1;$i<9;$i++){ ?>
      <input type="text" required class="woocommerce-Input woocommerce-Input--text input-text bk-xsmall"
      pattern="[A-Za-z0-9]{4}" title="Four digit alphanumeric" name="bk_old_halion_key2<?php echo $i;?>"
      id="bk_old_halion_key2<?php echo $i;?>" value="" />  &nbsp;&ndash;&nbsp;
    <?php
    }
  ?>
  </p>
  <p class="woocommerce-FormRow woocommerce-FormRow--first form-row">
    <label for="bk_old_halion_rythm_key"><?php _e( 'Enter Activation code 3 : Rythm', 'bk' ); ?> <span class="required">*</span></label>
  <?php
    for($i=1;$i<9;$i++){ ?>
      <input type="text" required class="woocommerce-Input woocommerce-Input--text input-text bk-xsmall"
      pattern="[A-Za-z0-9]{4}" title="Four digit alphanumeric" name="bk_old_halion_key3<?php echo $i;?>"
      id="bk_old_halion_key3<?php echo $i;?>" value="" />  &nbsp;&ndash;&nbsp;
    <?php
    }
  ?>
  </p>
  <div class="clear"></div>
  <p>
    <?php wp_nonce_field( 'bk_register_halion_keys' ); ?>
    <input type="submit" class="woocommerce-Button button" value="<?php esc_attr_e( 'Register HALion Product', 'fablesounds' ); ?>" />
    <input type="hidden" name="action" value="bk_register_halion_keys" />
  </p>
</form>
