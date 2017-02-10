<?php
add_action( 'load-post.php', 'bk_halion_meta_boxes_setup' );
add_action( 'load-post-new.php', 'bk_halion_meta_boxes_setup' );

function bk_halion_meta_boxes_setup() {
	add_action( 'add_meta_boxes', 'bk_add_halion_meta_boxes' );
  add_action( 'save_post', 'bk_save_halion_meta', 10, 2 );
}

function bk_add_halion_meta_boxes() {
	add_meta_box(
    'bk-halion-meta',
    esc_html__( 'Halion Codes', 'bk' ),
    'bk_halion_meta_box',
    array('fs_halion_codes'),
    'normal',
    'default'
  );
}

function bk_halion_meta_box($object, $box) {
	?>

  <?php wp_nonce_field( basename( __FILE__ ), 'bk_ac_meta_nonce' ); ?>
   <p>
  	<label for="bk-halion-user-email"><?php _e( "User ID:", 'bk' ); ?>
    <?php $user_id = get_post_meta( $object->ID, 'bk_ac_user_email', true ); ?>
      <input type="text" name="bk-halion-user-email" class="" id="bk-halion-user-email" value="<?php echo esc_html($user_id);?>" />
    </label>
   </p>
   <p>
  	<label for="bk-ac-product-sku"><?php _e( "Product SKU:", 'bk' ); ?>
    <?php $product_id = get_post_meta( $object->ID, 'bk_ac_product_sku', true ); ?>
      <input type="text" name="bk-ac-product-sku" class="" id="bk-ac-product-sku" value="<?php echo esc_html($product_id);?>" />
    </label>
   </p>
   <p>
  	<label for="bk-halion-status"><?php _e( "Status:", 'bk' ); ?>
    <?php $status = get_post_meta( $object->ID, 'bk_halion_status', true );?>
			<select name="bk-halion-status" id="bk-halion-status">
					<option value="nreg" <?php selected( $status, 'nreg' ); ?>>Not Registered</option>
					<option value="reg" <?php selected( $status, 'reg' ); ?>>Registered</option>
			</select>
    </label>
   </p>
   <p>
  	<label for="bk-halion-date"><?php _e( "Date:", 'bk' ); ?>
    <?php $date = get_post_meta( $object->ID, 'bk_halion_date', true ); ?>
      <input type="text" name="bk-halion-date" class="" id="bk-halion-date" value="<?php echo esc_html($date);?>" />
    </label>
   </p>
<?php
}

function bk_save_halion_meta( $post_id, $post ) {

	$post_type = get_post_type_object( $post->post_type );

	if( 'fs_halion_codes' == $post->post_type) {
	  if ( !isset( $_POST['bk_ac_meta_nonce'] ) || !wp_verify_nonce( $_POST['bk_ac_meta_nonce'], basename( __FILE__ ) ) )
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

	if( 'fs_halion_codes' == $post->post_type) {
		$buid = ( isset( $_POST['bk-halion-user-email'] ) ? intval($_POST['bk-halion-user-email']) : '' );
	  $bpid = ( isset( $_POST['bk-ac-product-sku'] ) ? esc_attr($_POST['bk-ac-product-sku']) : '' );
	  $bstatus= ( isset( $_POST['bk-halion-status'] ) ? esc_attr($_POST['bk-halion-status']) : '' );
	  $bdate = ( isset( $_POST['bk-halion-date'] ) ? $_POST['bk-halion-date'] : '' );

	  $meta_keys = array(
	    'bk_halion_user_email' => $buid,
	    'bk_ac_product_sku' => $bpid,
	    'bk_halion_status' => $bstatus,
	    'bk_halion_date' => $bdate
	  );

	}

  foreach ($meta_keys as $meta_key => $new_meta_value) {
    $meta_value = get_post_meta( $post_id, $meta_key, true );
    update_post_meta( $post_id, $meta_key, $new_meta_value );
  }

}
