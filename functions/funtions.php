<?php

function show_product_code()
{
    global $product;
    $product->set_price(50000);

    if (empty($product)) {
        return;
    }
    $product_id = $product->get_id();
    $deta = [
        'product_id' => $product_id,
    ];
    my_plugin_get_template("detayl_product_in_bid.php", $deta);
}

function add_bils_moza_to_wo($tabs)
{
    $tabs['docs'] = array(
        'title' => __('پیشنهادات', 'woocommerce'), // TAB TITLE
        'priority' => 50, // TAB SORTING (DESC 10, ADD INFO 20, REVIEWS 30)
        'callback' => 'show_bils_moza', // TAB CONTENT CALLBACK
    );
    return $tabs;
}

function show_bils_moza()
{
    my_plugin_get_template("show_bils_moza_html.php");
}


function menu_mza_in_price()
{
    // اضافه کردن منوی اصلی
    add_menu_page(
        'تنظیمات مزایده',
        'تنظیمات مزایده',
        'edit_posts',
        'menu_slug',
        'page_moza_fun',
        'dashicons-media-spreadsheet'
    );

    add_submenu_page(
        'menu_slug',
        'کیفیت مزایده',
        'کیفیت مزایده',
        'edit_posts',
        'edit-tags.php?taxonomy=quality&post_type=product'
    );

}

// تابع برای محتوای صفحه منوی اصلی
function page_moza_fun()
{
    my_plugin_get_template("seteng_moza_rang_price.php");
}

// تابع برای محتوای صفحه زیرمنو
function submenu_page_fun()
{
    echo '<h1>این صفحه زیرمنوی تنظیمات است</h1>';
}

function save_custom_price_ranges()
{
    // Check if form is submitted and nonce is valid
    if (isset($_POST['submit_custom_price_ranges'])) {
        check_admin_referer('custom_price_ranges_nonce', 'custom_price_ranges_nonce');

        // Prepare data to be saved
        $data = array();
        for ($i = 1; $i <= 12; $i++) {
            $low_value = isset($_POST["low$i"]) ? sanitize_text_field($_POST["low$i"]) : '';
            $high_value = isset($_POST["high$i"]) ? sanitize_text_field($_POST["high$i"]) : '';
            $increase_value = isset($_POST["increase$i"]) ? sanitize_text_field($_POST["increase$i"]) : '';

            $data["low$i"] = $low_value;
            $data["high$i"] = $high_value;
            $data["increase$i"] = $increase_value;
        }

        // Update or add the option in the options table
        update_option('custom_price_ranges_options', $data);

        // Redirect after saving
        echo "اعملیات مفقیت آمیز بود";

    }
}

function create_quality_taxonomy()
{
    $labels = array(
        'name' => _x('کیفیت مزایده', 'quality_moza_class', 'moza'),
        'singular_name' => _x(' کیفیت مزایده', 'snichery', 'moza'),
        'search_items' => __('جستجوی کیفیت‌های مزایده', 'moza'),
        'all_items' => __('تمام کیفیت‌های مزایده', 'moza'),
        'parent_item' => __('کیفیت والد', 'moza'),
        'parent_item_colon' => __('کیفیت والد:', 'moza'),
        'edit_item' => __('ویرایش کیفیت مزایده', 'moza'),
        'update_item' => __('به‌روزرسانی کیفیت مزایده', 'moza'),
        'add_new_item' => __('افزودن کیفیت مزایده جدید', 'moza'),
        'new_item_name' => __('نام کیفیت مزایده جدید', 'moza'),
        'menu_name' => __('کیفیت مزایده', 'moza'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'quality'),
    );

    register_taxonomy('quality', array('product'), $args);
}

function get_price_ranges()
{
    return get_option('custom_price_ranges_options', []);
}


function findMaxBidAmount($bids)
{
    if (empty($bids)) {
        return null; // یا می‌توانید مقدار دیگری را برگردانید مثل 0 یا "آرایه خالی است"
    }

    $maxBid = $bids[0]['bid_amount'];
    $userId = $bids[0]['user_id'];

    foreach ($bids as $bid) {
        if ($bid['bid_amount'] > $maxBid) {
            $maxBid = $bid['bid_amount'];
            $userId = $bid['user_id'];
        }
    }

    return ['user_id' => $userId, 'bid_amount' => $maxBid];
}

