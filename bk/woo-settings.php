<?php
class WC_Settings_Tab_FableSounds{

    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_settings_tab_fablesounds', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_settings_tab_fablesounds', __CLASS__ . '::update_settings' );
    }

    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['settings_tab_fablesounds'] = __( 'Fable Sounds', 'fablesounds' );
        return $settings_tabs;
    }

    public static function settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }

    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }

    public static function get_settings() {

      $settings = array(
        array(
            'name'     => __( 'My Account Page tabs', 'fablesounds' ),
            'type'     => 'title',
            'desc'     => '',
            'id'       => 'wc_settings_tab_fablesounds_section_title'
        ),
        // array(
        //       'name' => __( 'Dashboard title', 'fablesounds' ),
        //       'type' => 'text',
        //       'css'  => 'width:80%;',
        //       'id'   => 'wc_settings_bk_dashboard_title'
        //   ),
        array(
              'name' => __( 'Dasboard description', 'fablesounds' ),
              'type' => 'textarea',
              'css'  => 'width:80%; height: 150px;',
              'id'   => 'wc_settings_bk_dashboard_description'
          ),
        array(
              'name' => __( 'Registered Products Title', 'fablesounds' ),
              'type' => 'text',
              'css'  => 'width:80%;',
              // 'desc' => __( 'This is some helper text', 'fablesounds' ),
              'id'   => 'wc_settings_registered_products_title'
          ),
        array(
              'name' => __( 'Registered Products Description', 'fablesounds' ),
              'type' => 'textarea',
              // 'desc' => __( 'sjsjjsjsj shsjs d hd', 'fablesounds' ),
              'css'  => 'width:80%; height: 150px;',
              'id'   => 'wc_settings_registered_products_description'
          ),
        array(
                'name' => __( 'Register a new product Title', 'fablesounds' ),
                'type' => 'text',
                'css'  => 'width:80%;',
                'id'   => 'wc_settings_register_new_product_title'
            ),
        array(
                'name' => __( 'Register a new product Description', 'fablesounds' ),
                'type' => 'textarea',
                'css'  => 'width:80%; height: 150px;',
                'id'   => 'wc_settings_register_new_product_description'
            ),
        array(
                'name' => __( 'Register HALion - powered BBB Title', 'fablesounds' ),
                'type' => 'text',
                'css'  => 'width:80%;',
                'id'   => 'wc_settings_register_halion_title'
            ),
        array(
                'name' => __( 'Register HALion - powered BBB Description', 'fablesounds' ),
                'type' => 'textarea',
                'css'  => 'width:80%; height: 150px;',
                'id'   => 'wc_settings_register_halion_description'
            ),
        array(
                'name' => __( 'Available updates/upgrades Title', 'fablesounds' ),
                'type' => 'text',
                'css'  => 'width:80%;',
                'id'   => 'wc_settings_available_updates_upgrades_title'
            ),
       array(
                'name' => __( 'Available updates/upgrades Description', 'fablesounds' ),
                'type' => 'textarea',
                'css'  => 'width:80%; height: 150px;',
                'id'   => 'wc_settings_available_updates_upgrades_description'
            ),
      array(
              'name' => __( 'Ineligible to upgrades message', 'fablesounds' ),
              'type' => 'textarea',
              'css'  => 'width:80%; height: 150px;',
              'id'   => 'wc_settings_upgrades_message'
          ),
      array(
              'name' => __( 'Ineligible to updates message', 'fablesounds' ),
              'type' => 'textarea',
              'css'  => 'width:80%; height: 150px;',
              'id'   => 'wc_settings_updates_message'
          ),
      array(
              'name' => __( 'Ineligible to backup products message', 'fablesounds' ),
              'type' => 'textarea',
              'css'  => 'width:80%; height: 150px;',
              'id'   => 'wc_settings_backups_message'
          ),
      array(
              'type' => 'sectionend',
              'id' => 'wc_settings_tab_fablesounds_section_end'
            )
        );

        return apply_filters( 'wc_settings_tab_fablesounds_settings', $settings );
    }

}

WC_Settings_Tab_FableSounds::init();
