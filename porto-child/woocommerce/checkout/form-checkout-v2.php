<?php
if (!defined('ABSPATH')) {
    exit;
}
$porto_woo_version = porto_get_woo_version_number();
$checkout = WC()->checkout();
$get_checkout_url = version_compare($porto_woo_version, '2.5', '<') ? apply_filters('woocommerce_get_checkout_url', WC()->cart->get_checkout_url()) : wc_get_checkout_url();
?>
<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url($get_checkout_url); ?>" enctype="multipart/form-data">
    <div class="row">
        <?php if (sizeof($checkout->checkout_fields) > 0) : ?>
            <?php do_action('woocommerce_checkout_before_customer_details'); ?>
            <div class="col-md-8" id="customer_details">
                <?php do_action('woocommerce_checkout_billing'); ?>
                <?php do_action('woocommerce_checkout_shipping'); ?>
            </div>
            <?php do_action('woocommerce_checkout_after_customer_details'); ?>
        <?php endif; ?>
        <div class="checkout-order-review align-left col-md-4">
            <div id="order_review" class="woocommerce-checkout-review-order">
                <div class="row">
                    <div class="col-md-12">
                        <table class="shop_table review-order woocommerce-checkout-review-order-table">
                            <thead> 
                                <tr>
                                    <th class="product-name"><?php _e('Product', 'woocommerce'); ?></th>
                                    <th class="product-total"><?php _e('Total', 'woocommerce'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                                    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
                                        ?>
                                        <tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                                            <td class="product-name">
                                                <?php echo apply_filters('woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key) . '&nbsp;'; ?>
                                                <?php echo apply_filters('woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf('&times; %s', $cart_item['quantity']) . '</strong>', $cart_item, $cart_item_key); ?>
                                                <?php echo WC()->cart->get_item_data($cart_item); ?>
                                            </td>
                                            <td class="product-total">
                                                <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr class="cart-subtotal">
                                    <th><?php _e('Subtotal', 'woocommerce'); ?></th>
                                    <td><?php echo apply_filters('porto_get_price_html', WC()->cart->get_cart_subtotal()); ?></td>
                                </tr>
                                <?php
                                $codes = WC()->cart->get_coupons();
                                ?>
                                <?php foreach ($codes as $code => $coupon) : ?>
                                    <tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
                                        <th><?php wc_cart_totals_coupon_label($coupon); ?></th>
                                        <td><?php wc_cart_totals_coupon_html($coupon); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="order-total">
                                    <th><?php _e('Total', 'woocommerce'); ?></th>
                                    <td><?php wc_cart_totals_order_total_html(); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>