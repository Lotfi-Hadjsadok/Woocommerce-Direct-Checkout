<?php

/**
 * Plugin Name: Raysel Checkout
 * Description: Checkout on product page
 * Author: Lotfi Hadjsadok
 * Author URI: https://www.facebook.com/lotfihadjsadok.dev
 * Version: 1.5
 * Text Domain: raysel-checkout
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) die();
class RayselCheckout
{
    function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'front_css'));
        add_action('admin_notices', array($this, 'woo_error'));
        add_action('admin_init', array($this, 'main_links'));
        add_action('admin_menu', array($this, 'admin_settings'));
        add_action('init', array($this, 'languages'));
        if (get_option('rc_activation') == '1') {
            add_action('init', array($this, 'activate'));
        }
    }
    function createOrder()
    {
        if (isset($_POST['rc_order']) && isset($_POST['rc_product_id']) && isset($_POST['rc_name']) && isset($_POST['rc_number']) && isset($_POST['rc_state']) && isset($_POST['rc_city']) && isset($_POST['rc_address']) && isset($_POST['rc_quantity'])) {

            $args = array(
                'product_id' => $_POST['rc_product_id'],
                'name' => $_POST['rc_name'],
                'number' => $_POST['rc_number'],
                'address' => $_POST['rc_address'],
                'state' => $_POST['rc_state'],
                'city' => $_POST['rc_city'],
                'quantity' => $_POST['rc_quantity'],
            );

            $product = wc_get_product($args['product_id']);

            if ($product->get_attributes()) {
                $productMeta = get_post_meta($args['product_id'], '_product_attributes');
                $productAttributes = $productMeta[0];
                foreach ($productAttributes as $key => $attribute) {

                    $args['attr_' . $key] = $_POST["rc_$key"];
                }
            }

            require_once(plugin_dir_path(__FILE__) . 'inc/rc-custom-order.php');
            new customOrder($args);
        }
    }
    function front_css()
    {
        wp_enqueue_style('front_style', plugins_url('/build/style-index.css', __FILE__), 20);

        wp_enqueue_script('rc-js', plugin_dir_url(__FILE__) . '/build/index.js',  ['jquery']);
    }
    function activate()
    {
        if (class_exists('woocommerce')) {
            if (isset($_POST['rc_order']))
                remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
            add_action(
                'woocommerce_single_product_summary',
                array($this, 'checkout_form'),
                15
                /** Position of the Checkout form */
            );
            $this->createOrder();
        }
    }
    function checkout_form()
    {
        require_once(dirname(__FILE__) . '/inc/checkout-form.php');
    }
    function main_links()
    {
        // Link Admin CSS
        wp_enqueue_style('main_css', plugin_dir_url(__FILE__) . '/build/style-index.css', 20);
    }

    // Admin Settings
    function admin_settings()
    {
        /* Main Page */
        add_menu_page('Raysel Checkout', 'Raysel Checkout', 'manage_options', 'raysel-checkout', array($this, 'admin_settings_html'), 'dashicons-store', 30);
        add_settings_section('rc_settings_section', null, null, 'raysel-checkout');
        // Activate setting
        register_setting('rc_settings', 'rc_activation', array('sanitize_callback' => 'sanitize_text_field', 'default' => '0'));
        add_settings_field('rc_activation', __('Activate', 'raysel-checkout'), array($this, 'rc_activate_html'), 'raysel-checkout', 'rc_settings_section');

        // 1st Content setting
        register_setting('rc_settings', 'rc_first_content', array('sanitize_callback' => 'sanitize_text_field'));
        add_settings_field('rc_first_content', __('First Content', 'raysel-checkout'), array($this, 'rc_content_html'), 'raysel-checkout', 'rc_settings_section', array('setting' => 'rc_first_content', 'input' => 'text'));

        // 1st Content Color
        register_setting('rc_settings', 'rc_first_color', array('sanitize_callback' => 'sanitize_text_field'));
        add_settings_field('rc_first_color', __('First Content Color', 'raysel-checkout'), array($this, 'rc_content_html'), 'raysel-checkout', 'rc_settings_section', array('setting' => 'rc_first_color', 'input' => 'color'));

        // 2nd Content Setting
        register_setting('rc_settings', 'rc_second_content', array('sanitize_callback' => 'sanitize_text_field'));
        add_settings_field('rc_second_content', __('Second Content', 'raysel-checkout'), array($this, 'rc_content_html'), 'raysel-checkout', 'rc_settings_section', array('setting' => 'rc_second_content', 'input' => 'text'));

        // 2nd Content Color
        register_setting('rc_settings', 'rc_second_color', array('sanitize_callback' => 'sanitize_text_field'));
        add_settings_field('rc_second_color', __('Second Content Color', 'raysel-checkout'), array($this, 'rc_content_html'), 'raysel-checkout', 'rc_settings_section', array('setting' => 'rc_second_color', 'input' => 'color'));

        // Submit Button Text
        register_setting('rc_settings', 'rc_submit_btn', array('sanitize_callback' => 'sanitize_text_field'));
        add_settings_field('rc_submit_btn', __('Submit button text', 'raysel-checkout'), array($this, 'rc_content_html'), 'raysel-checkout', 'rc_settings_section', array('setting' => 'rc_submit_btn', 'input' => 'text'));

        // Email for orders
        register_setting('rc_settings', 'rc_email', array('sanitize_callback' => 'sanitize_text_field'));
        add_settings_field('rc_email', __('Email for orders', 'raysel-checkout'), array($this, 'rc_content_html'), 'raysel-checkout', 'rc_settings_section', array('setting' => 'rc_email', 'input' => 'email'));

        // Contact us number
        register_setting('rc_settings', 'rc_number', array('sanitize_callback' =>  'sanitize_text_field'));
        add_settings_field('rc_number', __('Call Button Number', 'raysel-checkout'), array($this, 'rc_content_html'), 'raysel-checkout', 'rc_settings_section', array('setting' => 'rc_number', 'input' => 'text'));

        // Submit button Color
        register_setting('rc_settings', 'rc_submit_btn_color', array('sanitize_callback' => 'sanitize_text_field'));
        add_settings_field('rc_submit_btn_color', __('Submit Button Color', 'raysel-checkout'), array($this, 'rc_content_html'), 'raysel-checkout', 'rc_settings_section', array('setting' => 'rc_submit_btn_color', 'input' => 'color'));


        /* Settings Page */
        add_submenu_page('raysel-checkout', __('Settings', 'raysel-checkout'), __('Settings', 'raysel-checkout'), 'manage_options', 'raysel-checkout-settings', array($this, 'form_settings'));
    }


    function form_settings()
    {
?>
        <div class="wrap">
            <p class="main-title">Form Settings</p>
            <?php
            require_once(plugin_dir_path(__FILE__) . '/inc/settings.php');
            ?>
        </div>
    <?php
    }
    function rc_content_html($args)
    {
    ?>
        <input type="<?php echo $args['input'] ?>" name="<?php echo $args['setting'] ?>" value="<?php echo esc_attr(get_option($args['setting'])) ?>" id="">

    <?php
    }
    function rc_activate_html()
    {
    ?>
        <input type="checkbox" name="rc_activation" value="1" <?php checked(get_option('rc_activation', '0'), '1') ?> id="">
    <?php
    }
    function admin_settings_html()
    {
    ?>
        <div class="wrap">
            <p class="main-title">Raysel Checkout</p>
            <p class="main-title__headline"><?php echo __('Checkout in product page', 'rayse-checkout') ?></p>
            <form action="options.php" method="POST">
                <?php
                settings_errors();
                settings_fields('rc_settings');
                do_settings_sections('raysel-checkout');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    function woo_error()
    {
        if (!class_exists('woocommerce')) { ?>
            <div class="notice notice-error">
                <p><?php echo  esc_html__('You must activate Woocommerce', 'raysel-checkout'); ?></p>
            </div>
<?php
        }
    }

    function languages()
    {

        load_plugin_textdomain('raysel-checkout', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
}

$RC = new RayselCheckout();