function save_bid()
{


    if (isset($_POST['product_id']) && isset($_POST['bid_amount']) && isset($_POST['user_id'])) {
        $product_id = intval($_POST['product_id']);
        $bid_amount = sanitize_text_field($_POST['bid_amount']);
        $user_id = intval($_POST['user_id']);
        $metabox_product = get_post_meta($product_id, '', true);

        $start_price = isset($metabox_product['start_price'][0]) ? $metabox_product['start_price'][0] : 0;
        if ($bid_amount >= $start_price) {

            // Get current bids
            $current_bids = get_post_meta($product_id, 'bids', true);
            if (!$current_bids) {
                $current_bids = [];
            }
            if (!$current_bids) {
                $current_bids[] = [
                    'user_id' => $user_id,
                    'bid_amount' => $bid_amount,
                    'timestamp' => current_time('mysql') // Optional: add timestamp
                ];
                update_post_meta($product_id, 'bids', $current_bids);
                update_post_meta($product_id, 'bids_user', $current_bids);
                // Update bids in post meta
                wp_redirect("?save_bid=ok");
                exit;
            } else {
                $current_bids_user = get_post_meta($product_id, "bids_user", true);
                $current_bids = get_post_meta($product_id, "bids", true);
                $increas = get_option('custom_price_ranges_options', []);
                $high_bids = findMaxBidAmount($current_bids);
                $high_bids_user = findMaxBidAmount($current_bids_user);
                if ($bid_amount > $high_bids['bid_amount']) {
                    for ($i = 1; $i <= 12; $i++) {
                        $low_key = "low{$i}";
                        $high_key = "high{$i}";
                        $increase_key = "increase{$i}";

                        if (!empty($increas[$low_key]) && !empty($increas[$high_key]) && !empty($increas[$increase_key])) {
                            $low = (int) $increas[$low_key];
                            $high = (int) $increas[$high_key];
                            $increase = (int) $increas[$increase_key];


                            if ($low <= $high_bids['bid_amount'] && $high_bids['bid_amount'] <= $high) {
                                $nex_bids = $high_bids['bid_amount'] + $increase;

                                if ($bid_amount > $nex_bids) {
                                    if ($bid_amount < $high_bids_user['bid_amount']) {
                                        $current_bids[] = [
                                            'user_id' => $user_id,
                                            'bid_amount' => $bid_amount,
                                            'timestamp' => current_time('mysql') // Optional: add timestamp
                                        ];
                                        update_post_meta($product_id, 'bids', $current_bids);
                                    } else {
                                        for ($i = 1; $i <= 12; $i++) {
                                            $low_key = "low{$i}";
                                            $high_key = "high{$i}";
                                            $increase_key = "increase{$i}";

                                            if (!empty($increas[$low_key]) && !empty($increas[$high_key]) && !empty($increas[$increase_key])) {
                                                $low = (int) $increas[$low_key];
                                                $high = (int) $increas[$high_key];
                                                $increase = (int) $increas[$increase_key];


                                                if ($low <= $high_bids['bid_amount'] && $high_bids['bid_amount'] <= $high) {
                                                    $nex_bids_one = $high_bids_user['bid_amount'] + $increase;

                                                    if ($bid_amount <= $nex_bids_one) {
                                                        $nex_bids = $bid_amount;
                                                        $current_bids[] = [
                                                            'user_id' => $high_bids_user['user_id'],
                                                            'bid_amount' => $high_bids_user['bid_amount'],
                                                            'timestamp' => current_time('mysql') // Optional: add timestamp
                                                        ];
                                                        update_post_meta($product_id, 'bids', $current_bids);
                                                    } else {
                                                        $nex_bids = $nex_bids_one;
                                                        $current_bids[] = [
                                                            'user_id' => $high_bids_user['user_id'],
                                                            'bid_amount' => $high_bids_user['bid_amount'],
                                                            'timestamp' => current_time('mysql') // Optional: add timestamp
                                                        ];
                                                        update_post_meta($product_id, 'bids', $current_bids);

                                                    }
                                                }
                                            }
                                        }
                                        $current_bids[] = [
                                            'user_id' => $user_id,
                                            'bid_amount' => $nex_bids,
                                            'timestamp' => current_time('mysql') // Optional: add timestamp
                                        ];
                                        update_post_meta($product_id, 'bids', $current_bids);
                                        update_post_meta($product_id, 'start_price', $nex_bids);
                                        $current_bids[] = [
                                            'user_id' => $user_id,
                                            'bid_amount' => $bid_amount,
                                            'timestamp' => current_time('mysql') // Optional: add timestamp
                                        ];
                                        update_post_meta($product_id, 'bids_user', $current_bids);

                                    }


                                    $high_bids_user = findMaxBidAmount($current_bids_user);
                                    $high_bids = findMaxBidAmount($current_bids);
                                    if ($high_bids_user['bid_amount'] > $bid_amount) {
                                        for ($i = 1; $i <= 12; $i++) {
                                            $low_key = "low{$i}";
                                            $high_key = "high{$i}";
                                            $increase_key = "increase{$i}";

                                            if (!empty($increas[$low_key]) && !empty($increas[$high_key]) && !empty($increas[$increase_key])) {
                                                $low = (int) $increas[$low_key];
                                                $high = (int) $increas[$high_key];
                                                $increase = (int) $increas[$increase_key];
                                                $high_bids = findMaxBidAmount($current_bids);

                                                if ($low <= $high_bids['bid_amount'] && $high_bids['bid_amount'] <= $high) {
                                                    $nex_bids = $high_bids['bid_amount'] + $increase;
                                                }
                                            }
                                        }
                                        $current_bids[] = [
                                            'user_id' => $high_bids_user['user_id'],
                                            'bid_amount' => $nex_bids,
                                            'timestamp' => current_time('mysql') // Optional: add timestamp
                                        ];
                                        update_post_meta($product_id, 'bids', $current_bids);
                                        update_post_meta($product_id, 'start_price', $nex_bids);

                                    } else {
                                        $current_bids[] = [
                                            'user_id' => $user_id,
                                            'bid_amount' => $bid_amount,
                                            'timestamp' => current_time('mysql') // Optional: add timestamp
                                        ];
                                        update_post_meta($product_id, 'bids_user', $current_bids);
                                    }


                                }
                            }

                        }
                    }

                } else {
                    var_dump("پیشنهاد ارسالی کمتر از پینهاد های قبلی بوده");
                }


            }


        } else {
            ?>
            <script>
                alert("قیمت ارسالی کمتر از قیمت شروع مزایده است");
            </script>
            <?php
        }

    }
}

