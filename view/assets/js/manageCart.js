$(function () {
    let refreshRequest = null;
    let mutationQueue = $.Deferred().resolve().promise();
    let pending = 0;
    function toast(message, error) {
        const box = $('#addtocart_toast');
        box.find('.desc').text(message);
        box.css('background-color', error ? '#b52d32' : '#147d47').addClass('show');
        setTimeout(function () { box.removeClass('show'); }, 3000);
    }
    function updateSummary(summary) {
        $('.cart-count-lable').text(summary.count || 0);
        $('.cart-subtotal').text(summary.subTotal || summary.cart_subtotal || '£0.00');
        $('.cart-grandtotal').text(summary.total || summary.cart_grandtotal || '£0.00');
        $('#ajax-subtotal').text(summary.subTotal || '£0.00');
        $(document).trigger('cart:updated', [summary]);
    }
    function syncButtons(items) {
        $('.add-to-cart').each(function () {
            const button = $(this);
            const productId = parseInt(button.attr('data-productid'), 10);
            const item = items.find(function (row) { return row.product_id === productId; });
            const label = item ? 'Remove From Cart' : 'Add To Cart';
            button.toggleClass('in-cart', !!item).attr('title', label).attr('aria-label', label);
            if (item) button.attr('data-cartitemid', item.cart_item_id);
            else button.removeAttr('data-cartitemid');
            button.html('<i aria-hidden="true" class="' + (item ? 'fi-rr-trash' : 'fi-rr-shopping-basket') + '"></i>' + (this.tagName === 'BUTTON' ? ' ' + label : ''));
        });
    }
    function refresh() {
        if (refreshRequest) return refreshRequest;
        refreshRequest = $.ajax({
            url: '../app/ajax/get_cart_items.php', type: 'GET', dataType: 'json', cache: false,
            success: function (summary) {
                $('.eccart-pro-items').html(summary.html || '<li><p class="emp-cart-msg">Your cart is empty!</p></li>');
                syncButtons(summary.items || []);
                updateSummary(summary);
            },
            error: function () {
                $('.eccart-pro-items').html('<li><p class="emp-cart-msg">Your cart could not be loaded. Please refresh and try again.</p></li>');
                $(document).trigger('cart:updated', [{count: 0}]);
            },
            complete: function () { refreshRequest = null; }
        });
        return refreshRequest;
    }
    function mutate(data, success) {
        pending++;
        $(document).trigger('cart:busy', [true]);
        mutationQueue = mutationQueue.then(function () {
            return $.ajax({url: '../app/ajax/cart_action.php', type: 'POST', dataType: 'json', data: data})
                .then(function (result) {
                    if (result.status !== 'success') { toast(result.msg || 'Your cart could not be updated.', true); return; }
                    if (success) success(result);
                    toast(result.msg || 'Cart updated.');
                }, function () { toast('Your cart could not be updated. Please try again.', true); })
                .then(function () { return refreshRequest || $.Deferred().resolve().promise(); })
                .then(refresh, refresh).always(function () {
                    pending--;
                    $(document).trigger('cart:busy', [pending > 0]);
                }).then(function () {}, function () {});
        });
    }
    $(document).on('cart:refresh', function () { mutationQueue.then(refresh, refresh); });
    $(document).on('click', '.add-to-cart', function (event) {
        event.preventDefault();
        const button = $(this);
        if (button.attr('aria-disabled') === 'true') return;
        const removing = button.hasClass('in-cart');
        const productId = button.attr('data-productid');
        if (!productId) return;
        const quantityField = button.closest('.ec-single-qty').find('.qty-input');
        const quantity = quantityField.length ? quantityField.val() : (button.attr('data-quantity') || 1);
        const data = removing
            ? {action: 'remove', cart_item_id: button.attr('data-cartitemid'), item_type: 'product'}
            : {action: 'add', product_id: productId, quantity: quantity};
        button.prop('disabled', true).attr('aria-disabled', 'true');
        mutate(data, function () {});
        mutationQueue.always(function () { button.prop('disabled', false).removeAttr('aria-disabled'); });
    });
    $(document).on('click', '.remove-from-cart, .ec-pro-content .removed', function (event) {
        event.preventDefault();
        const id = $(this).data('cartitemid');
        const type = $(this).data('itemtype') || 'product';
        if (!id) return;
        mutate({action: 'remove', cart_item_id: id, item_type: type}, function () {
            if ($('body').hasClass('cart_page') || $('body').hasClass('checkout_page')) location.reload();
        });
    });
    $(document).on('change', '.cart-plus-minus', function () {
        const field = $(this);
        const quantity = parseInt(field.val(), 10);
        if (!Number.isInteger(quantity) || quantity < 1) { toast('Quantity must be at least one.', true); field.val(1); return; }
        mutate({action: 'update_quantity', cart_item_id: field.data('cartitemid'), item_type: field.data('itemtype') || 'product', quantity: quantity}, function (result) {
            field.closest('tr').find('.line-total').text(result.line_total);
        });
    });
    $(document).on('catalog:loaded', refresh);
    refresh();
});
