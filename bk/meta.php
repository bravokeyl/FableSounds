<?php
add_action( 'load-post.php', 'bk_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'bk_post_meta_boxes_setup' );

function bk_post_meta_boxes_setup() {
	add_action( 'add_meta_boxes', 'bk_add_post_meta_boxes' );
  add_action( 'save_post', 'bk_save_post_meta', 10, 2 );
}

function bk_add_post_meta_boxes() {
	add_meta_box(
    'bk-ac-meta',
    esc_html__( 'Custom Fields', 'bk' ),
    'bk_ac_meta_box',
    array('fs_activation_codes'),
    'normal',
    'default'
  );
	add_meta_box(
    'bk-sn-meta',
    esc_html__( 'Extra Fields', 'bk' ),
    'bk_sn_meta_box',
    array('fs_serial_numbers'),
    'normal',
    'default'
  );
	add_meta_box(
    'bk-voucher-meta',
    esc_html__( 'Extra Fields', 'bk' ),
    'bk_voucher_meta_box',
    array('fs_vouchers'),
    'normal',
    'default'
  );
}

function bk_ac_meta_box($object, $box) {
	?>

  <?php wp_nonce_field( basename( __FILE__ ), 'bk_ac_meta_nonce' ); ?>
   <p>
  	<label for="bk-ac-user-login"><?php _e( "User Name:", 'bk' ); ?>
    <?php $user_id = get_post_meta( $object->ID, 'bk_ac_user_login', true ); ?>
      <input type="text" name="bk-ac-user-login" class="" id="bk-ac-user-login" value="<?php echo esc_html($user_id);?>" />
    </label>
   </p>
   <p>
  	<label for="bk-ac-product-sku"><?php _e( "Product SKU:", 'bk' ); ?>
    <?php $product_id = get_post_meta( $object->ID, 'bk_ac_product_sku', true ); ?>
      <input type="text" name="bk-ac-product-sku" class="" id="bk-ac-product-sku" value="<?php echo esc_html($product_id);?>" />
    </label>
   </p>
   <p>
  	<label for="bk-ac-status"><?php _e( "Status:", 'bk' ); ?>
    <?php $status = get_post_meta( $object->ID, 'bk_ac_status', true );?>
			<select name="bk-ac-status" id="bk-ac-status">
					<option value="nused" <?php selected( $status, 'nused' ); ?>>Unused</option>
					<option value="used" <?php selected( $status, 'used' ); ?>>Used</option>
			</select>
    </label>
   </p>
   <p>
  	<label for="bk-ac-date"><?php _e( "Date:", 'bk' ); ?>
    <?php $date = get_post_meta( $object->ID, 'bk_ac_date', true ); ?>
      <input type="text" name="bk-ac-date" class="" id="bk-ac-date" value="<?php echo esc_html($date);?>" />
    </label>
   </p>
<?php
}

function bk_voucher_meta_box($object, $box) {
	?>

  <?php wp_nonce_field( basename( __FILE__ ), 'bk_voucher_meta_nonce' ); ?>
   <p>
  	<label for="bk-voucher-user-login"><?php _e( "User Name:", 'bk' ); ?>
    <?php $user_id = get_post_meta( $object->ID, 'bk_voucher_user_login', true ); ?>
      <input type="text" name="bk-voucher-user-login" class="" id="bk-voucher-user-login" value="<?php echo esc_html($user_id);?>" />
    </label>
   </p>
   <p>
  	<label for="bk-voucher-product-sku"><?php _e( "Product SKU:", 'bk' ); ?>
    <?php $product_id = get_post_meta( $object->ID, 'bk_voucher_product_sku', true ); ?>
      <input type="text" name="bk-voucher-product-sku" class="" id="bk-voucher-product-sku" value="<?php echo esc_html($product_id);?>" />
    </label>
   </p>
   <p>
  	<label for="bk-voucher-status"><?php _e( "Status:", 'bk' ); ?>
    <?php $status = get_post_meta( $object->ID, 'bk_voucher_status', true );?>
			<select name="bk-voucher-status" id="bk-voucher-status">
					<option value="nused" <?php selected( $status, 'nused' ); ?>>Unused</option>
					<option value="used" <?php selected( $status, 'used' ); ?>>Used</option>
			</select>
    </label>
   </p>
   <p>
  	<label for="bk-voucher-date"><?php _e( "Date:", 'bk' ); ?>
    <?php $date = get_post_meta( $object->ID, 'bk_voucher_date', true ); ?>
      <input type="text" name="bk-voucher-date" class="" id="bk-voucher-date" value="<?php echo esc_html($date);?>" />
    </label>
   </p>
<?php
}

function bk_sn_meta_box($object, $box) {
	?>
  <?php wp_nonce_field( basename( __FILE__ ), 'bk_sn_meta_nonce' ); ?>
   <p>
  	<label for="bk-sn-user-login"><?php _e( "User Name:", 'bk' ); ?>
    <?php $user_id = get_post_meta( $object->ID, 'bk_sn_user_login', true ); ?>
      <input type="text" name="bk-sn-user-login" class="" id="bk-sn-user-login" value="<?php echo esc_attr($user_id);?>" />
    </label>
   </p>
	 <p>
  	<label for="bk-sn-seller-name"><?php _e( "Seller Name:", 'bk' ); ?>
    <?php $seller_name = get_post_meta( $object->ID, 'bk_sn_seller_name', true ); ?>
      <input type="text" name="bk-sn-seller-name" class="" id="bk-sn-seller-name" value="<?php echo esc_attr($seller_name);?>" />
    </label>
   </p>
   <p>
  	<label for="bk-sn-product-sku"><?php _e( "Product SKU:", 'bk' ); ?>
    <?php $product_id = get_post_meta( $object->ID, 'bk_sn_product_sku', true ); ?>
      <input type="text" name="bk-sn-product-sku" class="" id="bk-sn-product-sku" value="<?php echo esc_attr($product_id);?>" />
    </label>
   </p>
	 <p>
  	<label for="bk-download-code"><?php _e( "Download Code:", 'bk' ); ?>
    <?php $product_id = get_post_meta( $object->ID, 'bk_download_code', true ); ?>
      <input type="text" name="bk-download-code" class="" id="bk-download-code" value="<?php echo esc_attr($product_id);?>" />
    </label>
   </p>
	 <p>
  	<label for="bk-dealer-price"><?php _e( "Dealer Price:", 'bk' ); ?>
    <?php $product_id = get_post_meta( $object->ID, 'bk_dealer_price', true ); ?>
      <input type="text" name="bk-dealer-price" class="" id="bk-dealer-price" value="<?php echo esc_attr($product_id);?>" />
    </label>
   </p>
	 <p>
  	<label for="bk-serial-code-count"><?php _e( "Activation code count:", 'bk' ); ?>
    <?php $product_id = get_post_meta( $object->ID, 'bk_sn_activation_code_count', true ); ?>
      <input type="text" name="bk-serial-code-count" class="" id="bk-serial-code-count" value="<?php echo esc_attr($product_id);?>" />
    </label>
   </p>
   <p>
  	<label for="bk-sn-status"><?php _e( "Status:", 'bk' ); ?>
    	<?php $status = get_post_meta( $object->ID, 'bk_sn_status', true );?>
			<select name="bk-sn-status" id="bk-sn-status">
          <option value="nreg" <?php selected( $status, 'nreg' ); ?>>Not Registered</option>
          <option value="reg" <?php selected( $status, 'reg' ); ?>>Registered</option>
      </select>
    </label>
   </p>
	 <p>
  	<label for="bk_sn_distributed"><?php _e( "Distributed:", 'bk' ); ?>
    	<?php $dist = get_post_meta( $object->ID, 'bk_sn_distributed', true );?>
			<select name="bk-sn-distributed" id="bk-sn-distributed">
          <option value="0" <?php selected( $dist, '0' ); ?>>No</option>
          <option value="1" <?php selected( $dist, '1' ); ?>>Yes</option>
      </select>
    </label>
   </p>
   <p>
  	<label for="bk-sn-date"><?php _e( "Date:", 'bk' ); ?>
    <?php $date = get_post_meta( $object->ID, 'bk_sn_date', true ); ?>
      <input type="text" name="bk-sn-date" class="" id="bk-sn-date" value="<?php echo esc_html($date);?>" />
    </label>
   </p>
<?php
}

function bk_save_post_meta( $post_id, $post ) {

	$post_type = get_post_type_object( $post->post_type );

	if( 'fs_activation_codes' == $post->post_type) {
	  if ( !isset( $_POST['bk_ac_meta_nonce'] ) || !wp_verify_nonce( $_POST['bk_ac_meta_nonce'], basename( __FILE__ ) ) )
	    return $post_id;
	}

	if( 'fs_serial_numbers' == $post->post_type) {
	  if ( !isset( $_POST['bk_sn_meta_nonce'] ) || !wp_verify_nonce( $_POST['bk_sn_meta_nonce'], basename( __FILE__ ) ) )
	    return $post_id;
	}


  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return $post_id;
  }

  if ( wp_is_post_autosave( $post_id ) ) {
    return $post_id;
  }

  if ( wp_is_post_revision( $post_id ) ) {
    return $post_id;
  }

	$meta_keys = array();

	if( 'fs_activation_codes' == $post->post_type) {
		$buid = ( isset( $_POST['bk-ac-user-login'] ) ? $_POST['bk-ac-user-login'] : '' );
	  $bpid = ( isset( $_POST['bk-ac-product-sku'] ) ? esc_attr($_POST['bk-ac-product-sku']) : '' );
	  $bstatus= ( isset( $_POST['bk-ac-status'] ) ? esc_attr($_POST['bk-ac-status']) : '' );
	  $bdate = ( isset( $_POST['bk-ac-date'] ) ? $_POST['bk-ac-date'] : '' );

	  $meta_keys = array(
	    'bk_ac_user_login' => $buid,
	    'bk_ac_product_sku' => $bpid,
	    'bk_ac_status' => $bstatus,
	    'bk_ac_date' => $bdate
	  );

	}

	if( 'fs_vouchers' == $post->post_type) {
		$buid = ( isset( $_POST['bk-voucher-user-login'] ) ? $_POST['bk-voucher-user-login'] : '' );
	  $bpid = ( isset( $_POST['bk-voucher-product-sku'] ) ? esc_attr($_POST['bk-voucher-product-sku']) : '' );
	  $bstatus= ( isset( $_POST['bk-voucher-status'] ) ? esc_attr($_POST['bk-voucher-status']) : '' );
	  $bdate = ( isset( $_POST['bk-voucher-date'] ) ? $_POST['bk-voucher-date'] : '' );

	  $meta_keys = array(
	    'bk_voucher_user_login' => $buid,
	    'bk_voucher_product_sku' => $bpid,
	    'bk_voucher_status' => $bstatus,
	    'bk_voucher_date' => $bdate
	  );

	}

	if( 'fs_serial_numbers' == $post->post_type) {
		$buid = ( isset( $_POST['bk-sn-user-login'] ) ? $_POST['bk-sn-user-login'] : '' );
	  $bseller = ( isset( $_POST['bk-sn-seller-name'] ) ? esc_attr($_POST['bk-sn-seller-name']) : '' );
		$bpid = ( isset( $_POST['bk-sn-product-sku'] ) ? esc_attr($_POST['bk-sn-product-sku']) : '' );
		$bstatus = isset( $_POST['bk-sn-status'] ) ? esc_attr( $_POST['bk-sn-status']) : '';
	  $bdate = ( isset( $_POST['bk-sn-date'] ) ? $_POST['bk-sn-date'] : '' );
		$bdcode = isset( $_POST['bk-download-code'] ) ? esc_attr( $_POST['bk-download-code']) : '';
	  $bdealer_price = ( isset( $_POST['bk-dealer-price'] ) ? $_POST['bk-dealer-price'] : '' );
		$bdate = ( isset( $_POST['bk-sn-date'] ) ? $_POST['bk-sn-date'] : '' );
		$bdist = ( isset( $_POST['bk-sn-distributed'] ) ? $_POST['bk-sn-distributed'] : '' );
		$bserialcount = ( isset( $_POST['bk-serial-code-count'] ) ? $_POST['bk-serial-code-count'] : '' );

	  $meta_keys = array(
	    'bk_sn_user_login' => $buid,
			'bk_sn_seller_name' => $bseller,
	    'bk_sn_product_sku' => $bpid,
	    'bk_sn_status' => $bstatus,
	    'bk_download_code' => $bdcode,
			'bk_dealer_price' => $bdealer_price,
			'bk_sn_date' => $bdate,
			'bk_sn_distributed' => $bdist,
			'bk_sn_activation_code_count' => $bserialcount
	  );
	}

  foreach ($meta_keys as $meta_key => $new_meta_value) {
    $meta_value = get_post_meta( $post_id, $meta_key, true );
    update_post_meta( $post_id, $meta_key, $new_meta_value );
  }

}

