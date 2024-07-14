
<div class="box_single_product_moza">
<?php
session_start();

display_alert_message_denger(); ?>
<?php echo display_alert_message_sucsses(); ?>
<?php
date_default_timezone_set('Asia/Tehran');
save_bid();
$bids_user = get_post_meta($product_id, 'high_bids', true);
$top_bids = findMaxBidAmount($bids_user);
$bids_product = get_post_meta($product_id,'bids',true);
$top_bid_product =findMaxBidAmount($bids_product);
$price = get_post_meta($product_id,"price_now", true);
$metabox_product = get_post_meta($product_id, '', true);
$set_price = $metabox_product['set_price'][0];
echo get_current_user_id();
if (isset($set_price) && $set_price!==null && $set_price>$price){
    echo "<div class='denger'>قیمت این کالا به قیمت رزرو نرسیده و فروشنده میتواند این کالارا ارسال نکند</div>";
}
if (isset($metabox_product['start_time_moza'][0]) && isset($metabox_product['end_time_moza'][0])):
    if (isset($metabox_product['start_price'][0])): ?>
        <div class="price_moza">
            <span id="start_price"> <?php 
            if(isset($price) && $price>0){
                echo number_format($price).'تومان';
            }else{
                echo number_format($metabox_product['start_price'][0]).'تومان';
            }
            
            ?> 
            </span>
        </div>
        <div class="time_moza_start"><span id="start_countdown" class=""></span></div>
        <div class="alert text-center alert-warning end_time_moza">
            <span id="end_countdown" class=''></span>
        </div>
    <?php endif;

    $start_date_shamsi = $metabox_product['start_time_moza'][0]; // تاریخ شروع از متاباکس
    $end_date_shamsi = $metabox_product['end_time_moza'][0]; // تاریخ پایان از متاباکس

    $price_now = get_post_meta($product_id, 'price_now', true);
    $start_gregorian_date = jalali_to_gregorian_with_time($start_date_shamsi);


    $end_gregorian_date = jalali_to_gregorian_with_time($end_date_shamsi);
    if (isset($top_bid_product)&&$top_bid_product['user_id'] ==get_current_user_id()):?>

    <div class="alert alert-success text-center">
        شما بالاترین پیشنهاد هستید با:  <?php echo number_format($top_bid_product['bid_amount'])?>  تومان
    </div>
    <?php elseif(findMaxBidAmount($bids_product,get_current_user_id())):?>
    <div class="alert alert-denger text-center">
        شما دیگر بالاترین پیشنهاد دهنده این مزایده نیستید، برای برنده شدن لطفا پیشنهاد خود را بالا ببرید
    </div>
    <?php endif;?>
    <?php
    $id_user_create_post = get_product_author_id($product_id);
    if ($metabox_product['option_sell'][0] == "aution" && $end_gregorian_date > date("Y-m-d H:m:s")): ?>

        <?php if (is_user_logged_in()): ?>
            <?php if ($id_user_create_post != get_current_user_id()): ?>
                <form id="bid_form" action="" method="post" style="display: none;">
                    <input type="hidden" name="product_id" value="<?php echo $product_id ?>">
                    <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">
                    <input type="number" name="bid_amount" placeholder="حداکثر پیشنهاد شما" id="bid_amount">
                    <button type="submit" onclick="confirmBid()" class="submit_form_bids">ارسال پیشنهاد</button>
                </form>
                <div class="cunt_bids text-center m-t-10">
                    <a href="#" id="show_hide_list_moza"><?php echo (is_array($bids_product))?count($bids_product):0;?> پیشنهاد تا کنون - تاریخچه را ببینید</a>
                </div>
            <?php else: ?>
                <p class='denger'>شما سازنده مزایده هستید نمیتوانید پیشنهاد ارسال کنید</p>
            <?php endif; ?>
        <?php else: ?>
            <p class="denger">لطفا برای ارسال پیشنهاد مزایده ورود کنید</p>
        <?php endif; ?>
        <?php else:
    $bids = get_post_meta($product_id, "bids", true);
    if ($bids) {
    $bids_user = get_post_meta($product_id, 'high_bids', true);
    $top_bids = findMaxBidAmount($bids_user);
    if (get_current_user_id() == $top_bids['user_id']) {
    ?>
    <button id="auction-pay" class="submit_form_bids">تبریک فراوان شما برنده شدید جهت پرداخت کلیک کنید</button>
    <?php
} else {
        ?>
        <div class="denger">
            متسفانه از شما پیشنهاد بالاتر وجود داشت شما مزایدرو باختید
        </div>
        <?php
    }
}else{
        ?>
        <div class="denger">
            متاسفانه مزایده شما بدون پیشنهادتمام شد </div>
        <?php
    }endif; ?>
<?php endif; ?>
</div>
<?php
$bid_top_user_in_bid =findMaxBidAmount($bids_user,get_current_user_id()); ?>
<?php if (isset($bid_top_user_in_bid)):?>
<div class="alert alert-warning text-center m-t-10">
    بالاترین پیشنهاد شما  <?php echo number_format($bid_top_user_in_bid['bid_amount']);?> تومان است
</div>

