<?php

add_action('admin_menu', 'menu_mza_in_price');

add_action('woocommerce_single_product_summary','show_product_code',10);
add_filter( 'woocommerce_product_tabs', 'add_bils_moza_to_wo', 9999 );
add_action('admin_post_save_custom_price_ranges', 'save_custom_price_ranges');
add_action( 'init', 'create_quality_taxonomy' );

add_action('init', 'register_auction_product_type');
add_filter('product_type_selector', 'add_auction_product_type');
add_filter('dokan_product_types', 'add_dokan_auction_product_type');


add_action( 'dokan_new_product_after_product_tags','new_product_field_moza',10 );
add_action( 'dokan_new_product_added','save_add_product_meta', 10, 2 );
add_action( 'dokan_product_updated', 'save_add_product_meta', 10, 2 );
add_action('dokan_product_edit_after_product_tags','show_on_edit_page',99,2);


add_action( 'init', 'bbloomer_create_auction_product_type' );
add_filter( 'woocommerce_product_class', 'moza_woocommerce_product_class', 10, 2 );
add_filter( 'woocommerce_product_data_tabs', 'create_tab_to_moza_in_admin' );
add_action( 'woocommerce_product_data_panels', 'auction_product_options_product_tab_content' );
add_action( 'woocommerce_process_product_meta', 'save_auction_product_options' );
add_action('pre_get_posts', 'add_auction_product_to_dokan_dashboard');
add_action('woocommerce_before_calculate_totals', 'add_custom_price');
add_action('dokan_product_listing_table_column_vendor_bids', 'dcustom_admin_products_store_name_column_content', 10, 2);
add_filter('dokan_product_listing_table_columns', 'dcustom_vendor_bids_to_tabel');
add_action('manage_product_posts_custom_column', 'custom_admin_products_store_name_column_content', 10, 2);
add_filter('manage_edit-product_columns', 'castome_bids_to_tabel');
add_action('wp_ajax_update_start_price', 'update_start_price_callback');
add_action('wp_ajax_nopriv_update_start_price', 'update_start_price_callback');

add_action('wp_ajax_add_auction_to_cart', 'add_auction_to_cart');
add_action('wp_ajax_nopriv_add_auction_to_cart', 'add_auction_to_cart');