add_action('woocommerce_product_options_sku','bk_product_meta');
function bk_product_meta() {
	woocommerce_wp_text_input( array( 'id' => '_continuata_sku', 'label' => '<abbr title="'. __( 'Continuata Stock Keeping Unit', 'fablesounds' ) .'">' . __( 'Continuata SKU', 'fablesounds' ) . '</abbr>', 'desc_tip' => 'true', 'description' => __( 'This is the SKU that we send to Continuata.', 'fablesounds' ) ) );
	woocommerce_wp_text_input( array( 'id' => '_activation_sku', 'label' => '<abbr title="'. __( 'Continuata Stock Keeping Unit', 'fablesounds' ) .'">' . __( 'Activation SKU', 'fablesounds' ) . '</abbr>', 'desc_tip' => 'true', 'description' => __( 'This is the SKU used for activation codes.', 'fablesounds' ) ) );
}

add_action('woocommerce_process_product_meta','bk_save_product_meta');
function bk_save_product_meta($post_id) {
	// Unique Continuata SKU
	$sku     = get_post_meta( $post_id, '_continuata_sku', true );
	$new_sku = (string) wc_clean( $_POST['_continuata_sku'] );
	$asku     = get_post_meta( $post_id, '_activation_sku', true );
	$new_asku = (string) wc_clean( $_POST['_activation_sku'] );
	$eligble_products = (string) wc_clean( $_POST['bk_eligible_products'] );
	$is_upgrade = $is_update = $is_download = 'no';

	if ( '' == $new_sku ) {
		update_post_meta( $post_id, '_continuata_sku', '' );
	} elseif ( $new_sku !== $sku ) {
		if ( ! empty( $new_sku ) ) {
				update_post_meta( $post_id, '_continuata_sku', $new_sku );
		} else {
			update_post_meta( $post_id, '_continuata_sku', '' );
		}
	}

	if ( '' == $new_asku ) {
		update_post_meta( $post_id, '_activation_sku', '' );
	} elseif ( $new_asku !== $asku ) {
		if ( ! empty( $new_asku ) ) {
				update_post_meta( $post_id, '_activation_sku', $new_asku );
		} else {
			update_post_meta( $post_id, '_activation_sku', '' );
		}
	}

	if ( ! empty( $_POST['bk_product_upgrade'] ) ) {
		$is_upgrade = 'yes';
	}
	if ( ! empty( $_POST['bk_product_update'] ) ) {
		$is_update = 'yes';
	}
	if ( ! empty( $_POST['bk_product_message'] ) ) {
		$product_message = $_POST['bk_product_message'];
	}
	if ( ! empty( $_POST['bk_product_url'] ) ) {
		$product_url = $_POST['bk_product_url'];
	}

	$eligble_products_arr = isset( $_POST['bk_eligible_products'] ) ? array_filter( array_map( 'intval', explode( ',', $_POST['bk_eligible_products'] ) ) ) : array();

	update_post_meta( $post_id, 'bk_eligible_products', $eligble_products_arr );
	update_post_meta( $post_id, 'bk_product_upgrade', $is_upgrade );
	update_post_meta( $post_id, 'bk_product_update', $is_update );
	update_post_meta( $post_id, 'bk_product_url', $product_url );
	update_post_meta( $post_id, 'bk_product_message', $product_message );
}

