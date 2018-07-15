<?php if (!defined('ABSPATH')) exit; ?>
<div id="payment" class="woocommerce-checkout-payment">
    <h3>Phương Thức Thanh Toán</h3> 
    <div class="form-row place-order">
        <noscript>
        <?php _e('Since your browser does not support JavaScript, or it is disabled, please ensure you click the <em>Update Totals</em> button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce'); ?>
        <br/><input type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e('Update totals', 'woocommerce'); ?>" />
        </noscript>
        <?php wc_get_template('checkout/terms.php'); ?>
        <h3>Tổng: &nbsp; <span><?php wc_cart_totals_order_total_html(); ?></span></h3>
        <?php echo apply_filters('woocommerce_order_button_html', '<input type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr($order_button_text) . '" data-value="' . esc_attr($order_button_text) . '" /><img src="' . $img . '" srcset="' . $img_2x . '" alt="loader"/>'); ?>
        <?php wp_nonce_field('woocommerce-process_checkout'); ?>
    </div>
</div>