<?php endif;?>
<div class="box_moza_list" id="box_moza_list">
    <?php if ($bids_product && is_array($bids_product)) {
        // مرتب‌سازی پیشنهادات از جدید به قدیم
        usort($bids_product, function ($a, $b) {
            return (int)$b['id'] - (int)$a['id'];
        });
        $number_row = 1;
        echo '<table>';
        echo '<tr><th>ردیف</th><th>کاربر</th><th>مبلغ پیشنهاد</th><th>زمان ارسال</th></tr>';
        foreach ($bids_product as $bid) {
            $user_info = get_userdata($bid['user_id']);

            $gregorianDate  = $bid['timestamp'];
            // استخراج تاریخ و زمان از رشته
            list($date, $time) = explode(' ', $gregorianDate);
            list($gy, $gm, $gd) = explode('-', $date);
            list($hour, $minute, $second) = explode(':', $time);

            // تبدیل تاریخ میلادی به شمسی
            list($jy, $jm, $jd) = gregorian_to_jalali($gy, $gm, $gd);

            // ترکیب تاریخ و زمان شمسی
            $jalaliDateWithTime = "$jy/$jm/$jd $hour:$minute:$second";
            $user_name = $user_info ? $user_info->user_login : 'کاربر ناشناس';
            echo '<tr>';
            echo '<td>' . esc_html($number_row) . '</td>';
            echo '<td>' . obfuscateString(esc_html($user_name)) . '</td>';
            echo '<td>' . number_format(esc_html($bid['bid_amount'])) . ' تومان</td>';
            echo '<td>' . esc_html($jalaliDateWithTime) . '</td>';
            echo '</tr>';
            $number_row +=1;
            
        }
        echo '</table>';
    } else {
        echo '<p>هیچ پیشنهادی ارسال نشده است.</p>';
    }?>
    <button id="close_bid">بستن</button>
</div>

<script>
    // تاریخ و زمان شروع و پایان مزایده را از PHP دریافت کنید
    const startAuctionDate = new Date("<?php echo $start_gregorian_date; ?>").getTime();
    const endAuctionDate = new Date("<?php echo $end_gregorian_date; ?>").getTime();

    // به روز رسانی تایمر هر ثانیه
    const x = setInterval(function () {

        // تاریخ و زمان فعلی
        const now = new Date().getTime();

        // تفاوت بین حالا و تاریخ شروع
        const startDistance = startAuctionDate - now;

        // تفاوت بین حالا و تاریخ پایان
        const endDistance = endAuctionDate - now;

        const bidForm = document.getElementById("bid_form");

        if (startDistance > 0) {
            // محاسبه زمان باقی مانده تا شروع مزایده
            const startDays = Math.floor(startDistance / (1000 * 60 * 60 * 24));
            const startHours = Math.floor((startDistance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const startMinutes = Math.floor((startDistance % (1000 * 60 * 60)) / (1000 * 60));
            const startSeconds = Math.floor((startDistance % (1000 * 60)) / 1000);

            // نمایش نتیجه در عنصر با id="start_countdown"
            document.getElementById("start_countdown").innerHTML = startDays + "d " + startHours + "D"
                + startMinutes + ":" + startSeconds;

            document.getElementById("end_countdown").innerHTML = "";
            if (bidForm) {
                bidForm.style.display = "none";
            }

        } else if (endDistance > 0) {
            // محاسبه زمان باقی مانده تا پایان مزایده
            const endDays = Math.floor(endDistance / (1000 * 60 * 60 * 24));
            const endHours = Math.floor((endDistance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const endMinutes = Math.floor((endDistance % (1000 * 60 * 60)) / (1000 * 60));
            const endSeconds = Math.floor((endDistance % (1000 * 60)) / 1000);

            // نمایش نتیجه در عنصر با id="end_countdown"
            document.getElementById("end_countdown").innerHTML = endDays + "d " + endHours + ":"
                + endMinutes + ":" + endSeconds;

            if (bidForm) {
                bidForm.style.display = "block";

            }
        } else {
            clearInterval(x);
            document.getElementById("start_countdown").innerHTML = "";
            document.getElementById("end_countdown").innerHTML = "مزایده به پایان رسیده است";
            if (bidForm) {
                bidForm.style.display = "none";

            }
        }

    }, 1000);


    function confirmBid() {
        document.getElementById('bid_form').addEventListener('submit', function (event) {
            event.preventDefault(); // جلوگیری از ارسال فرم به صورت پیش‌فرض
        });
        const bidAmount = document.getElementById("bid_amount").value;
        if (bidAmount) {
            const formattedBidAmount = new Intl.NumberFormat().format(bidAmount);
            const confirmation = confirm("آیا مبلغ شما " + formattedBidAmount + "  تومان است؟ آیا می‌خواهید ارسال کنید؟");
            if (confirmation) {
                document.getElementById("bid_form").submit();
            } else {
                alert("مبلغ خود را اصلاح کنید.");
            }
        } else {
            alert("لطفا مبلغی را وارد کنید.");
        }
    }
    jQuery(document).ready(function ($) {
        $('#auction-pay').on('click', function () {
            var bidAmount = <?php if (isset($price_now)&& $price_now>0) {
                echo $price_now;
            } else {
                echo 0;
            }
            ; ?>;
            var productId = <?php echo $product_id; ?>;

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'add_auction_to_cart',
                    product_id: productId,
                    bid_amount: bidAmount
                },
                success: function (response) {
                    if (response.success) {

                        window.location.href = '<?php echo wc_get_cart_url(); ?>';
                    } else {
                        console.log(response)
                    }
                }
            });
        });
    });

    function updatePrice() {
        var productId = <?php echo $product_id; ?>;
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'GET',
            data: {
                action: 'update_start_price',
                product_id: productId
            },
            success: function (response) {
                if (response.success) {
                    if(response.data.price_now!== null){
                        document.getElementById("start_price").innerText = response.data.price_now + " تومان";

                    }
                }
            }
        });
    }

    // اجرای تابع بلافاصله و سپس هر 30 ثانیه یکبار
    updatePrice();
    setInterval(updatePrice, 3000);

</script>