add_action('woocommerce_product_options_general_product_data','bk_product_is_new');
function bk_product_is_new() {
	global $post;
	woocommerce_wp_text_input( array( 'id' => 'bk_product_url', 'label' => __( 'Product URL', 'fablesounds' ), 'desc_tip' => 'true', 'description' => __( 'This is the URL used in the cart pages.', 'fablesounds' ) ) );
	woocommerce_wp_checkbox( array( 'id' => 'bk_product_upgrade', 'label' => __( 'Voucher Required?', 'fablesounds' ),'description' => __( 'Is this product a upgrade and needs other product to be bought to become eligible?', 'fablesounds' ) ) );
	woocommerce_wp_checkbox( array( 'id' => 'bk_product_update', 'label' => __( 'No activation codes required?', 'fablesounds' ),'description' => __( 'Is this product a update and needs no activation codes?', 'fablesounds' ) ) );
	woocommerce_wp_textarea_input( array( 'id' => 'bk_product_message', 'label' => __( 'Ineligible Message', 'fablesounds' ), 'class' => 'widefat' ) );
	?>
	<p class="form-field">
		<label for="bk_eligible_products"><?php _e( 'Eligible Products', 'fablesounds' ); ?></label>
		<input type="hidden" class="wc-product-search" style="width: 50%;" id="bk_eligible_products" name="bk_eligible_products" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'fablesounds' ); ?>" data-action="woocommerce_json_search_products" data-multiple="true" data-exclude="<?php echo intval( $post->ID ); ?>" data-selected="<?php
			$product_ids = array_filter( array_map( 'absint', (array) get_post_meta( $post->ID, 'bk_eligible_products', true ) ) );
			$json_ids    = array();

			foreach ( $product_ids as $product_id ) {
				$product = wc_get_product( $product_id );
				if ( is_object( $product ) ) {
					$json_ids[ $product_id ] = wp_kses_post( html_entity_decode( $product->get_formatted_name(), ENT_QUOTES, get_bloginfo( 'charset' ) ) );
				}
			}

			echo esc_attr( json_encode( $json_ids ) );
		?>" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" /> <?php echo wc_help_tip( __( 'Comma separated list of eligble product SKU\'s once this product is bought.', 'fablesounds' ) ); ?>
	</p>
<?php }
