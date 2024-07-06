<?php
if (isset($_GET["save_bid"]) && $_GET['save_bid'] == "duplicate") {
    echo "این پیشنهاد از قبل وارد شد لطفا پیشنهاد بزرگتر ارسال کنید";
} elseif (isset($_GET["save_bid"]) && $_GET['save_bid'] == "ok") {

    echo "پیشنهاد با مفقیت ذخیزه شد";
}

save_bid();
$metabox_product = get_post_meta($product_id, '', true);

if (isset($metabox_product['start_time_moza'][0]) && isset($metabox_product['end_time_moza'][0])):
    $start_date_shamsi = $metabox_product['start_time_moza'][0]; // تاریخ شروع از متاباکس
    $end_date_shamsi = $metabox_product['end_time_moza'][0]; // تاریخ پایان از متاباکس


    $start_gregorian_date = jalali_to_gregorian_with_time($start_date_shamsi);


    $end_gregorian_date = jalali_to_gregorian_with_time($end_date_shamsi);
    ?>
    <div id="price_moza">s</div>
    <div class="time_moza">
        <i class="fa-solid fa-calendar-days"></i>تاریخ شروع مزایده: <span id="start_countdown"></span><br>
        <i class="fa-solid fa-calendar-days"></i>تاریخ پایان مزایده: <span id="end_countdown"></span>
    </div>

    <?php if (isset($metabox_product['start_price'][0])): ?>
    <div class="price_moza">
        قیمت شروع معامله : <span id="start_price"> <?php echo number_format($metabox_product['start_price'][0]); ?> تومان </span>
    </div>
<?php endif;
    $id_user_create_post = get_product_author_id($product_id);

    ?>

    <?php if ($metabox_product['option_sell'][0] == "aution" && $end_gregorian_date > date("Y-m-d")): ?>
    <?php if (is_user_logged_in()): ?>
        <?php if ($id_user_create_post != get_current_user_id()): ?>
            <form id="bid_form" action="" method="post" style="display: none;">
                <input type="hidden" name="product_id" value="<?php echo $product_id ?>">
                <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">
                <input type="number" name="bid_amount" placeholder="پیشنهاد خود را وارد کنید" id="bid_amount">
                <button type="submit" onclick="confirmBid()">ارسال پیشنهاد</button>
            </form>
        <?php else: ?>
            <p>شما سازنده مزایده هستید نمیتوانید پیشنهاد ارسال کنید</p>
        <?php endif; ?>
    <?php else: ?>
        <p>لطفا برای ارسال پیشنهاد مزایده ورود کنید</p>
    <?php endif; ?>
<?php else:
    $bids = get_post_meta($product_id, "bids", true);
    if ($bids) {
        $bids_user = get_post_meta($product_id, 'bids_user', true);
        $top_bids = findMaxBidAmount($bids_user);
        if (get_current_user_id() == $top_bids['user_id']) {
            ?>
            <p>هورااا شما مزایدرو بردید</p>
            <p>پشنهاد شما:<?php echo $top_bids['bid_amount'] ?></p>
            <button id="auction-pay">پرداخت مزایده</button>
            <?php
        } else {
            echo "متسفانه از شما پینهاد بالاتر وجود داشت شما مزایدرو باختید";
        }
    } else {
        echo "متاسفانه مزایده شما بدون پیشنهادتمام شد";
    }


    ?>

<?php endif; ?>
<?php endif; ?>
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
            document.getElementById("start_countdown").innerHTML = startDays + " روز " + startHours + " ساعت "
                + startMinutes + " دقیقه " + startSeconds + " ثانیه ";

            document.getElementById("end_countdown").innerHTML = "";
            if (bidForm){
                bidForm.style.display = "none";
            }

        } else if (endDistance > 0) {
            // محاسبه زمان باقی مانده تا پایان مزایده
            const endDays = Math.floor(endDistance / (1000 * 60 * 60 * 24));
            const endHours = Math.floor((endDistance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const endMinutes = Math.floor((endDistance % (1000 * 60 * 60)) / (1000 * 60));
            const endSeconds = Math.floor((endDistance % (1000 * 60)) / 1000);

            // نمایش نتیجه در عنصر با id="end_countdown"
            document.getElementById("end_countdown").innerHTML = endDays + " روز " + endHours + " ساعت "
                + endMinutes + " دقیقه " + endSeconds + " ثانیه ";

            document.getElementById("start_countdown").innerHTML = "مزایده آغاز شده است";
            if (bidForm){
                bidForm.style.display = "block";

            }
        } else {
            clearInterval(x);
            document.getElementById("start_countdown").innerHTML = "مزایده به پایان رسیده است";
            document.getElementById("end_countdown").innerHTML = "";
            if (bidForm){
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
            var bidAmount = <?php  if (isset($top_bids['bid_amount'])) {
                echo $top_bids['bid_amount'];
            } else {
                echo 0;
            }; ?>;
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
                    document.getElementById("start_price").innerText = response.data.start_price + " تومان";
                }
            }
        });
    }

    // اجرای تابع بلافاصله و سپس هر 30 ثانیه یکبار
    updatePrice();
    setInterval(updatePrice, 3000);

</script>
