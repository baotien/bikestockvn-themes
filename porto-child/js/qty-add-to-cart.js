/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(function ($) {
    
    /* when product quantity changes, update quantity attribute on add-to-cart button */
    $("form.woocommerce-cart-form").on("change", "input.qty", function () {
//        var total_price = 0;
//        jQuery("form.woocommerce-cart-form input.qty").each(function () {
//            var quantity = this.value;
//            var price = $(this).parents().eq(2).find("input[name='input-product-price']").val();
//            var sub_total = price * quantity;
//            $(this).parents().eq(2).find('.product-subtotal .woocommerce-Price-amount').html(sub_total.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '&nbsp;<span class="woocommerce-Price-currencySymbol">đ</span>');
//            total_price += price * quantity;
//        });
//
//        jQuery('.order-total .woocommerce-Price-amount').html(total_price.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '&nbsp;<span class="woocommerce-Price-currencySymbol">đ</span>');
        
        jQuery("[name='update_cart']").removeAttr("disabled").trigger("click");

//        var form = jQuery(this).closest('form');
//        var formData = form.serialize();
//        jQuery.post(form.attr('ajax_url'), formData);
    });

});