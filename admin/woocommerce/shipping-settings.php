<?php // Shipbubble woocommerce shipping settings

    /**
     * Check if WooCommerce is active
     */
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
    {
        function shipbubble_shipping_service_init() 
        {
            if ( ! class_exists( 'WC_SHIPBUBBLE_SHIPPING_METHOD' ) )  {
                class WC_SHIPBUBBLE_SHIPPING_METHOD extends WC_Shipping_Method 
                {
                    /**
                     * Constructor for your shipping class
                     *
                     * @access public
                     * @return void
                     */
                    public function __construct() 
                    {
                        $this->id                 = SHIPBUBBLE_ID; // Id for your shipping method. Should be uunique.
                        $this->method_title       = __( 'Shipbubble' );  // Title shown in admin
                        
                        $this->method_description = __( '' ); // Description shown in admin

                        // Define user set variables
                        $this->enabled            = $this->get_option('activate_shipbubble', 'no'); // This can be added as an setting but for this example its forced enabled
                        $this->title              = "Shipbubble"; // This can be added as an setting but for this example its forced.

                        $this->init();
                    }

                    /**
                     * Init your settings
                     *
                     * @access public
                     * @return void
                     */
                    public function init() 
                    {

                        $this->display_errors();

						$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings

                        $this->init_settings(); // This is part of the settings API. Loads settings you previously init.


                        // Save settings in admin if you have any defined
                        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
                    }

                    public function init_form_fields()
                    {
	                    $response = shipbubble_get_wallet_balance(shipbubble_get_token());


	                    // Load the settings API
	                    if (isset($response->response_code) && $response->response_code == SHIPBUBBLE_RESPONSE_IS_OK) {
		                    $countries_obj = new WC_Countries();
		                    $countries = $countries_obj->__get('countries');
		                    $default_country = $countries_obj->get_base_country();

		                    // $courier_options = shipbubble_courier_options();
		                    $categories_options = shipbubble_get_order_categories();

		                    // $isEnabled = '<br><div class="sb-activated-not">Not Activated for use</div>';
		                    // if ($this->get_option('activate_shipbubble', 'no') == 'yes') {
		                    //     $isEnabled = '<br><div class="sb-activated-success">Activated for use</div>';
		                    // }

		                    // $this->method_description .= $isEnabled;

		                    $this->form_fields = array(
			                    'live_mode' => array(
				                    'title' => __('Change Mode', 'woocommerce'),
				                    'type' => 'shipbubble_switch',
				                    'class' => ['switch-checkbox', 'address_form_field'],
				                    'description' => __('', 'woocommerce'),
				                    'default' => __('yes', 'woocommerce'),
			                    ),
			                    'activate_shipbubble' => array(
				                    'title' => __('Activate to use', 'woocommerce'),
				                    'type' => 'checkbox',
				                    'class' => 'address_form_field',
				                    'description' => __('Activate Shipubble on Checkout.', 'woocommerce'),
				                    'default' => __('no', 'woocommerce'),
			                    ),
			                    'sender_name' => array(
				                    'title' => __('Sender\'s Name', 'woocommerce'),
				                    'type' => 'text',
				                    'class' => 'address_form_field',
				                    'description' => __('This is the first and last name of the sender sender.', 'woocommerce'),
				                    'placeholder' => __('Store Sender Name', 'woocommerce'),
			                    ),
			                    'sender_phone' => array(
				                    'title' => __('Sender\'s Phone Number', 'woocommerce'),
				                    'type' => 'text',
				                    'class' => 'address_form_field',
				                    'description' => __('This is the phone number of the sender.', 'woocommerce'),
			                    ),
			                    'sender_email' => array(
				                    'title' => __('Sender\'s Email Address', 'woocommerce'),
				                    'type' => 'text',
				                    'class' => 'address_form_field',
				                    'description' => __('This is the email of the sender.', 'woocommerce'),
			                    ),
			                    'pickup_address' => array(
				                    'title' => __('Sender Address', 'woocommerce'),
				                    'type' => 'text',
				                    'class' => 'address_form_field',
				                    'description' => __('This is the address setup for pickup.', 'woocommerce'),
				                    'default' => __('', 'woocommerce'),
				                    // 'custom_attributes' => array('readonly' => 'readonly')
			                    ),
			                    'pickup_state' => array(
				                    'title' => __('Sender State', 'woocommerce'),
				                    'type' => 'text',
				                    'class' => 'address_form_field',
			                    ),
			                    'pickup_country' => array(
				                    'title' => __('Sender Country', 'woocommerce'),
				                    'type' => 'select',
				                    'class' => 'address_form_field',
				                    'options' => $countries,
				                    'default' => __($default_country, 'woocommerce'),
			                    ),
			                    'store_category' => array(
				                    'title' => __('Store Category', 'woocommerce'),
				                    'type' => 'select',
				                    'options' => $categories_options,
				                    'class' => 'address_form_field',
				                    'custom_attributes' => array('required' => 'required')
				                    // 'default'        => __( '', 'woocommerce' ),
			                    ),
			                    'disable_other_shipping_methods' => array(
				                    'title' => __('Disable Other Shipping Method', 'woocommerce'),
				                    'type' => 'checkbox',
				                    'class' => 'address_form_field',
				                    'description' => __('Shipbubble will disable other shipping methods.', 'woocommerce'),
				                    'default' => __('no', 'woocommerce'),
			                    ),
			                    'address_code' => array(
				                    // 'title'         => __( 'Address Code', 'woocommerce' ),
				                    'type' => 'hidden',
				                    // 'description'     => __( 'This is the address code setup for pickup (66502255).', 'woocommerce' ),
				                    'default' => __('0', 'woocommerce'),
				                    'class' => 'address_form_field',
				                    // 'custom_attributes' => array('readonly' => 'readonly')
			                    ),
			                    'sandbox_address_code' => array(
				                    // 'title'         => __( 'Address Code', 'woocommerce' ),
				                    'type' => 'hidden',
				                    // 'description'     => __( 'This is the address code setup for pickup (66502255).', 'woocommerce' ),
				                    'default' => __('0', 'woocommerce'),
				                    'class' => 'address_form_field',
				                    // 'custom_attributes' => array('readonly' => 'readonly')
			                    ),
			                    'live_api_key' => array(
				                    'title' => __('Live API Key', 'woocommerce'),
				                    'type' => 'text',
				                    'class' => 'api_form_field',
				                    'description' => __('', 'woocommerce'),
				                    'placeholder' => 'sb_prod_xxxxxxxxxxxxxxxxxxxxx'
			                    ),
			                    'sandbox_api_key' => array(
				                    'title' => __('Test API Key', 'woocommerce'),
				                    'type' => 'text',
				                    'class' => 'api_form_field',
				                    'description' => __('', 'woocommerce'),
				                    'placeholder' => 'sb_sandbox_xxxxxxxxxxxxxxxxxxxxx'
			                    ),
		                    );
	                    } else {
		                    $this->form_fields = array(
			                    'live_api_key' => array(
				                    'title' => __('API Key', 'woocommerce'),
				                    'type' => 'text',
				                    'description' => __('', 'woocommerce'),
				                    'placeholder' => 'sb_prod_xxxxxxxxxxxxxxxxxxxxx'
			                    ),
			                    'sandbox_api_key' => array(
				                    'title' => __('Test API Key', 'woocommerce'),
				                    'type' => 'text',
				                    'description' => __('', 'woocommerce'),
				                    'placeholder' => 'sb_sandbox_xxxxxxxxxxxxxxxxxxxxx'
			                    ),
		                    );
	                    }
                        
                    }

                    /**
                     * calculate_shipping function.
                     *
                     * @access public
                     * @param array $package optional – multi-dimensional array of cart items to calc shipping for.
                     * @return void
                     */
                    public function calculate_shipping( $package = array() ) 
                    {
                        // This is where you'll add your rates
                        $rate = array(
                            'id'     => $this->id,
                            'label' => $this->title,
                            'cost' => '50000',
                            // 'calc_tax' => 'per_item'
                        );
                        // This will add custom cost to shipping method 

                        // Register the rate
                        $this->add_rate( $rate );
                    }
                }
            }
        }

        add_action( 'woocommerce_shipping_init', 'shipbubble_shipping_service_init' );

        function shipbubble_couriers_methods( $methods ) 
        {
            $methods['shipbubble_shipping_services'] = 'WC_SHIPBUBBLE_SHIPPING_METHOD';
            return $methods;
        }

        add_filter( 'woocommerce_shipping_methods', 'shipbubble_couriers_methods' );

	    add_filter('woocommerce_generate_shipbubble_switch_html', 'generate_shipbubble_switch', 10, 4);

	    function generate_shipbubble_switch($field_html, $key, $value, $wc_settings) {

			if (empty($wc_settings->get_option('sandbox_api_key'))) return $field_html;
		    $switch_status = shipbubble_is_live_mode() ? 'Live' : 'Test';
		    $switch_color = shipbubble_is_live_mode() ? 'green' : 'grey';
			$class = $value['class'];
			if (is_array($class)) {
				$class = implode(' ', $class);
			}
			$title = $value['title'];
		    $field_key = $wc_settings->get_field_key( $key );
			$value['desc_tip'] = false;
		    ob_start();
		    ?>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $title ); ?> <?php echo $wc_settings->get_tooltip_html( $value ); // WPCS: XSS ok. ?></label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php echo wp_kses_post( $title ); ?></span></legend>
                        <label class="switch">
                            <input type="checkbox" id="woocommerce_shipbubble_shipping_services_live_mode" name="woocommerce_shipbubble_shipping_services_live_mode" value="1" class="<?php echo esc_attr($class); ?>" <?php checked(shipbubble_is_live_mode()); ?>>
                            <span class="slider round" style="background-color: <?php echo esc_attr($switch_color); ?>;"></span>
                        </label>
                        <span class="switch-status" style="color: <?php echo esc_attr($switch_color); ?>; margin-left: 20px;"><?php echo esc_html($switch_status); ?></span>
                    </fieldset>
                </td>
            </tr>
		    <?php
		    return ob_get_clean();
	    }

    }
