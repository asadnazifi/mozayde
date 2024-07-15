<?php

add_action('admin_menu', 'menu_mza_in_price');

add_action('woocommerce_product_meta_start','show_product_code',10);
add_action('admin_post_save_custom_price_ranges', 'save_custom_price_ranges');
add_action( 'init', 'create_quality_taxonomy' );

add_action('init', 'register_auction_product_type');
add_filter('product_type_selector', 'add_auction_product_type');
add_filter('dokan_product_types','add_dokan_auction_product_type',12);


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
add_action('manage_product_posts_custom_column', 'custom_admin_products_store_name_column_content', 10, 2);
add_filter('manage_edit-product_columns', 'castome_bids_to_tabel');
add_action('wp_ajax_update_start_price', 'update_start_price_callback');
add_action('wp_ajax_nopriv_update_start_price', 'update_start_price_callback');

add_action('wp_ajax_add_auction_to_cart', 'add_auction_to_cart');
add_action('wp_ajax_nopriv_add_auction_to_cart', 'add_auction_to_cart');

add_action('template_redirect', 'prevent_seller_editing_published_products');

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

    // Add notice after redirect
}

// Add new columns to the Dokan product list table
add_filter('dokan_product_listing_columns', 'add_custom_dokan_product_columns');

function add_custom_dokan_product_columns($columns) {
    $columns['new_column1'] = __('New Column 1', 'your-text-domain');
    $columns['new_column2'] = __('New Column 2', 'your-text-domain');
    return $columns;
}

// Display custom column content in Dokan product list table
add_action('dokan_product_list_table_after_column_content', 'populate_custom_dokan_product_columns', 12, 2);

function populate_custom_dokan_product_columns($column_name, $post) {
    if ($column_name == 'new_column1') {
        echo get_post_meta($post->ID, '_new_column1_meta_key', true);
    }

    if ($column_name == 'new_column2') {
        echo get_post_meta($post->ID, '_new_column2_meta_key', true);
    }
}

// Add custom CSS for admin area (Optional)
add_action('admin_head', 'custom_admin_styles');

function custom_admin_styles() {
    echo '<style>
        .column-new_column1 { width: 10%; }
        .column-new_column2 { width: 10%; }
    </style>';
}
