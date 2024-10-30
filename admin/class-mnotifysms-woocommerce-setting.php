<?php

class Mnotifysms_WooCoommerce_Setting {

    private $settings_api;

    function __construct() {
        $this->settings_api = new WeDevs_Settings_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        add_options_page( 'MnotifySMS WooCommerce', 'MnotifySMS WooCommerce', 'manage_options', 'mnotifysms-woocoommerce-setting', array($this, 'plugin_page') );
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'mnotifysms_setting',
                'title' => __( 'MnotifySMS Settings', 'mnotifysms-woocoommerce' )
            ),
            array(
                'id' => 'admin_setting',
                'title' => __( 'Vendor Settings', 'mnotifysms-woocoommerce' )
            ),


            array(
                'id' => 'customer_setting',
                'title' => __( 'Customer Settings', 'mnotifysms-woocoommerce' )
            )
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $additional_billing_fields = '';
        $additional_billing_fields_desc = '';
        $additional_billing_fields_array = $this->get_additional_billing_fields();
        foreach ($additional_billing_fields_array as $field) {
            $additional_billing_fields .= ', ['.$field.']';
        }
        if($additional_billing_fields) {
            $additional_billing_fields_desc = '<br />Custom tags: '.substr($additional_billing_fields, 2);
        }
        $settings_fields = array(
            'mnotifysms_setting' => array(    
                array(
                    'name'              => 'mnotifysms_woocommerce_api_key',
                    'label'             => __( 'API Key', 'mnotifysms-woocoommerce' ),
                    'desc'              => __( 'Enter your Mnotify API Key. You can create an account <a href="https://apps.mnotify.net/home/register" target="blank">here</a>', 'mnotifysms-woocoommerce' ),
                    'type'              => 'text',
                ),
               
                array(
                    'name'              => 'mnotifysms_woocommerce_sms_from',
                    'label'             => __( 'Sender ID (Message From)', 'mnotifysms-woocoommerce' ),
                    'desc'              => __( 'Sender of the SMS. NB: Sender ID must have a maximum length up to 11 characters. Exclude special characters like +, -, .  ', 'mnotifysms-woocoommerce' ),
                    'type'              => 'text',
                ),
                           
            ),
            'admin_setting' => array(    
                array(
                    'name' => 'mnotifysms_woocommerce_admin_send_sms',
                    'label' => __( 'Enable Admin SMS Notifications', 'mnotifysms-woocoommerce' ),
                    'desc' => ' '.__( 'Enable  sms notification for new order placed', 'mnotifysms-woocoommerce' ),
                    'type' => 'checkbox',
                    'default' => 'on',
                ),     
                array(
                    'name'              => 'mnotifysms_woocommerce_admin_sms_recipients',
                    'label'             => __( 'Phone Number', 'mnotifysms-woocoommerce' ),
                    'desc'              => __( 'Phone number to receive new order SMS notification. For multiple receivers, separate each number with comma and phone number must include country code, e.g. 23320811902, 4444853500', 'mnotifysms-woocoommerce' ),
                    'type'              => 'text',
                ),
                array(
                    'name' => 'mnotifysms_woocommerce_admin_sms_template',
                    'label' => __( 'Admin SMS Message', 'mnotifysms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc, 'mnotifysms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',
                    'default' => __('[shop_name] : You have a new order with order ID [order_id] from [billing_first_name], [billing_last_name]. The order is now [order_status].', 'mnotifysms-woocoommerce')
                )  
            ),

            //    'vendor_setting' => array(    

            //        array(
            //         'name' => 'mnotifysms_woocommerce_admin_sms_template',
            //         'label' => __( 'Admin SMS Message', 'mnotifysms-woocoommerce' ),
            //         'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc, 'mnotifysms-woocoommerce' ),
            //         'type' => 'textarea',
            //         'rows' => '8',
            //         'cols' => '500',
            //         'css' => 'min-width:350px;',
            //         'default' => __('[shop_name] : You have a new order with order ID [order_id] and order amount [order_currency] [order_amount]. The order is now [order_status].', 'mnotifysms-woocoommerce')
            //     ) 
                
            // ),

            'customer_setting' => array(      
                array(
                    'name'    => 'mnotifysms_woocommerce_send_sms',
                    'label'   => __( 'Send notification on', 'mnotifysms-woocoommerce' ),
                    'desc'    => __( 'Send notification to customer on order status', 'mnotifysms-woocoommerce' ),
                    'type'    => 'multicheck',
                    'options' => array(
                        'pending'   => ' Pending',
                        'on-hold'   => ' On-hold',
                        'processing' => ' Processing',
                        'completed'  => ' Completed',
                        'cancelled'  => ' Cancelled',
                        'refunded'  => ' Refunded',
                        'failed'  => ' Failed'
                    )
                ),                
                array(
                    'name' => 'mnotifysms_woocommerce_sms_template_default',
                    'label' => __( 'Default Customer SMS Message', 'mnotifysms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [bank_details], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc.'<br />Bank details will only be included when bank transfer option is chosen.', 'mnotifysms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',
                    'default' => __(' Thank you for shopping with us. Your order ([order_id]) is currently [order_status].', 'mnotifysms-woocoommerce')
                ),    
                array(
                    'name' => 'mnotifysms_woocommerce_sms_template_pending',
                    'label' => __( 'Pending SMS Message', 'mnotifysms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [bank_details], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc.'<br />Bank details will only be included when bank transfer option is chosen.', 'mnotifysms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',                    
                    'default' => __(' Thank you for shopping with us. Your order ([order_id]) is currently [order_status].', 'mnotifysms-woocoommerce')
                ),           
                array(
                    'name' => 'mnotifysms_woocommerce_sms_template_on-hold',
                    'label' => __( 'On-hold SMS Message', 'mnotifysms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [bank_details], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc.'<br />Bank details will only be included when bank transfer option is chosen.', 'mnotifysms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',                    
                    'default' => __(' Thank you for shopping with us. Your order ([order_id]) is currently [order_status].', 'mnotifysms-woocoommerce')
                ),  
                array(
                    'name' => 'mnotifysms_woocommerce_sms_template_processing',
                    'label' => __( 'Processing SMS Message', 'mnotifysms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [bank_details], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc.'<br />Bank details will only be included when bank transfer option is chosen.', 'mnotifysms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',                    
                    'default' => __(' Thank you for shopping with us. Your order ([order_id]) is currently [order_status].', 'mnotifysms-woocoommerce')
                ),  
                array(
                    'name' => 'mnotifysms_woocommerce_sms_template_completed',
                    'label' => __( 'Completed SMS Message', 'mnotifysms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc, 'mnotifysms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',                    
                    'default' => __(' Thank you for shopping with us. Your order ([order_id]) is currently [order_status].', 'mnotifysms-woocoommerce')
                ),  
                array(
                    'name' => 'mnotifysms_woocommerce_sms_template_cancelled',
                    'label' => __( 'Cancelled SMS Message', 'mnotifysms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc, 'mnotifysms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',                    
                    'default' => __(' Thank you for shopping with us. Your order ([order_id]) is currently [order_status].', 'mnotifysms-woocoommerce')
                ),  
                array(
                    'name' => 'mnotifysms_woocommerce_sms_template_refunded',
                    'label' => __( 'Refunded SMS Message', 'mnotifysms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc, 'mnotifysms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',                    
                    'default' => __(' Thank you for shopping with us. Your order ([order_id]) is currently [order_status].', 'mnotifysms-woocoommerce')
                ),  
                array(
                    'name' => 'mnotifysms_woocommerce_sms_template_failed',
                    'label' => __( 'Failed SMS Message', 'mnotifysms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc, 'mnotifysms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',                    
                    'default' => __(' Thank you for shopping with us. Your order ([order_id]) is currently [order_status].', 'mnotifysms-woocoommerce')
                )
            )
        );                
        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }
    
    function get_additional_billing_fields() {
        $default_billing_fields = array(
            'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 
            'billing_country', 'billing_postcode', 'billing_phone', 'billing_email'
        );
        $additional_billing_field = array();
        $billing_fields = array_filter(get_option('wc_fields_billing', array()));
        foreach($billing_fields as $field_key => $field_info) {
            if(!in_array($field_key, $default_billing_fields) && $field_info['enabled']) {
                array_push($additional_billing_field, $field_key);
            }
        }
        return $additional_billing_field;
    }    
}

?>
