<h2>پیشنهاد های ارسال شده</h2>
<?php
$bids = get_post_meta(get_the_ID(), 'bids', true);
if ($bids && is_array($bids)) {
    // مرتب‌سازی پیشنهادات از جدید به قدیم
    usort($bids, function ($a, $b) {
        return (int)$b['bid_amount'] - (int)$a['bid_amount'];
    });

    echo '<table>';
    echo '<tr><th>ردیف</th><th>کاربر</th><th>مبلغ پیشنهاد</th><th>زمان ارسال</th></tr>';
    $row_number = 1;
    foreach ($bids as $bid) {
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
        echo '<td>' . esc_html($row_number) . '</td>';
        echo '<td>' . obfuscateString(esc_html($user_name)) . '</td>';
        echo '<td>' . number_format(esc_html($bid['bid_amount'])) . ' تومان</td>';
        echo '<td>' . esc_html($jalaliDateWithTime) . '</td>';
        echo '</tr>';
        $row_number++;
    }
    echo '</table>';
} else {
    echo '<p>هیچ پیشنهادی ارسال نشده است.</p>';
}
?>