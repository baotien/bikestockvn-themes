<?php
/**
 * Cart Version 2
 */
if (!defined('ABSPATH')) {
    exit;
}
$porto_woo_version = porto_get_woo_version_number();
?>
<div class="cart-v2">
    <h2 class="heading-primary m-b-md font-weight-normal clearfix">
        <span><?php _e('Shopping Cart', 'porto'); ?></span>
    </h2>
    <div class="row">
        <div class="col-md-8 col-lg-9">

            <div class="featured-box align-left">
                <div class="box-content">
                    <form class="woocommerce-cart-form" action="<?php echo esc_url(version_compare($porto_woo_version, '2.5', '<') ? WC()->cart->get_cart_url() : wc_get_cart_url() ); ?>" method="post">
                        <?php do_action('woocommerce_before_cart_table'); ?>
                        <table class="shop_table responsive cart woocommerce-cart-form__contents" cellspacing="0">
                            <thead>
                                <tr>
                                    <th colspan="2" class="product-name"><?php _e('Product Name', 'porto'); ?></th>
                                    <th class="product-price"><?php _e('Unit Price', 'porto'); ?></th>
                                    <th class="product-quantity"><?php _e('Qty', 'porto'); ?></th>
                                    <th class="product-subtotal"><?php _e('Subtotal', 'porto'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php do_action('woocommerce_before_cart_contents'); ?>
                                <?php
                                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                                    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                                        ?>
                                        <tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                                            <td class="product-thumbnail">
                                                <?php
                                                $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
                                                if (!$_product->is_visible()) {
                                                    echo $thumbnail;
                                                } else {
                                                    printf('<a href="%s">%s</a>', $_product->get_permalink($cart_item), $thumbnail);
                                                }
                                                ?>
                                            </td>
                                            <td class="product-name">
                                                <?php
                                                if (!$_product->is_visible()) {
                                                    echo apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;';
                                                } else {
                                                    echo apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', $_product->get_permalink($cart_item), $_product->get_name()), $cart_item, $cart_item_key);
                                                }
                                                // Meta data
                                                echo WC()->cart->get_item_data($cart_item);
                                                // Backorder notification
                                                if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                                    echo '<p class="backorder_notification">' . __('Available on backorder', 'porto') . '</p>';
                                                }
                                                ?>
                                            </td>
                                            <td class="product-price">
                                                <?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key, $cart_item); ?>
                                            </td>
                                            <td class="product-quantity">
                                                <?php
                                                if ($_product->is_sold_individually())
                                                    $product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                                                else {
                                                    $product_quantity = woocommerce_quantity_input(array(
                                                        'input_name' => "cart[{$cart_item_key}][qty]",
                                                        'input_value' => $cart_item['quantity'],
                                                        'max_value' => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
                                                        'min_value' => '0'
                                                            ), $_product, false);
                                                } echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key);
                                                ?>
                                                <input type="hidden" name="input-product-price" value="<?php echo $_product->price; ?>" />
                                            </td>
                                            <td class="product-subtotal">
                                                <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                <?php do_action('woocommerce_cart_contents'); ?>
                                <tr>
                                    <td colspan="6" class="actions">
                                        <?php if (version_compare($porto_woo_version, '2.5', '<') ? WC()->cart->coupons_enabled() : wc_coupons_enabled()) { ?>
                                            <div class="panel-group">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading arrow">
                                                        <h2 class="panel-title"><a class="accordion-toggle" data-toggle="collapse" href="#panel-cart-discount"><?php _e('DISCOUNT CODE', 'porto'); ?></a></h2>
                                                    </div>
                                                    <div id="panel-cart-discount" class="accordion-body collapse">
                                                        <div class="panel-body">
                                                            <div class="coupon">
                                                                <label for="coupon_code"><?php _e('Enter your coupon code if you have one:', 'porto'); ?></label>
                                                                <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" />
                                                                <input type="submit" class="btn btn-primary" name="apply_coupon" value="<?php esc_attr_e('Apply Coupon', 'porto'); ?>" />
                                                                <?php do_action('woocommerce_cart_coupon'); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php } ?>
                                        <?php wp_nonce_field('woocommerce-cart'); ?>
                                    </td>
                                </tr>

                                <?php do_action('woocommerce_after_cart_contents'); ?>
                            </tbody>
                        </table>

                        <div class="cart-actions">
                            <a class="btn btn-default" href="<?php echo get_permalink(woocommerce_get_page_id('shop')); ?>"><?php _e('Continue Shopping', 'porto'); ?></a>
                            <input type="submit" class="btn btn-default pt-right" name="update_cart" value="<?php esc_attr_e('Update Cart', 'porto'); ?>" />
                            <?php do_action('woocommerce_cart_actions'); ?>
                        </div>
                        <div class="clear"></div>
                        <?php do_action('woocommerce_after_cart_table'); ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-3">
            <div class="cart-collaterals">
                <div class="panel-group">
                    <?php do_action('woocommerce_cart_collaterals'); ?>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    jQuery(function ($) {
        
        jQuery("[name='update_cart']").hide();

        $("form.woocommerce-cart-form").on("change", "input.qty", function () {
            var total_price = 0;
            jQuery("form.woocommerce-cart-form input.qty").each(function () {
                var quantity = this.value;
                var price = $(this).parents().eq(2).find("input[name='input-product-price']").val();
                var sub_total = price * quantity;
                $(this).parents().eq(2).find('.product-subtotal .woocommerce-Price-amount').html(sub_total.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '&nbsp;<span class="woocommerce-Price-currencySymbol">đ</span>');
                total_price += price * quantity;
            });

            jQuery('.order-total .woocommerce-Price-amount').html(total_price.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '&nbsp;<span class="woocommerce-Price-currencySymbol">đ</span>');
        });
        
        $(".wc-proceed-to-checkout").on("click", "a", function() {
            jQuery("[name='update_cart']").trigger("click");
            window.location.href = this.attr("href");
        });

    });
</script>