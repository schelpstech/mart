$(function () {
    const dialog = document.getElementById('cart-share-dialog');
    if (!dialog) return;
    const messageField = document.getElementById('cart-share-message');
    const previewStatus = document.getElementById('cart-share-preview-status');
    let lastTrigger = null;
    let busy = false;
    let cartBusy = false;
    let cartCount = 0;

    function setAvailability(count) {
        cartCount = count;
        $('.cart-share-open, .cart-share-copy').prop('disabled', !count || busy || cartBusy);
        $('.cart-share-status').text(count ? 'Review your list before sending. Your cart stays available.' : 'Add items to your cart to share a list.');
    }
    $(document).on('cart:updated', function (_, summary) { setAvailability(summary.count || 0); });
    $(document).on('cart:busy', function (_, saving) { cartBusy = saving; setAvailability(cartCount); });

    async function copyMessage() {
        try {
            if (!navigator.clipboard || !window.isSecureContext) throw new Error('Clipboard unavailable');
            await navigator.clipboard.writeText(messageField.value);
            previewStatus.textContent = 'List copied. Open WhatsApp and paste it into your message.';
        } catch (_) {
            messageField.focus();
            messageField.select();
            previewStatus.textContent = 'Your list is selected. Use Copy on your device, then paste it into WhatsApp.';
        }
    }

    $('.cart-share-close').on('click', function () { dialog.close(); });
    $('.cart-share-preview-copy').on('click', copyMessage);
    dialog.addEventListener('close', function () {
        if (lastTrigger && document.contains(lastTrigger)) lastTrigger.focus();
    });
    $(document).on('click', '.cart-share-open, .cart-share-copy', function () {
        if (busy || cartBusy) return;
        const trigger = this;
        const block = trigger.closest('.cart-share');
        const context = block.dataset.shareContext;
        const fulfilment = document.querySelector('input[name="fulfilment_type"]:checked, input[name="fulfilment_type"][type="hidden"]');
        const data = {context: context};
        if (context === 'checkout') {
            data.fulfilment_type = fulfilment ? fulfilment.value : '';
            ['delivery_zone_id', 'coupon_code', 'appointment_date', 'appointment_time'].forEach(function (key) {
                const field = document.getElementById(key);
                data[key] = field ? field.value : '';
            });
        }
        busy = true;
        $('.cart-share-open, .cart-share-copy').prop('disabled', true);
        block.querySelector('.cart-share-status').textContent = 'Preparing your current cart…';
        $.ajax({
            url: '../app/ajax/cart_share.php', type: 'POST', dataType: 'json', data: data,
            success: function (result) {
                if (result.status !== 'success') return;
                messageField.value = result.message;
                dialog.querySelector('.cart-share-whatsapp').href = result.url;
                previewStatus.textContent = result.copy_required
                    ? 'This is a long list. Copy it first, then open WhatsApp and paste it into the chat.'
                    : 'This is a cart enquiry. Availability and the final amount will be confirmed by the store.';
                lastTrigger = trigger;
                dialog.showModal();
                if (trigger.classList.contains('cart-share-copy')) copyMessage();
            },
            error: function (xhr) {
                block.querySelector('.cart-share-status').textContent = xhr.responseJSON?.msg || 'Your list could not be prepared. Please try again.';
            },
            complete: function () {
                busy = false;
                $('.cart-share-open, .cart-share-copy').prop('disabled', !cartCount || cartBusy);
                if (dialog.open) block.querySelector('.cart-share-status').textContent = 'Your current list is ready to review.';
            }
        });
    });
});
