<div class="fom_group_data_moza" id="fom_group_data_moza">
    <div class="form_create_moza">

        <label for="qualety">کیفیت ایتم</label>
        <select name="qualety" id="qualety">
            <option value="lat">لت</option>
            <option value="level1">دست دوم</option>
            <option value="level2">دست سوم</option>
            <option value="level2">دست پهارم</option>
        </select>
        <label for="option_sell">نوع حراج</label>
        <select name="option_sell" id="option_sell">
            <option value="aution">حراج</option>
            <option value="by_only_one">هم اکنون خرید کنید </option>
        </select>
        <div class="aution">
            <label for="start_price">شروع قیمت</label>
            <input type="number" placeholder="قیمت شروع معامله تومان" name="start_price" id="start_price">
            <label for="set_price">قیمت رزور</label>
            <input type="number" name="set_price" placeholder="قیمت رزور ایتم تومان" id="set_price">
        </div>
        <label for="price">قیمت خرید فوری</label>
        <input type="number" name="price" id="price" placeholder="قیمت خرید فوری ایتم تومان">
        <label for="price_sent">قیمت ارسال</label>
        <input type="number" name="price_sent" id="price_sent" placeholder="هزینه ارسال ایتم تومان">
        <label for="time_post">مدت زمان</label>
        <select name="time" id="time">
            <option value="1">۱ روز</option>
            <option value="3">3 روز</option>
            <option value="5">5 روز</option>
        </select>
        <label for="post_item__end_date">ثبت مجدد ایتم در صورت عدم فروش</label>
        <select name="post_item_end_date" id="post_item_end_date">
            <option value="1">بک بار</option>
            <option value="2">دو بار</option>
            <option value="3">سه بار</option>
        </select>
        <div class="sent_request_button">
            <label for="sent_request">ارسال پیشنهاد</label>
            <input type="checkbox" name="cheack_snet_request" id="cheack_snet_request" class="toggle-button">
        </div>

        <div class="sent_request" id="sent_request">
            <label for="reject_request">رد درخواست کمتر از </label>
            <input type="number" name="reject_request" id="reject_request" placeholder="رد کردن درخواست کمتر از  تومان">
            <label for="acsept_requset">پذیرش اتومات درخواست بالاتر از</label>
            <input type="number" name="acsept_requset" id="acsept_requset" placeholder="پذیرش در خواست بالاتر از تومان">
        </div>


    </div>
</div><!-- .dashboard-content-area -->