document.addEventListener('DOMContentLoaded', function() {
    var checkbox = document.getElementById('check_sent_request');
    var element = document.querySelector('.sent_request');
    
    if (!checkbox) {
        console.error('Checkbox with id "check_sent_request" not found.');
        return;
    }
    
    if (!element) {
        console.error('Element with class "sent_request" not found.');
        return;
    }

    checkbox.addEventListener('change', function() {
        if (this.checked) {
            element.style.display = 'block';
        } else {
            element.style.display = 'none';
        }
    });
});


document.addEventListener('DOMContentLoaded', function() {
    var select = document.getElementById('option_sell');
    var element = document.querySelector('.aution');

    select.addEventListener('change', function() {
        if (this.value === 'aution') {
            element.style.display = 'block';
        } else {
            element.style.display = 'none';
        }
    });

    // Initialize display based on the default selected option
    if (select.value === 'aution') {
        element.style.display = 'block';
    } else {
        element.style.display = 'none';
    }
});
document.addEventListener('DOMContentLoaded', function () {

    const productTypeSelect = document.getElementById('product_type');
    const fromContentMoza = document.getElementById('fom_group_data_moza');
    const form_price = document.getElementsByClassName('dokan-price-container');

    function updateDisplay() {
        if (productTypeSelect.value === 'auction_product') {
            fromContentMoza.style.display = 'block';
            for (let i = 0; i < form_price.length; i++) {
                form_price[i].style.display = "none";
            }
        } else {
            fromContentMoza.style.display = 'none';
            for (let i = 0; i < form_price.length; i++) {
                form_price[i].style.display = "block";
            }
        }
    }

    productTypeSelect.addEventListener('change', updateDisplay);

    // به‌روزرسانی نمایش در بارگذاری اولیه
    updateDisplay();

});
document.addEventListener('DOMContentLoaded', function () {
    jQuery(document).ready(function($) {
        $("#start_time").persianDatepicker({
            initialValue: false,
            calendar:{
                persian: {
                    locale: 'en'
                }
            },
            format: 'YYYY/MM/DD-HH:mm:ss',
            timePicker: {
                enabled: true,
                step: 1,
                hour: {
                    enabled: true,
                    step: 1
                },
                minute: {
                    enabled: true,
                    step: 1
                },
                second: {
                    enabled: true,
                    step: 1
                },
                meridian: {
                    enabled: false
                }
            }
        });
        $("#show_hide_list_moza").click(function (){
            $("#box_moza_list").toggle(1000);
        })
    });
    $("#close_bid").click(function (){
        $("#box_moza_list").hide(1000);

    })


});
