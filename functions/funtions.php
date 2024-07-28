<?php
use function MailPoetVendor\twig_var_dump;

function show_product_code()
{
    global $product;
    if (empty($product)) {
        return;
    }
    $product_id = $product->get_id();
    $deta = [
        'product_id' => $product_id,
    ];
    my_plugin_get_template("detayl_product_in_bid.php", $deta);
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



{
    if (empty($bids)) {
        return null; // یا می‌توانید مقدار دیگری را برگردانید مثل 0 یا "آرایه خالی است"
    }

    $maxBid = null;
    $maxBidId = null;
    $maxUserId = null;

    foreach ($bids as $bid) {
        // اگر userId پاس داده شده باشد، فقط پیشنهادات مربوط به آن کاربر را در نظر بگیرید
        if ($userId !== null) {
            if ($bid['user_id'] === $userId) {
                $bidid = (int)$bid['id'];
                if ($maxBidId === null || $maxBidId > $maxBidId) {
                    $maxBid = $bid['bid_amount'];
                    $maxBidId = $bid['id'];
                    $maxUserId = $bid['user_id'];
                }
            }
        } else {
            // اگر userId پاس داده نشده باشد، تمام پیشنهادات را در نظر بگیرید
            $bidid = (int)$bid['id'];
            if ($maxBidId === null || $maxBidId > $maxBidId) {
                $maxBid = $bid['bid_amount'];
                $maxBidId = $bid['id'];
                $maxUserId = $bid['user_id'];
            }
        }
    }

    return ['user_id' => $maxUserId, 'bid_id' => $maxBidId,'bid_amount'=>$maxBid];
}
function findMaxBidAmount($bids, $userId = null)
{
    if (empty($bids)) {
        return null; // یا می‌توانید مقدار دیگری را برگردانید مثل 0 یا "آرایه خالی است"
    }

    $maxBid = null;
    $maxBidId = null;
    $maxUserId = null;

    foreach ($bids as $bid) {
        // اگر userId پاس داده شده باشد، فقط پیشنهادات مربوط به آن کاربر را در نظر بگیرید
        if ($userId !== null) {
            if ($bid['user_id'] == $userId) {
                $bidAmount = (int)$bid['bid_amount'];
                if ($maxBid === null || $bidAmount > $maxBid ||$bid['id'] > $maxBidId) {
                    $maxBid = $bidAmount;
                    $maxBidId = $bid['id'];
                    $maxUserId = $bid['user_id'];
                }
            }
        } else {
            // اگر userId پاس داده نشده باشد، تمام پیشنهادات را در نظر بگیرید
            $bidAmount = (int)$bid['bid_amount'];
            if ($maxBid === null || $bidAmount > $maxBid || ($bidAmount == $maxBid && $bid['id'] > $maxBidId)) {
                $maxBid = $bidAmount;
                $maxBidId = $bid['id'];
                $maxUserId = $bid['user_id'];
            }
        }
    }

    return ['user_id' => $maxUserId, 'bid_id' => $maxBidId,'bid_amount'=>$maxBid];
}
function set_alert_message_denger($message)
{
    session_start();

    $_SESSION['alert_message_moza_denger'] = $message;
}

// نمایش پیام هشدار
function display_alert_message_denger()
{
    if (isset($_SESSION['alert_message_moza_denger'])) {
        echo '<div class="denger">' . $_SESSION['alert_message_moza_denger'] . '</div>';
        unset($_SESSION['alert_message_moza_denger']);
    }
}

function set_alert_message_sucsses($message)
{
    session_start();

    $_SESSION['alert_message_moza_sucsses'] = $message;
}

// نمایش پیام هشدار
function display_alert_message_sucsses()
{
    if (isset($_SESSION['alert_message_moza_sucsses'])) {
        echo '<div class="sucsee">' . $_SESSION['alert_message_moza_sucsses'] . '</div>';
        unset($_SESSION['alert_message_moza_sucsses']);
    }
}

function get_current_url()
{
    global $wp;
    return home_url(add_query_arg(array(), $wp->request));
}
function get_next_bid_id($product_id) {
    $current_bids = get_post_meta($product_id, 'bids', true);
    if (!$current_bids) {
        return 1;
    }
    $max_bid_id = 0;
    foreach ($current_bids as $bid) {
        if (isset($bid['id']) && $bid['id'] > $max_bid_id) {
            $max_bid_id = $bid['id'];
        }
    }
    return $max_bid_id + 1;
}
function get_next_bid_id_user($product_id) {
    $current_bids = get_post_meta($product_id, 'high_bids', true);
    if (!$current_bids) {
        return 1;
    }
    $max_bid_id = 0;
    foreach ($current_bids as $bid) {
        if (isset($bid['id']) && $bid['id'] > $max_bid_id) {
            $max_bid_id = $bid['id'];
        }
    }
    return $max_bid_id + 1;
}

function save_bid()
{


    if (isset($_POST['product_id']) && isset($_POST['bid_amount']) && isset($_POST['user_id'])) {
        $product_id = intval($_POST['product_id']);
        $bid_amount = sanitize_text_field($_POST['bid_amount']);
        $user_id = intval($_POST['user_id']);
        $metabox_product = get_post_meta($product_id, '', true);
        $current_bids_user = get_post_meta($product_id, "high_bids", true);
        $high_bids_user = findMaxBidAmount($current_bids_user);
        $start_price = isset($metabox_product['start_price'][0]) ? $metabox_product['start_price'][0] : 0;
        if ($bid_amount >= $start_price) {

            // Get current bids
            $current_bids = get_post_meta($product_id, 'bids', true);
            if (!$current_bids) {
                $current_bids = [];
            }
            if (!$current_bids) {
                $current_bids[] = [
                    'id'=>1,
                    'user_id' => $user_id,
                    'bid_amount' => $start_price,
                    'timestamp' => current_time('mysql') // Optional: add timestamp
                ];
                update_post_meta($product_id, 'bids', $current_bids);
                update_post_meta($product_id, 'price_now', $start_price);
                $current_bids[] = [
                    'id'=>1,
                    'user_id' => $user_id,
                    'bid_amount' => $bid_amount,
                    'timestamp' => current_time('mysql') // Optional: add timestamp
                ];
                update_post_meta($product_id, 'high_bids', $current_bids);

                // Update bids in post meta


                set_alert_message_sucsses("پیشنهاد شما با مفقیت ثبت شد.بالاترین پیشنهاد شما   " . number_format($bid_amount) . " تومان است ");
                wp_redirect(get_current_url());
            } else {
                $current_bids_user = get_post_meta($product_id, "high_bids", true);
                $current_bids = get_post_meta($product_id, "bids", true);
                $increas = get_option('custom_price_ranges_options', []);

                $high_bids_user = findMaxBidAmount($current_bids_user);
                $price_now = get_post_meta($product_id, 'price_now', true);
                if ($user_id == $high_bids_user['user_id']) {
                    if ($bid_amount > $high_bids_user['bid_amount']) {
                        $current_bids_high[] = [
                            'id'=>get_next_bid_id_user($product_id),
                            'user_id' => $user_id,
                            'bid_amount' => $bid_amount,
                            'timestamp' => current_time('mysql') // Optional: add timestamp
                        ];
                        update_post_meta($product_id, 'high_bids', $current_bids_high);
                        set_alert_message_sucsses("پیشنهاد شما با مفقیت افزایش یافت بالاترین پیشنهاد شما" . number_format($bid_amount) . " تومان است");
                        wp_redirect(get_current_url());
                    } else {
                        set_alert_message_denger("امکان کم کردن پیشنهاد وجود ندارد بالاترین پیشنها شما" . number_format($high_bids_user['bid_amount']) . " تومان است");
                        wp_redirect(get_current_url());

                    }
                } else {
                    for ($i = 1; $i <= 12; $i++) {
                        $low_key = "low{$i}";
                        $high_key = "high{$i}";
                        $increase_key = "increase{$i}";

                        if (!empty($increas[$low_key]) && !empty($increas[$high_key]) && !empty($increas[$increase_key])) {
                            $low = (int)$increas[$low_key];
                            $high = (int)$increas[$high_key];
                            $increase = (int)$increas[$increase_key];
                            if ($low <= $price_now && $price_now <= $high) {
                                $nex_bids = $price_now + $increase;

                                if ($bid_amount >= $nex_bids) {
                                    if ($bid_amount > $high_bids_user['bid_amount']) {
                                        if ($high_bids_user['bid_amount'] != $price_now) {
                                            $current_bids[] = [
                                                'id'=>get_next_bid_id($product_id),
                                                'user_id' => $high_bids_user['user_id'],
                                                'bid_amount' => $high_bids_user['bid_amount'],
                                                'timestamp' => current_time('mysql') // Optional: add timestamp
                                            ];
                                            update_post_meta($product_id, 'bids', $current_bids);
                                        }

                                        if ($bid_amount > $high_bids_user['bid_amount'] + $increase) {
                                            update_post_meta($product_id, 'price_now', $high_bids_user['bid_amount'] + $increase);

                                            $current_bids[] = [
                                                'id'=>get_next_bid_id($product_id),
                                                'user_id' => $user_id,
                                                'bid_amount' => $high_bids_user['bid_amount'] + $increase,
                                                'timestamp' => current_time('mysql') // Optional: add timestamp
                                            ];

                                            update_post_meta($product_id, 'bids', $current_bids);
                                            $current_bids[] = [
                                                'id'=>get_next_bid_id_user($product_id),
                                                'user_id' => $user_id,
                                                'bid_amount' => $bid_amount,
                                                'timestamp' => current_time('mysql') // Optional: add timestamp
                                            ];
                                            update_post_meta($product_id, 'high_bids', $current_bids);
                                           
                                            set_alert_message_sucsses("پیشنهاد شما با مفقیت ثبت شد.بالاترین پیشنهاد شما   " . number_format($bid_amount) . "تومان است ");
                                            wp_redirect(get_current_url());
            
                                        } else {
                                            $current_bids[] = [
                                                'id'=>get_next_bid_id_user($product_id),
                                                'user_id' => $user_id,
                                                'bid_amount' => $bid_amount,
                                                'timestamp' => current_time('mysql') // Optional: add timestamp
                                            ];
                                            update_post_meta($product_id, 'high_bids', $current_bids);
                                            $current_bids[] = [
                                                'id'=>get_next_bid_id($product_id),
                                                'user_id' => $user_id,
                                                'bid_amount' => $bid_amount,
                                                'timestamp' => current_time('mysql') // Optional: add timestamp
                                            ];
                                            update_post_meta($product_id, 'bids', $current_bids);
                                            update_post_meta($product_id, 'price_now', $bid_amount);
                                            set_alert_message_sucsses("پیشنهاد شما با مفقیت ثبت شد.بالاترین پیشنهاد شما   " . number_format($bid_amount) . "تومان است ");
                                            wp_redirect(get_current_url());
            
                                        }
                                    } else {
                                        if ($bid_amount == $high_bids_user['bid_amount']) {
                                            $current_bids[] = [
                                                'id'=>get_next_bid_id($product_id),
                                                'user_id' => $user_id,
                                                'bid_amount' => $bid_amount,
                                                'timestamp' => current_time('mysql') // Optional: add timestamp
                                            ];
                                            update_post_meta($product_id, 'bids', $current_bids);

                                            update_post_meta($product_id, 'price_now', $bid_amount);
                                            $current_bids[] = [
                                                'id'=>get_next_bid_id($product_id),
                                                'user_id' => $high_bids_user['user_id'],
                                                'bid_amount' => $bid_amount,
                                                'timestamp' => current_time('mysql') // Optional: add timestamp
                                            ];
                                            update_post_meta($product_id, 'bids', $current_bids);
                                            $current_bids[]= [
                                                'id'=>get_next_bid_id_user($product_id),
                                                'user_id' => $user_id,
                                                'bid_amount' => $bid_amount,
                                                'timestamp' => current_time('mysql') // Optional: add timestamp
                                            ];
                                            update_post_meta($product_id, 'high_bids', $current_bids);
                                            set_alert_message_denger("شرکت کننده دیگری قبلا همین پیشنهاد را وارد کرده است ، لطفا پیشنهاد خود را بالا ببرید");
                                            wp_redirect(get_current_url());
                                            exit();
                                        } else {
                                            if ($high_bids_user['bid_amount'] >= $bid_amount + $increase) {
                                                update_post_meta($product_id, 'price_now', $bid_amount + $increase);

                                                $current_bids[] = [
                                                    'id'=>get_next_bid_id($product_id),
                                                    'user_id' => $user_id,
                                                    'bid_amount' => $bid_amount,
                                                    'timestamp' => current_time('mysql') // Optional: add timestamp
                                                ];
                                                update_post_meta($product_id, 'bids',$current_bids );
                                                $current_bids[] = [
                                                    'id'=>get_next_bid_id($product_id),
                                                    'user_id' => $high_bids_user['user_id'],
                                                    'bid_amount' => $bid_amount + $increase,
                                                    'timestamp' => current_time('mysql') // Optional: add timestamp
                                                ];
            
                                                update_post_meta($product_id, 'bids', $current_bids);
                                                set_alert_message_denger("شرکت کننده دیگری قبلا پیشنهاد بالاتری از شما ثبت کرده است لطفا پیشنهاد خود را بالا ببرید");
                                                wp_redirect(get_current_url());
            
                                            } else {
                                                $current_bids[] = [
                                                    'id'=>get_next_bid_id($product_id),
                                                    'user_id' => $user_id,
                                                    'bid_amount' => $bid_amount,
                                                    'timestamp' => current_time('mysql') // Optional: add timestamp
                                                ];
                                                update_post_meta($product_id, 'bids', $current_bids);
                                                $current_bids[] = [
                                                    'id'=>get_next_bid_id($product_id),
                                                    'user_id' => $high_bids_user['user_id'],
                                                    'bid_amount' => $high_bids_user['bid_amount'],
                                                    'timestamp' => current_time('mysql') // Optional: add timestamp
                                                ];
                                                update_post_meta($product_id, 'bids', $current_bids);
                                                update_post_meta($product_id, 'price_now', $high_bids_user['bid_amount']);

                                                set_alert_message_denger("شرکت کننده دیگری قبلا پیشنهاد بالاتری از شما ثبت کرده است لطفا پیشنهاد خود را بالا ببرید");
                                                wp_redirect(get_current_url());
                                            }
                                        }
            
                                    }
            
                                } else {
                                    set_alert_message_denger("پیشنهاد شما ثبت نشد . باتوجه با گام افزایشی حداقل مبلغ پیشنهادی" . number_format($nex_bids) . "تومان است");
                                    wp_redirect(get_current_url());
            
                                }

                            }
                        }

                    }





                }


            }


        } else {

            set_alert_message_denger("پیشنهاد شما کمتر از قیمت پایه ای حراج است ، حداقل قیمت قابل قبول" . number_format($start_price) . "تومان است");
            wp_redirect(get_current_url());

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

        $types['auction_product'] = 'مزایده';

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
    $next_date = '+' . $select_date_end . ' days';
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
        $next_date = '+' . $select_date_end . ' days';
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

function jalali_to_gregorian_with_time($jalali_datetime)
{
    // جدا کردن تاریخ و زمان
    list($jalali_date, $jalali_time) = explode('-', $jalali_datetime);
    list($year, $month, $day) = explode('/', $jalali_date);
    list($hour, $minute) = explode(':', $jalali_time);
    $second = 0;
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

function update_start_price_callback()
{
    if (!isset($_GET['product_id'])) {
        wp_send_json_error();
    }

    $product_id = intval($_GET['product_id']);
    $metabox_product = get_post_meta($product_id, '', true);

    if (isset($metabox_product['price_now'][0])) {
        $start_price = number_format($metabox_product['price_now'][0]);
        wp_send_json_success(['price_now' => $start_price]);
    } else {
        wp_send_json_error();
    }
}
function prevent_seller_editing_published_products() {
    if ( is_user_logged_in() && dokan_is_seller_dashboard() && isset($_GET['product_id']) ) {
        $product_id = intval($_GET['product_id']);
        $bids = get_post_meta( $product_id,'bids',true);

        
            // Check if the current user is the seller of this product
            if ( $bids && count( $bids ) > 0) {
                // اگر کاربر جاری نویسنده محصول نیست
                wp_die( __('به دلیل داشتن پیشنهاد در محصول شما اجازه ویرایش محصول را ندارید', 'your-text-domain') );
            }
        
    }
}


function register_custom_order_status_sent_moza() {
    register_post_status('wc-sent', array(
        'label'                     => 'ارسال',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('ارسال (%s)', 'ارسال (%s)')
    ));
}
function add_custom_order_statuses_sent_moza($order_statuses) {
    $new_order_statuses = array();

    // وضعیت جدید را به لیست وضعیت‌ها اضافه کنید
    foreach ($order_statuses as $key => $status) {
        $new_order_statuses[$key] = $status;
        if ('wc-processing' === $key) {
            $new_order_statuses['wc-sent'] = 'ارسال';
        }
    }

    return $new_order_statuses;
}

function add_castume_menu_to_dokan_moza($query_var){
    $query_var['order_moza']='order_moza';
    return $query_var;
}

function add_oreder_moza_to_dokan($url){
    $url['order_moza']=[
    'title'=> 'سفارش ها',
    'icon'  => '<i class="fa fa-shopping-cart"></i>',
    'url'   => dokan_get_navigation_url( 'order_moza' ),
    'pos'   => 3
];
return $url;
}

function dokan_load_template_order_moza($query_vars){
    if ( isset( $query_vars['order_moza'] ) ) {
        $args['seller_id'] = get_current_user_id();
        $user_orders = dokan()->order->all( $args );
        $data = ['user_orders'=>$user_orders];
        my_plugin_get_template('order_moza.php', $data);
       }
}

function dokan_get_order_status_translated_moza( $status ) {
    $translated_order_status = '';
    switch ( $status ) {
        case 'completed':
        case 'wc-completed':
            $translated_order_status = __( 'Completed', 'dokan-lite' );
            break;

        case 'pending':
        case 'wc-pending':
            $translated_order_status = __( 'Pending Payment', 'dokan-lite' );
            break;

        case 'on-hold':
        case 'wc-on-hold':
            $translated_order_status = __( 'On-hold', 'dokan-lite' );
            break;

        case 'processing':
        case 'wc-processing':
            $translated_order_status = __( 'Processing', 'dokan-lite' );
            break;

        case 'refunded':
        case 'wc-refunded':
            $translated_order_status = __( 'Refunded', 'dokan-lite' );
            break;

        case 'cancelled':
        case 'wc-cancelled':
            $translated_order_status = __( 'Cancelled', 'dokan-lite' );
            break;

        case 'failed':
        case 'wc-failed':
            $translated_order_status = __( 'Failed', 'dokan-lite' );
            break;

        case 'checkout-draft':
            $translated_order_status = __( 'Draft', 'dokan-lite' );
            break;
        case 'sent':
            $translated_order_status = __( 'ارسال', 'dokan-lite' );
            break;
    }

    return apply_filters( 'dokan_get_order_status_translated', $translated_order_status, $status );
}


function dokan_get_order_status_class_moza( $status ) {
    $order_status_class = '';
    switch ( $status ) {
        case 'completed':
        case 'wc-completed':
            $order_status_class = 'success';
            break;

        case 'pending':
        case 'wc-pending':
        case 'failed':
        case 'wc-failed':
            $order_status_class = 'danger';
            break;

        case 'on-hold':
        case 'wc-on-hold':
            $order_status_class = 'warning';
            break;

        case 'processing':
        case 'sent':
        case 'wc-processing':
            $order_status_class = 'info';
            break;
        case 'refunded':
        case 'wc-refunded':
        case 'cancelled':
        case 'wc-cancelled':
        case 'checkout-draft':
            $order_status_class = 'default';
            break;
    }

    return apply_filters( 'dokan_get_order_status_class', $order_status_class, $status );
}

function dokan_order_listing_status_filter_moza() {
    $status_class  = 'all';
    $orders_url    = dokan_get_navigation_url( 'order_moza' );
    $orders_counts = dokan_count_orders( dokan_get_current_user_id() );
    $total_orders  = $orders_counts->total ?? 0;
    $filter_nonce  = wp_create_nonce( 'seller-order-filter-nonce' );

    if ( isset( $_GET['seller_order_filter_nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['seller_order_filter_nonce'] ) ), 'seller-order-filter-nonce' ) ) {
        $status_class = isset( $_GET['order_status'] ) ? sanitize_text_field( wp_unslash( $_GET['order_status'] ) ) : $status_class;
    }
    

    /**
     * Filter the list of order statuses to exclude.
     *
     * This filter allows developers to modify the array of order statuses that
     * should be excluded from the displayed list. It is useful for removing
     * statuses dynamically based on specific conditions or configurations.
     *
     * @since 3.10.4
     *
     * @param array $exclude_statuses Array of order status slugs to be excluded.
     */
    $exclude_statuses = (array) apply_filters( 'dokan_vendor_dashboard_excluded_order_statuses', [ 'wc-checkout-draft' ] );

    // Convert the indexed array to an associative array where the values become keys & Get WooCommerce order statuses.
    $exclude_statuses  = array_flip( $exclude_statuses );
    $wc_order_statuses = wc_get_order_statuses();

    // Remove keys from $wc_order_statuses that are found in $exclude_statuses.
    $filtered_statuses = array_diff_key( $wc_order_statuses, $exclude_statuses );

    // Directly prepend the custom 'All' status to the WooCommerce order statuses.
    $order_statuses = array_merge( [ 'all' => 'All' ], $filtered_statuses );

    /**
     * Determine the order listing statuses on the Dokan dashboard.
     *
     * This hook allows developers to modify or extend the list of order statuses
     * used in the order listing on the Dokan vendor dashboard. It can be used to
     * add new statuses or modify existing ones to customize the dashboard functionality.
     *
     * @since 3.10.4
     *
     * @param array $order_statuses Array of order statuses with all. Key is the status slug, and value is the display label.
     */
    $order_statuses = apply_filters( 'dokan_vendor_dashboard_order_listing_statuses', $order_statuses );
    ?>
    <ul class='list-inline order-statuses-filter subsubsub'>
        <?php foreach ( $order_statuses as $status_key => $status_label ) : ?>
            <?php
            $url_args = array(
                'order_status'              => $status_key,
                'seller_order_filter_nonce' => $filter_nonce,
            );

            // Get filtered orders url based on order status.
            $status_url = add_query_arg( $url_args, $orders_url );
            ?>
            <li <?php echo $status_class === $status_key ? 'class="active"' : ''; ?>>
                <a href="<?php echo esc_url( $status_url ); ?>">
                    <?php
                    // Set formatted orders count data based on status.
                    $status_order_count    = $orders_counts->{$status_key} ?? 0;
                    $formatted_order_count = $status_key === 'all' ? number_format_i18n( $total_orders ) : number_format_i18n( $status_order_count );

                    /* translators: 1: Order status label 2: Order count */
                    printf( esc_html__( '%1$s (%2$s)', 'dokan-lite' ), $status_label, $formatted_order_count );
                    ?>
                </a>
            </li>
        <?php endforeach; ?>

        <?php do_action( 'dokan_status_listing_item', $orders_counts ); ?>
    </ul>
    <?php
}
function add_count_to_moza($count){
    $count['wc-sent']=0;
    return $count;
}
function add_my_account_my_orders_custom_action( $actions, $order ) {
    if ($order->has_status('sent')){
        $action_slug = 'specific_name';

        $actions[$action_slug] = array(
            'url'  => wp_nonce_url( add_query_arg( 'confirm_received', $order->get_id(), home_url('/my-account/') ), 'woocommerce-mark-order-received' ),
            'name' => 'تایید دریافت محصول',
        );
    }

    return $actions;
}
function handle_confirm_received_action() {
    if ( isset( $_GET['confirm_received'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'woocommerce-mark-order-received' ) ) {
        $order_id = intval( $_GET['confirm_received'] );
        $order = wc_get_order( $order_id );

        if ( $order && $order->get_user_id() === get_current_user_id() ) {
            $order->update_status( 'completed', __( 'محصول توسط مشتری تایید شد.', 'woocommerce' ) );
            wc_add_notice( __( 'سفارش با موفقیت تکمیل شد.', 'woocommerce' ) );
            wp_safe_redirect( wc_get_account_endpoint_url( 'orders' ) );
            exit;
        }
    }
}

function remove_wc_prefix_from_text($text) {
    // استفاده از الگوی منظم برای پیدا کردن و حذف "wc-"
    $pattern = '/\bwc-([a-zA-Z0-9_]+)/';
    $replacement = '$1';

    // جایگزینی "wc-" با استفاده از الگوی منظم
    $result = preg_replace($pattern, $replacement, $text);

    return $result;
}