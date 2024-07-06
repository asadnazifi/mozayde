<?php  save_custom_price_ranges() ?>
<div class="wrap">
    <h1>Custom Price Ranges Form</h1>

    <form method="post" action="">
        <?php wp_nonce_field('custom_price_ranges_nonce', 'custom_price_ranges_nonce'); ?>

        <table border="1">
            <tr>
                <th>Low Value</th>
                <th>High Value</th>
                <th>Increase Value</th>
            </tr>
            <tr>
                <td><input type="number" name="low1" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['low1']); ?>" required> تومان</td>
                <td><input type="number" name="high1" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['high1']); ?>" required> تومان</td>
                <td><input type="number" name="increase1" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['increase1']); ?>" required> تومان</td>
            </tr>
            
            <tr>
                <td><input type="number" name="low2" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['low2']); ?>" required> تومان</td>
                <td><input type="number" name="high2" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['high2']); ?>" required> تومان</td>
                <td><input type="number" name="increase2" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['increase2']); ?>" required> تومان</td>
            </tr>
            
            <tr>
                <td><input type="number" name="low3" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['low3']); ?>" required> تومان</td>
                <td><input type="number" name="high3" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['high3']); ?>" required> تومان</td>
                <td><input type="number" name="increase3" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['increase3']); ?>" required> تومان</td>
            </tr>
            
            <tr>
                <td><input type="number" name="low4" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['low4']); ?>" required> تومان</td>
                <td><input type="number" name="high4" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['high4']); ?>" required> تومان</td>
                <td><input type="number" name="increase4" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['increase4']); ?>" required> تومان</td>
            </tr>
            
            <tr>
                <td><input type="number" name="low5" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['low5']); ?>" required> تومان</td>
                <td><input type="number" name="high5" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['high5']); ?>" required> تومان</td>
                <td><input type="number" name="increase5" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['increase5']); ?>" required> تومان</td>
            </tr>
            
            <tr>
                <td><input type="number" name="low6" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['low6']); ?>" required> تومان</td>
                <td><input type="number" name="high6" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['high6']); ?>" required> تومان</td>
                <td><input type="number" name="increase6" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['increase6']); ?>" required> تومان</td>
            </tr>
            
            <tr>
                <td><input type="number" name="low7" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['low7']); ?>" required> تومان</td>
                <td><input type="number" name="high7" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['high7']); ?>" required> تومان</td>
                <td><input type="number" name="increase7" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['increase7']); ?>" required> تومان</td>
            </tr>
            
            <tr>
                <td><input type="number" name="low8" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['low8']); ?>" required> تومان</td>
                <td><input type="number" name="high8" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['high8']); ?>" required> تومان</td>
                <td><input type="number" name="increase8" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['increase8']); ?>" required> تومان</td>
            </tr>
            
            <tr>
                <td><input type="number" name="low9" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['low9']); ?>" required> تومان</td>
                <td><input type="number" name="high9" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['high9']); ?>" required> تومان</td>
                <td><input type="number" name="increase9" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['increase9']); ?>" required> تومان</td>
            </tr>
            
            <tr>
                <td><input type="number" name="low10" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['low10']); ?>" required> تومان</td>
                <td><input type="number" name="high10" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['high10']); ?>" required> تومان</td>
                <td><input type="number" name="increase10" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['increase10']); ?>" required> تومان</td>
            </tr>
            <tr>
                <td><input type="number" name="low11" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['low11']); ?>" required> تومان</td>
                <td><input type="number" name="high11" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['high11']); ?>" required> تومان</td>
                <td><input type="number" name="increase11" value="<?php echo esc_attr(get_option('custom_price_ranges_options')['increase11']); ?>" required> تومان</td>
            </tr>
            
        </table>
        <input type="submit" name="submit_custom_price_ranges" id="submit" class="button button-primary" value="ذخیره تغییرات">
    </form>
</div>
