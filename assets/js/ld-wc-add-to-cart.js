
jQuery(document).ready(function ($) {
    if ($('.wc-ld-add-to-cart').length) {
        $('.wc-ld-add-to-cart').each(function () {
            var $button = $(this),
                product_id = $button.data('product_id'),
                $buttonText = $button.find('.add-to-cart-text'),
                onSuccessText = $button.data('on-success-text');

            if (cartStatus[product_id]) {
                $buttonText.text(onSuccessText);
                $button.addClass('in-cart');
            }
        });

        $('.wc-ld-add-to-cart').click(function (e) {
            e.preventDefault();

            var $button = $(this),
                $buttonText = $button.find('.add-to-cart-text'),
                product_id = $button.data('product_id'),
                onSuccessText = $button.data('on-success-text');

            $.ajax({
                type: 'POST',
                url: wc_add_to_cart_params_object.ajax_url,
                data: {
                    action: 'woocommerce_ajax_add_to_cart',
                    product_id: product_id,
                    quantity: 1,
                    nonce: wc_add_to_cart_params_object.nonce,
                },
                success: function (response) {
                    $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
                    $buttonText.text(onSuccessText);
                    $button.addClass('in-cart');
                }
            });
        });
    }

    if ($('.wc-ld-variable-product-link').length) {
        jQuery(document).on('click', '.wc-ld-variable-product-link', function (e) {
            e.preventDefault();
            window.location.href = jQuery(this).attr('href');
        });
    }
});
