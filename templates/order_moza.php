<?php
/**
 *  Dokan Dashboard Orders Template
 *
 *  Load order related template
 *
 *  @since 2.4
 *
 *  @package dokan
 */
?>

<?php do_action('dokan_dashboard_wrap_start'); ?>

<div class="dokan-dashboard-wrap">

    <?php

    /**
     *  Added dokan_dashboard_content_before hook
     *
     *  @hooked get_dashboard_side_navigation
     *
     *  @since 2.4
     */
    do_action('dokan_dashboard_content_before');
    do_action('dokan_order_content_before');

    ?>

    <div class="dokan-dashboard-content dokan-orders-content">

        <?php

        /**
         *  Added dokan_order_content_inside_before hook
         *
         *  @hooked show_seller_enable_message
         *
         *  @since 2.4
         */
        do_action('dokan_order_content_inside_before');
        ?>


        <article class="dokan-orders-area">

            <?php

            /**
             *  Added dokan_order_inside_content Hook
             *
             *  @hooked dokan_order_listing_status_filter
             *  @hooked dokan_order_main_content
             *
             *  @since 2.4
             */
            global $woocommerce;
            if(isset($_POST['order_id'])){
                $order_id = $_POST['order_id'];
                $order = dokan()->order->get( $order_id );
                $order->update_status( 'sent' );
                wp_redirect(get_current_url());
            }
            if ( isset( $_GET['seller_order_filter_nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['seller_order_filter_nonce'] ) ), 'seller-order-filter-nonce' ) ) {
                $status_class = isset( $_GET['order_status'] ) ? sanitize_text_field( wp_unslash( $_GET['order_status'] ) ) : $status_class;
            }
            if (!isset($_GET['order_id'])) {

                dokan_order_listing_status_filter_moza();

                if ($user_orders) {
                    ?>
                        <table class="dokan-table dokan-table-striped">
                            <thead>
                                <tr>
                                    <th id="cb" class="manage-column column-cb check-column">
                                        <label for="cb-select-all"></label>
                                        <input id="cb-select-all" class="dokan-checkbox" type="checkbox">
                                    </th>
                                    <th><?php esc_html_e('Order', 'dokan-lite'); ?></th>
                                    <th><?php esc_html_e('Order Total', 'dokan-lite'); ?></th>
                                    <th><?php esc_html_e('Earning', 'dokan-lite'); ?></th>
                                    <th><?php esc_html_e('Status', 'dokan-lite'); ?></th>
                                    <th><?php esc_html_e('Customer', 'dokan-lite'); ?></th>
                                    <th><?php esc_html_e('Date', 'dokan-lite'); ?></th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($user_orders as $order) { // phpcs:ignore
                                    /**
                                     * @var WC_Order $order
                                     */
                                    echo $order->get_status();
                                    ?>
                                    <?php if($order->get_status()=="sent"):?>
                                    <tr>
                                        <th class="dokan-order-select check-column">
                                            <label for="cb-select-<?php echo esc_attr($order->get_id()); ?>"></label>
                                            <input class="cb-select-items dokan-checkbox" type="checkbox" name="bulk_orders[]"
                                                value="<?php echo esc_attr($order->get_id()); ?>">
                                        </th>
                                        <td class="dokan-order-id column-primary"
                                            data-title="<?php esc_attr_e('Order', 'dokan-lite'); ?>">
                                            <?php if (current_user_can('dokan_view_order')) { ?>
                                                <?php
                                                echo '<a href="'
                                                    . esc_url(wp_nonce_url(add_query_arg(['order_id' => $order->get_id()], dokan_get_navigation_url('order_moza')), 'dokan_view_order'))
                                                    . '"><strong>'
                                                    // translators: 1) order number
                                                    . sprintf(__('Order %s', 'dokan-lite'), esc_attr($order->get_order_number())) . '</strong></a>';
                                                ?>
                                            <?php } else { ?>
                                                <?php
                                                echo '<strong>'
                                                    // translators: 1) order number
                                                    . sprintf(__('Order %s', 'dokan-lite'), esc_attr($order->get_order_number()))
                                                    . '</strong>';
                                                ?>
                                            <?php } ?>

                                            <button type="button" class="toggle-row"></button>
                                        </td>
                                        <td class="dokan-order-total"
                                            data-title="<?php esc_attr_e('Order Total', 'dokan-lite'); ?>">
                                            <?php echo $order->get_formatted_order_total(); ?>
                                        </td>
                                        <td class="dokan-order-earning"
                                            data-title="<?php esc_attr_e('Earning', 'dokan-lite'); ?>">
                                            <?php echo wp_kses_post(wc_price(dokan()->commission->get_earning_by_order($order))); ?>
                                        </td>
                                        <td class="dokan-order-status" data-title="<?php esc_attr_e('Status', 'dokan-lite'); ?>">
                                            <?php echo '<span class="dokan-label dokan-label-' . dokan_get_order_status_class_moza($order->get_status()) . '">' . dokan_get_order_status_translated_moza($order->get_status()) . '</span>'; ?>
                                        </td>
                                        <td class="dokan-order-customer"
                                            data-title="<?php esc_attr_e('Customer', 'dokan-lite'); ?>">
                                            <?php
                                            $customer_full_name = trim($order->get_formatted_billing_full_name());
                                            $user = empty($customer_full_name) ? __('Guest', 'dokan-lite') : $customer_full_name;
                                            echo esc_html($user);
                                            ?>
                                        </td>
                                        <td class="dokan-order-date" data-title="<?php esc_attr_e('Date', 'dokan-lite'); ?>">
                                            <?php
                                            if ('0000-00-00 00:00:00' === $order->get_date_created()->format('Y-m-d H:i:s')) {
                                                $t_time = __('Unpublished', 'dokan-lite');
                                                $h_time = __('Unpublished', 'dokan-lite');
                                            } else {
                                                $t_time = $order->get_date_created();
                                                $time_diff = time() - $t_time->getTimestamp();

                                                // get human-readable time
                                                $h_time = $time_diff > 0 && $time_diff < 24 * 60 * 60
                                                    // translators: 1)  human-readable date
                                                    ? sprintf(__('%s ago', 'dokan-lite'), human_time_diff($t_time->getTimestamp(), time()))
                                                    : dokan_format_date($t_time->getTimestamp());

                                                // fix t_time
                                                $t_time = dokan_format_date($t_time->getTimestamp());
                                            }

                                            echo '<abbr title="' . esc_attr($t_time) . '">' . esc_html(apply_filters('post_date_column_time', $h_time, $order->get_id())) . '</abbr>';
                                            ?>
                                        </td>

                                        <?php do_action('dokan_order_listing_row_before_action_field', $order); ?>

                                        <?php if (current_user_can('dokan_manage_order')) { ?>
                                            <td class="dokan-order-action" width="17%"
                                                data-title="<?php esc_attr_e('Action', 'dokan-lite'); ?>">
                                                <?php if($order->get_status()=='processing'):?>
                                                <form action="#" method = "post">
                                                     <input type="hidden" name="order_id" value="<?php echo $order->get_id();?>">
                                                    <button>ارسال محصول</button>
                                                 </form>
                                                 <?php endif;?>                                                 
                                                 <a href="../order_moza?order_id">جزئیات سفارش</a>

                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <?php else:?>
                                        <div class="dokan-error">
                                         <?php esc_html_e('No orders found', 'dokan-lite'); ?>
                                         </div>
                                    <?php
                                    endif;

                                }
                                ?>

                            </tbody>

                        </table>
                    </form>
                    <?php
                } else {
                    ?>

                    <div class="dokan-error">
                        <?php esc_html_e('No orders found', 'dokan-lite'); ?>
                    </div>

                <?php }

            } else {

                
                foreach ($user_orders as $order) {
                    ?>
                    <div class="order-container">
                        <h2>سفارش شماره: <?php echo $order->get_id(); ?></h2>
                        <div class="order-details">
                            <div class="order-general-info">
                                <h3>جزئیات عمومی</h3>
                                <p><strong>وضعیت سفارش:</strong> <?php echo wc_get_order_status_name($order->get_status()); ?></p>
                                <p><strong>تاریخ سفارش:</strong> <?php echo $order->get_date_created()->date('Y-m-d H:i:s'); ?></p>
                                <p><strong>هزینه ارسال :</strong> <?php echo $order->get_shipping_total(); ?></p>
                                <p><strong>مبلغ کل:</strong> <?php echo wc_price($order->get_total()); ?></p>
                                <p><strong>مشتری:</strong> <?php echo $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(); ?></p>
                                <p><strong>ایمیل:</strong> <?php echo $order->get_billing_email(); ?></p>
                                <p><strong>تلفن:</strong> <?php echo $order->get_billing_phone(); ?></p>
                                <p><strong>شهر:</strong> <?php echo $order->get_billing_city(); ?></p>
                                <p><strong>آدرس:</strong> <?php echo $order->get_billing_address_1(); ?></p>
                                <form action="#" method = "post">
                                        <input type="hidden" name="order_id" value="<?php echo $order->get_id();?>">
                                         <button>ارسال محصول</button>
                                </form>
                            </div>
                            <div class="order-items">
                                <h3>موارد سفارش</h3>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>نام محصول</th>
                                            <th>قیمت پایه</th>
                                            <th>تعداد</th>
                                            <th>مجموع</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($order->get_items() as $item) {
                                            ?>
                                            <tr>
                                                <td><?php echo $item->get_name(); ?></td>
                                                <td><?php echo wc_price($item->get_total() / $item->get_quantity()); ?></td>
                                                <td><?php echo $item->get_quantity(); ?></td>
                                                <td><?php echo wc_price($item->get_total()); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <div class="dokan-panel-body" id="dokan-order-notes">
                        <?php
                        $args = [
                            'post_id' => $order->get_id(),
                            'approve' => 'approve',
                            'type'    => 'order_note',
                            'status'  => 1,
                        ];

                        remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
                        $notes = get_comments( $args );

                        echo '<ul class="order_notes list-unstyled">';

                        if ( $notes ) {
                            foreach ( $notes as $note ) {
                                $note_classes = get_comment_meta( $note->comment_ID, 'is_customer_note', true ) ? array( 'customer-note', 'note' ) : array( 'note' );

                                ?>
                                <li rel="<?php echo esc_attr( absint( $note->comment_ID ) ); ?>" class="<?php echo esc_attr( implode( ' ', $note_classes ) ); ?>">
                                    <div class="note_content">
                                        <?php echo wp_kses_post( wpautop( wptexturize( $note->comment_content ) ) ); ?>
                                    </div>
                                    <p class="meta">
                                        <?php
                                        // translators: 1) human-readable date
                                        printf( esc_html__( 'added %s ago', 'dokan-lite' ), esc_textarea( human_time_diff( dokan_current_datetime()->setTimezone( new DateTimeZone( 'UTC' ) )->modify( $note->comment_date_gmt )->getTimestamp(), time() ) ) );
                                        ?>
                                        <?php if ( current_user_can( 'dokan_manage_order_note' ) ) : ?>
                                            <a href="#" class="delete_note"><?php esc_html_e( 'Delete note', 'dokan-lite' ); ?></a>
                                        <?php endif ?>
                                    </p>
                                </li>
                                <?php
                            }
                        } else {
                            echo '<li>' . esc_html__( 'There are no notes for this order yet.', 'dokan-lite' ) . '</li>';
                        }
                        ?>
                                <div class="add_note">
                            <?php if ( current_user_can( 'dokan_manage_order_note' ) ) : ?>
                                <h4><?php esc_html_e( 'Add note', 'dokan-lite' ); ?></h4>
                                <form class="dokan-form-inline" id="add-order-note" role="form" method="post">
                                    <p>
                                        <textarea type="text" id="add-note-content" name="note" class="form-control" cols="19" rows="3"></textarea>
                                    </p>
                                    <div class="clearfix">
                                        <div class="order_note_type dokan-form-group">
                                            <select name="note_type" id="order_note_type" class="dokan-form-control">
                                                <option value="customer"><?php esc_html_e( 'Customer note', 'dokan-lite' ); ?></option>
                                                <option value=""><?php esc_html_e( 'Private note', 'dokan-lite' ); ?></option>
                                            </select>
                                        </div>

                                        <input type="hidden" name="security" value="<?php echo esc_attr( wp_create_nonce( 'add-order-note' ) ); ?>">
                                        <input type="hidden" name="delete-note-security" id="delete-note-security" value="<?php echo esc_attr( wp_create_nonce( 'delete-order-note' ) ); ?>">
                                        <input type="hidden" name="post_id" value="<?php echo esc_attr( $order->get_id() ); ?>">
                                        <input type="hidden" name="action" value="dokan_add_order_note">
                                        <input type="submit" name="add_order_note" class="add_note btn btn-sm btn-theme dokan-btn-theme" value="<?php esc_attr_e( 'Add Note', 'dokan-lite' ); ?>">
                                    </div>
                                </form>
                            <?php endif; ?>
                                <?php if ( ! dokan()->is_pro_exists() || 'on' !== dokan_get_option( 'enabled', 'dokan_shipping_status_setting' ) ) : ?>
                                <div class="clearfix dokan-form-group" style="margin-top: 10px;">
                                    <!-- Trigger the modal with a button -->
                                    <input type="button" id="dokan-add-tracking-number" name="add_tracking_number" class="dokan-btn dokan-btn-success" value="<?php esc_attr_e( 'Tracking Number', 'dokan-lite' ); ?>">

                                    <form id="add-shipping-tracking-form" method="post" class="dokan-hide" style="margin-top: 10px;">
                                        <div class="dokan-form-group">
                                            <label class="dokan-control-label"><?php esc_html_e( 'Shipping Provider Name / URL', 'dokan-lite' ); ?></label>
                                            <input type="text" name="shipping_provider" id="shipping_provider" class="dokan-form-control" value="">
                                        </div>

                                        <div class="dokan-form-group">
                                            <label class="dokan-control-label"><?php esc_html_e( 'Tracking Number', 'dokan-lite' ); ?></label>
                                            <input type="text" name="tracking_number" id="tracking_number" class="dokan-form-control" value="">
                                        </div>

                                        <div class="dokan-form-group">
                                            <label class="dokan-control-label"><?php esc_html_e( 'Date Shipped', 'dokan-lite' ); ?></label>
                                            <input type="text" name="shipped_date" id="shipped-date" class="dokan-form-control" value="" placeholder="<?php echo esc_attr( get_option( 'date_format' ) ); ?>">
                                        </div>
                                        <input type="hidden" name="security" id="security" value="<?php echo esc_attr( wp_create_nonce( 'add-shipping-tracking-info' ) ); ?>">
                                        <input type="hidden" name="post_id" id="post-id" value="<?php echo esc_attr( $order->get_id() ); ?>">
                                        <input type="hidden" name="action" id="action" value="dokan_add_shipping_tracking_info">

                                        <div class="dokan-form-group">
                                            <input id="add-tracking-details" type="button" class="btn btn-primary" value="<?php esc_attr_e( 'Add Tracking Details', 'dokan-lite' ); ?>">
                                            <button type="button" class="btn btn-default" id="dokan-cancel-tracking-note"><?php esc_html_e( 'Close', 'dokan-lite' ); ?></button>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>


        </article>


        <?php

        /**
         *  Added dokan_order_content_inside_after hook
         *
         *  @since 2.4
         */
        do_action('dokan_order_content_inside_after');
        ?>

    </div> <!-- #primary .content-area -->

    <?php

    /**
     *  Added dokan_dashboard_content_after hook
     *  dokan_order_content_after hook
     *
     *  @since 2.4
     */
    do_action('dokan_dashboard_content_after');
    do_action('dokan_order_content_after');

    ?>

</div><!-- .dokan-dashboard-wrap -->

<?php do_action('dokan_dashboard_wrap_end'); ?>