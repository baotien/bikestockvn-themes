<?php

/**

 * My Account page

 *

 * @version     2.6.0

 */



if ( ! defined( 'ABSPATH' ) ) {

    exit;

}



$porto_woo_version = porto_get_woo_version_number();



wc_print_notices();



if (version_compare($porto_woo_version, '2.6', '>=')) {

    /**

     * My Account navigation.

     *

     * @since 2.6.0

     */

    do_action( 'woocommerce_account_navigation' );

}

?>

<?php if (version_compare($porto_woo_version, '2.6', '>=')) : ?>



    <div class="woocommerce-MyAccount-content">

        <div class="featured-box align-left">

            <div class="box-content">



                <?php

                /**

                 * My Account content.

                 * @since 2.6.0

                 */

                do_action( 'woocommerce_account_content' );

                ?>



            </div>

        </div>

    </div>



<?php else : ?>



    <p class="myaccount_user alert alert-success m-b-lg">

        <?php

        printf(

            __( 'Xin chào <strong>%1$s</strong> (không phải %1$s? <a href="%2$s">Thoát</a>).', 'woocommerce' ) . ' ',

            $current_user->display_name,

            wc_get_endpoint_url( 'customer-logout', '', wc_get_page_permalink( 'myaccount' ) )

        );



        printf( __( 'Từ trang này bạn có thể xem lại các đơn hàng, quản lý địa chỉ nhận và <a href="%s">thay đổi mật khẩu và thông tin tài khoản</a>.', 'woocommerce' ),

            wc_customer_edit_account_url()

        );

        ?>

    </p>



    <?php do_action( 'woocommerce_before_my_account' ); ?>



    <?php wc_get_template( 'myaccount/my-downloads.php' ); ?>



    <?php wc_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) ); ?>



    <?php wc_get_template( 'myaccount/my-address.php' ); ?>



    <?php do_action( 'woocommerce_after_my_account' ); ?>



<?php endif; ?>

