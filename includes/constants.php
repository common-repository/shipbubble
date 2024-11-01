<?php // predefined constants

define('SHIPBUBBLE_BASE_URL', 'https://api.shipbubble.com/v1/shipping');
define('SHIPBUBBLE_ID', 'shipbubble_shipping_services');
define('SHIPBUBBLE_INIT', 'shipbubble_init');
define('WC_SHIPBUBBLE_ID', 'woocommerce_' . SHIPBUBBLE_ID . '_settings');
define('SHIPBUBBLE_REQUEST_TOKEN_EXPIRY', 120); // hours
define('SHIPBUBBLE_EP_REQUEST_TIMEOUT', 60);
define('SHIPBUBBLE_RESPONSE_IS_OK', 200);
define('SHIPBUBBLE_WC_BAD_ORDER_STATUS_ARR', ['pending payment', 'on hold', 'cancelled', 'refunded', 'failed']);
define('SHIPBUBBLE_EXT_BASE_URL', site_url());
define('SHIPBUBBLE_PLUGIN_VERSION', 'shipbubble_plugin_version');
define('SHIPBUBBLE_ADDRESS_VALIDATED', 'address_validated');
define('SHIPBUBBLE_SANDBOX_ADDRESS_VALIDATED', 'sandbox_address_validated');
