<?php
$metabox_product = get_post_meta($post_id, '', true);
$quality = isset($metabox_product['quality'][0]) ? $metabox_product['quality'][0] : '';
$sell_option = isset($metabox_product['sell_option'][0]) ? $metabox_product['sell_option'][0] : '';
$start_price = isset($metabox_product['start_price'][0]) ? $metabox_product['start_price'][0] : '';
$set_price = isset($metabox_product['set_price'][0]) ? $metabox_product['set_price'][0] : '';
$price = isset($metabox_product['price'][0]) ? $metabox_product['price'][0] : '';
$price_sent = isset($metabox_product['price_sent'][0]) ? $metabox_product['price_sent'][0] : '';
$time = isset($metabox_product['time'][0]) ? $metabox_product['time'][0] : '';
$post_item_end_date = isset($metabox_product['post_item_end_date'][0]) ? $metabox_product['post_item_end_date'][0] : '';
$select_date_end = isset($metabox_product['select_date_end'][0]) ? $metabox_product['select_date_end'][0] : '';
$check_sent_request = isset($metabox_product['check_sent_request'][0]) ? $metabox_product['check_sent_request'][0] : '';
$reject_request = isset($metabox_product['reject_request'][0]) ? $metabox_product['reject_request'][0] : '';
$accept_request = isset($metabox_product['accept_request'][0]) ? $metabox_product['accept_request'][0] : '';
$start_time = isset($metabox_product['start_time_moza'][0]) ? $metabox_product['start_time_moza'][0] : '';
$end_time = isset($metabox_product['end_time_moza'][0]) ? $metabox_product['end_time_moza'][0] : '';


?>
<div class="fom_group_data_moza" id="fom_group_data_moza">
    <div class="form_create_moza">
        <?php wp_nonce_field('save_auction_product_options', 'auction_product_options_nonce'); ?>

        <p>
            <label for="quality">کیفیت ایتم</label>
            <?php $terms = get_terms(
                array(
                    'taxonomy' => 'quality',
                    'hide_empty' => false,
                )
            );

            // بررسی وجود تاکسونومی‌ها
            if (!empty($terms) && !is_wp_error($terms)) {
                echo '<select name="quality" id="quality">';
                foreach ($terms as $term) { ?>
                    <option value="<?php echo esc_attr($term->slug) ?>" <?php selected($quality, esc_attr($term->slug)); ?>>
                        <?php echo esc_html($term->name); ?>
                    </option>
                    <?php
                }
                echo '</select>';
            } else {
                echo '<select name="quality" id="quality">';
                echo '<option value="">هیچ کیفیتی موجود نیست</option>';
                echo '</select>';
            } ?>
        </p>
        <p>
            <label for="option_sell">نوع حراج</label>
            <select name="option_sell" id="option_sell">
                <option value="aution" <?php selected($sell_option, 'aution'); ?>>حراج</option>
                <option value="by_only_one" <?php selected($sell_option, 'by_only_one'); ?>>هم اکنون خرید کنید</option>
            </select>
        </p>



        <p>
        <div class="aution">
            <label for="start_price">شروع قیمت</label>
            <input type="number" placeholder="قیمت شروع معامله تومان" name="start_price" id="start_price"
                value="<?php echo esc_attr($start_price); ?>" required>
            <label for="set_price">قیمت رزرو</label>
            <input type="number" name="set_price" placeholder="قیمت رزور ایتم تومان" id="set_price"
                value="<?php echo esc_attr($set_price); ?>">
        </div>
        </p>

        <p>
            <label for="price_sent">قیمت ارسال</label>
            <input type="number" name="price_sent" id="price_sent" placeholder="هزینه ارسال ایتم تومان"
                value="<?php echo esc_attr($price_sent); ?>">
        </p>

        <p>
        <div>
            <label for="start_time">تاریخ:</label>
            <input type="text" id="start_time" name="start_time_moza" value="<?php echo $start_time; ?>" data-date="<?php echo $start_time; ?>">
        </div>
        </p>
        <p>
            <label for="select_date_end">تاریخ پایان</label>
            <select name="select_date_end" id="select_date_end">
                <option value="1" <?php selected($select_date_end, '1'); ?>>یک روز</option>
                <option value="3" <?php selected($select_date_end, '3'); ?>>سه روز</option>
                <option value="7" <?php selected($select_date_end, '7'); ?>>هفت روز</option>
            </select>

        </p>
        <p>
            <label for="post_item_end_date">ثبت مجدد ایتم در صورت عدم فروش</label>
            <select name="post_item_end_date" id="post_item_end_date">
                <option value="1" <?php selected($post_item_end_date, '1'); ?>>یک بار</option>
                <option value="2" <?php selected($post_item_end_date, '2'); ?>>دو بار</option>
                <option value="3" <?php selected($post_item_end_date, '3'); ?>>سه بار</option>
            </select>

        </p>
        <p>
        <div class="sent_request_button">
            <label for="check_sent_request">ارسال پیشنهاد</label>
            <input type="checkbox" name="check_sent_request" id="check_sent_request" class="toggle-button" <?php checked($check_sent_request, "on"); ?>>
        </div>
        </p>


        <div class="sent_request">
            <p>
                <label for="reject_request">رد درخواست کمتر از</label>
                <input type="number" name="reject_request" id="reject_request"
                    placeholder="رد کردن درخواست کمتر از تومان" value="<?php echo esc_attr($reject_request); ?>">
            </p>
            <p>
                <label for="accept_request">پذیرش اتومات درخواست بالاتر از</label>
                <input type="number" name="accept_request" id="accept_request"
                    placeholder="پذیرش درخواست بالاتر از تومان" value="<?php echo esc_attr($accept_request); ?>">
            </p>

        </div>
    </div><!-- .dashboard-content-area -->
</div>