function register_auction_product_type()
{
    class WC_Product_Auction extends WC_Product
    {
        public function __construct($product)
        {
            $this->product_type = 'auction_product';
            parent::__construct($product);
        }
    }
}

function add_auction_product_type($types)
{
    $types['auction_product'] = __('مزایده', 'your-text-domain');
    return $types;
}

function add_dokan_auction_product_type($types)
{
    // چاپ مقدار $types برای دیباگ

    if (is_array($types)) {
        $types['auction_product'] = 'مزایده';
    } else {
        $types = array('auction_product' => 'مزایده');
    }
    return $types;


}
function get_product_author_id($product_id)
{
    // گرفتن شیء محصول
    $product = wc_get_product($product_id);

    // اگر محصول پیدا شد
    if ($product) {
        // گرفتن آی‌دی نویسنده (کاربر ایجادکننده)
        $author_id = $product->get_post_data()->post_author;
        return $author_id;
    } else {
        return false; // اگر محصول پیدا نشد
    }
}
function new_product_field_moza()
{

    my_plugin_get_template("form_group_data.php");


}

function save_add_product_meta($product_id, $postdata)
{

    if (!dokan_is_user_seller(get_current_user_id())) {
        return;
    }

    if (isset($postdata['product_type']) && $postdata['product_type'] === 'auction_product') {
        $product = wc_get_product($product_id);

        wp_set_object_terms($product_id, 'auction_product', 'product_type');


    }
    $quality = sanitize_text_field($_POST['quality']);
    $sell_option = sanitize_text_field($_POST['option_sell']);
    $start_price = isset($_POST['start_price']) ? floatval($_POST['start_price']) : 0;
    $set_price = isset($_POST['set_price']) ? floatval($_POST['set_price']) : 0;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    $price_sent = isset($_POST['price_sent']) ? floatval($_POST['price_sent']) : 0;
    $start_time = sanitize_text_field($_POST['start_time_moza']);
    $select_date_end = sanitize_text_field($_POST['select_date_end']);

    list($initial_date, $initial_time) = explode('-', $start_time);
    list($year, $month, $day) = explode('/', $initial_date);
    list($hour, $minute) = explode(':', $initial_time);

    // تبدیل تاریخ شمسی به میلادی
    $gregorian_date = jalali_to_gregorian($year, $month, $day);

    // ایجاد رشته تاریخ و زمان میلادی
    $gregorian_datetime_str = sprintf('%04d-%02d-%02d %02d:%02d:00', $gregorian_date[0], $gregorian_date[1], $gregorian_date[2], $hour, $minute);

    // ایجاد شیء DateTime از تاریخ و زمان میلادی
    $date = new DateTime($gregorian_datetime_str);
    $next_date = '+'.$select_date_end.' days';
    // اضافه کردن سه روز به تاریخ میلادی
    $date->modify($next_date);

    $modified_gregorian_date = $date->format('Y-m-d');
    $modified_time = $date->format('H:i:s');
    list($gyear, $gmonth, $gday) = explode('-', $modified_gregorian_date);

    $j_date = gregorian_to_jalali($gyear, $gmonth, $gday);

    $end_time = sprintf('%04d/%02d/%02d-%s', $j_date[0], $j_date[1], $j_date[2], $modified_time);
    $post_item_end_date = sanitize_text_field($_POST['post_item_end_date']);
    $check_sent_request = isset($_POST['check_sent_request']) ? 1 : 0;
    $reject_request = isset($_POST['reject_request']) ? floatval($_POST['reject_request']) : 0;
    $accept_request = isset($_POST['accept_request']) ? floatval($_POST['accept_request']) : 0;

    // Setting the time based on the input and converting to Jalali date


    // Creating an array to store the sanitized values
    $postdata = [
        'quality' => $quality,
        'option_sell' => $sell_option,
        'start_price' => $start_price,
        'set_price' => $set_price,
        'price' => $price,
        'price_sent' => $price_sent,
        'select_date_end' => $select_date_end,
        'start_time_moza' => $start_time,
        'end_time_moza' => $end_time,
        'post_item_end_date' => $post_item_end_date,
        'check_sent_request' => $check_sent_request,
        'reject_request' => $reject_request,
        'accept_request' => $accept_request,
    ];

    // Iterating through the array and updating post meta if not empty
    foreach ($postdata as $key => $value) {
        if (!empty($value) || $value === 0 || $value === '0') { // Check for 0 or '0' as valid values
            update_post_meta($product_id, $key, $value);
        }
    }


}

