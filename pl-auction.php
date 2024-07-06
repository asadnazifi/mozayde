<?php
/**
 * Plugin Name: اضافه کردن مزایده
 * Description: افزونه سفارشی برای اضافه کردن قسمت مزایده به افزونه دکان و بهر مندی از امکانت زیاد برای مزایده میباشد.
 * Version: 1.0
 * Author: اسعد نظیفی
 * Text Domain: MOza_plugin
 */

// جلوگیری از دسترسی مستقیم
if ( !defined( 'ABSPATH' ) ) exit;

// تعریف مسیرهای ثابت
define( 'MY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// فراخوانی فایل‌های CSS و JS
function my_plugin_enqueue_scripts() {
    wp_enqueue_style( 'my-plugin-style', MY_PLUGIN_URL . 'assets/css/style.css' );
    wp_enqueue_style( 'persian-datepicker.min', MY_PLUGIN_URL . 'assets/css/persian-datepicker.min.css' );
    wp_enqueue_script( 'my-plugin-script', MY_PLUGIN_URL . 'assets/js/script.js', array('jquery'), null, true );
    wp_enqueue_script( 'persian-datemin', MY_PLUGIN_URL . 'assets/js/persian-date.min.js' );
    wp_enqueue_script( 'jqery', MY_PLUGIN_URL . 'assets/js/jqer.js' );
    wp_enqueue_script( 'persian-datepicker.min', MY_PLUGIN_URL . 'assets/js/persian-datepicker.min.js' );
}
add_action( 'wp_enqueue_scripts', 'my_plugin_enqueue_scripts' );

// فراخوانی فایل‌های مدیریت
function my_plugin_admin_enqueue_scripts() {
    wp_enqueue_style( 'my-plugin-admin-style', MY_PLUGIN_URL . 'assets/css/admin-style.css' );
    wp_enqueue_script( 'my-plugin-script', MY_PLUGIN_URL . 'assets/js/script.js', array('jquery'), null, true );

    wp_enqueue_style( 'persian-datepicker.min', MY_PLUGIN_URL . 'assets/css/persian-datepicker.min.css' );
    wp_enqueue_script( 'my-plugin-admin-script', MY_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), null, true );
    wp_enqueue_script( 'persian-datemin', MY_PLUGIN_URL . 'assets/js/persian-date.min.js');
    wp_enqueue_script( 'jqer', MY_PLUGIN_URL . 'assets/js/jqer.js');
    wp_enqueue_script( 'persian-datepickermin', MY_PLUGIN_URL . 'assets/js/persian-datepicker.min.js' );
}
add_action( 'admin_enqueue_scripts', 'my_plugin_admin_enqueue_scripts' );

// فراخوانی توابع سفارشی
foreach (glob(MY_PLUGIN_DIR . "functions/*.php") as $file) {
    include_once $file;
}

// فراخوانی فایل‌های مدیریت
foreach (glob(MY_PLUGIN_DIR . "admin/*.php") as $file) {
    include_once $file;
}

// فراخوانی قالب‌ها
function my_plugin_get_template( $template_name, $data = array() ) {
    $template_path = MY_PLUGIN_DIR . 'templates/' . $template_name;

    // Check if the template file exists
    if ( file_exists( $template_path ) ) {
        // Extract the data to variables if any data is passed
        if ( !empty( $data ) && is_array( $data ) ) {
            extract( $data );
        }

        // Include the template file
        include $template_path;
    } else {
        echo 'قالب پیدا نشد.';
    }
}







/*
* Showing field data on product edit page
*/




// showing on single product page




