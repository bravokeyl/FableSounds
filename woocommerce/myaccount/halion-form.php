<h4><?php echo get_option('wc_settings_register_halion_title');?></h4>
<div>
  <?php echo get_option('wc_settings_register_halion_description');?>
</div>
<form action="" method="post">
  <p class="woocommerce-FormRow woocommerce-FormRow--first form-row">
    <label for="bk_old_halion_brass_key"><?php _e( 'Enter Activation code 1 : Brass', 'bk' ); ?> <span class="required">*</span></label>
  <?php
    for($i=1;$i<9;$i++){ ?>
      <input type="text" maxlength="4" required class="serial-input woocommerce-Input woocommerce-Input--text input-text bk-xsmall"
      pattern="[A-Za-z0-9]{4}" title="Four digit alphanumeric" name="bk_old_halion_key1<?php echo $i;?>"
      id="bk_old_halion_key1<?php echo $i;?>" value="" />
      <?php if($i != 8){?>
      <span class="bk-dash">&nbsp;&ndash;&nbsp;</span>
      <?php }?>
    <?php
    }
  ?>
  </p>
  <p class="woocommerce-FormRow woocommerce-FormRow--first form-row">
    <label for="bk_old_halion_reeds_key"><?php _e( 'Enter Activation code 2 : Reeds', 'bk' ); ?> <span class="required">*</span></label>
  <?php
    for($i=1;$i<9;$i++){ ?>
      <input type="text" maxlength="4" required class="serial-input woocommerce-Input woocommerce-Input--text input-text bk-xsmall"
      pattern="[A-Za-z0-9]{4}" title="Four digit alphanumeric" name="bk_old_halion_key2<?php echo $i;?>"
      id="bk_old_halion_key2<?php echo $i;?>" value="" />
      <?php if($i != 8){?>
      <span class="bk-dash">&nbsp;&ndash;&nbsp;</span>
      <?php }?>
    <?php
    }
  ?>
  </p>
  <p class="woocommerce-FormRow woocommerce-FormRow--first form-row">
    <label for="bk_old_halion_rythm_key"><?php _e( 'Enter Activation code 3 : Rythm', 'bk' ); ?> <span class="required">*</span></label>
  <?php
    for($i=1;$i<9;$i++){ ?>
      <input type="text" maxlength="4" required class="serial-input woocommerce-Input woocommerce-Input--text input-text bk-xsmall"
      pattern="[A-Za-z0-9]{4}" title="Four digit alphanumeric" name="bk_old_halion_key3<?php echo $i;?>"
      id="bk_old_halion_key3<?php echo $i;?>" value="" />
      <?php if($i != 8){?>
      <span class="bk-dash">&nbsp;&ndash;&nbsp;</span>
      <?php }?>
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