function show_on_edit_page($post, $post_id)
{
    $data = array(
        'post_id' => $post_id,
        'post' => $post
    );


    my_plugin_get_template("edit_form_group_data.php", $data);
}

function bbloomer_create_auction_product_type()
{
    class WC_Product_Custom_moza extends WC_Product
    {
        public function get_type()
        {
            return 'auction_product';
        }
    }


}

function moza_woocommerce_product_class($classname, $product_type)
{
    if ($product_type == 'auction_product') {
        $classname = 'WC_Product_Custom_moza';
    }
    return $classname;
}

function create_tab_to_moza_in_admin($tabs)
{

    $tabs['auction_product'] = array(
        'label' => __('مزایده', 'dm_product'),
        'target' => 'auction_product_options',
        'class' => 'show_if_auction_product',
        'priority' => 1, // اولویت پایین تر یعنی نمایش در بالای تب‌ها

    );
    return $tabs;
}

function auction_product_options_product_tab_content()
{
    global $post;
    $product_id = $post->ID;

    my_plugin_get_template(
        "edit_form_group_data.php",
        array(
            'post_id' => $product_id
        )
    );
}

function save_auction_product_options($post_id)
{
    // Verify nonce for security
    if (!isset($_POST['auction_product_options_nonce']) || !wp_verify_nonce($_POST['auction_product_options_nonce'], 'save_auction_product_options')) {
        return;
    }

    // Check for autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save or update meta data
    $fields = [
        'quality',
        'option_sell',
        'start_price',
        'set_price',
        'price',
        'price_sent',
        'start_time_moza',
        'end_time_moza',
        'post_item_end_date',
        'check_sent_request',
        'reject_request',
        'accept_request'
    ];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        } else {
            delete_post_meta($post_id, $field);
        }
    }

    // Add time and date processing
    if (isset($_POST['start_time_moza']) && isset($_POST['select_date_end'])) {
        $start_time = sanitize_text_field($_POST['start_time_moza']);
        $select_date_end = sanitize_text_field($_POST['select_date_end']);

        list($initial_date, $initial_time) = explode('-', $start_time);
        list($year, $month, $day) = explode('/', $initial_date);
        list($hour, $minute) = explode(':', $initial_time);

        // تبدیل تاریخ شمسی به میلادی
        $gregorian_date = jalali_to_gregorian($year, $month, $day);

        // ایجاد رشته تاریخ و زمان میلادی
        $gregorian_datetime_str = sprintf('%04d-%02d-%02d %02d:%02d:00', $gregorian_date[0], $gregorian_date[1], $gregorian_date[2], $hour, $minute);

        // ایجاد شیء DateTime از تاریخ و زمان میلادی
        $date = new DateTime($gregorian_datetime_str);
        $next_date = '+'.$select_date_end.' days';
        // اضافه کردن تعداد روزهای مورد نظر به تاریخ میلادی
        $date->modify($next_date);

        $modified_gregorian_date = $date->format('Y-m-d');
        $modified_time = $date->format('H:i:s');
        list($gyear, $gmonth, $gday) = explode('-', $modified_gregorian_date);

        $j_date = gregorian_to_jalali($gyear, $gmonth, $gday);

        $end_time = sprintf('%04d/%02d/%02d-%s', $j_date[0], $j_date[1], $j_date[2], $modified_time);

        // Update meta data with calculated end time
        update_post_meta($post_id, 'end_time_moza', sanitize_text_field($end_time));
    }
}


