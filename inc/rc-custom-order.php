<?php
class customOrder
{
    function __construct($args)
    {
        //var_dump($args); 
        /**
         * $this->name
         * $this->number
         * $this->state
         * $this->city
         * $this->address
         * $this->quantity
         * $this->product_id
         * $this->_ATTRIBUTE_NAME
         */

        foreach ($args as $key => $arg) {
            $this->$key = $arg;

            if (strpos($key, 'attr_') !== FALSE) {
                $key = str_ireplace('attr_', '', $key);
                $this->attr[$key] = $arg;
            }
        }

        $this->custom_order();
    }
    function custom_order()
    {

        $order = wc_create_order();

        $args = array(
            'variation' => $this->attr,
        );
        $order->add_product(wc_get_product($this->product_id), $this->quantity, $args);
        $billing_address = array(
            'first_name' => $this->name,
            'address_1'  => $this->address,
            'city'       => $this->city,
            'state'      => $this->state
        );
        $order->set_address($billing_address, 'billing');
        $order->update_status('on-hold');

        $order->calculate_totals();
        $order->save();
        $the_order = wc_get_order($order->id);

        $thank_you_page = $the_order->get_checkout_order_received_url();
        // exit(wp_redirect($thank_you_page));
        wp_redirect($thank_you_page);
        exit();
    }
}