function obfuscateString($input)
{
    $length = strlen($input);

    if ($length <= 3) {
        return $input;
    }

    $firstTwo = substr($input, 0, 2);
    $lastChar = substr($input, -1);
    $stars = str_repeat('*', $length - 3);

    return $firstTwo . $stars . $lastChar;
}



function add_auction_to_cart()
{
    if (!isset($_POST['product_id']) || !isset($_POST['bid_amount'])) {
        wp_send_json_error();
    }

    $product_id = intval($_POST['product_id']);
    $bid_amount = floatval($_POST['bid_amount']);
    update_post_meta($product_id, '_price', $bid_amount);
    update_post_meta($product_id, '_regular_price', $bid_amount);
    // اضافه کردن محصول به سبد خرید با قیمت سفارشی
    $added = WC()->cart->add_to_cart($product_id, 1, 0, array(), array('custom_price' => $bid_amount));

    if ($added) {
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}

function add_custom_price($cart)
{
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        if (isset($cart_item['custom_price'])) {
            $cart_item['data']->set_price($cart_item['custom_price']);
        }
    }
}
function add_auction_product_to_dokan_dashboard($query)
{
    if (!is_admin() && dokan_is_seller_dashboard()) {
        if (isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'product') {
            $query->set(
                'tax_query',
                array(
                    array(
                        'taxonomy' => 'product_type',
                        'field' => 'slug',
                        'terms' => array('auction_product'),
                        'operator' => 'IN',
                    )
                )
            );
        }
    }
}
function jalali_to_gregorian_with_time($jalali_datetime) {
    // جدا کردن تاریخ و زمان
    list($jalali_date, $jalali_time) = explode('-', $jalali_datetime);
    list($year, $month, $day) = explode('/', $jalali_date);
    list($hour, $minute) = explode(':', $jalali_time);
$second =0;
    // تبدیل تاریخ شمسی به میلادی
    $gregorian_date = jalali_to_gregorian($year, $month, $day);

    // ایجاد رشته تاریخ و زمان میلادی
    $gregorian_datetime_str = sprintf(
        '%04d-%02d-%02d %02d:%02d:%02d',
        $gregorian_date[0],
        $gregorian_date[1],
        $gregorian_date[2],
        $hour,
        $minute,
        $second
    );

    return $gregorian_datetime_str;
}
// افزودن ستون جدید به لیست محصولات ووکامرس
function castome_bids_to_tabel($columns)
{
    $columns['vendor_bids'] = __('پیشنهادات', 'text-domain');
    return $columns;
}

// نمایش تعداد پیشنهادات در ستون جدید ووکامرس
function custom_admin_products_store_name_column_content($column, $product_id)
{
    if ($column == 'vendor_bids') {
        $bids = get_post_meta($product_id, 'bids', true);
        if (is_array($bids)) {
            $count_bids = count($bids);
        } else {
            $count_bids = intval($bids); // تبدیل به عدد صحیح در صورت عدم وجود آرایه
        }
        echo $count_bids;
    }
}

// افزودن ستون جدید به لیست محصولات دکان
function dcustom_vendor_bids_to_tabel($columns)
{
    $columns['vendor_bids'] = __('پیشنهادات', 'text-domain');
    return $columns;
}

// نمایش تعداد پیشنهادات در ستون جدید دکان
function dcustom_admin_products_store_name_column_content($column, $product_id)
{
    if ($column == 'vendor_bids') {
        $bids = get_post_meta($product_id, 'bids', true);
        if (is_array($bids)) {
            $count_bids = count($bids);
        } else {
            $count_bids = intval($bids); // تبدیل به عدد صحیح در صورت عدم وجود آرایه
        }
        echo $count_bids;
    }
}
function update_start_price_callback() {
    if (!isset($_GET['product_id'])) {
        wp_send_json_error();
    }

    $product_id = intval($_GET['product_id']);
    $metabox_product = get_post_meta($product_id, '', true);

    if (isset($metabox_product['start_price'][0])) {
        $start_price = number_format($metabox_product['start_price'][0]);
        wp_send_json_success(['start_price' => $start_price]);
    } else {
        wp_send_json_error();
    }
}