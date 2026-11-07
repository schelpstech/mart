// Capture errors for future AJAX cards too; check images whose errors preceded this script.
(function () {
    function showProductFallback(image) {
        if (image.matches('img[data-product-image]')) image.closest('.qs-product-visual')?.classList.add('is-placeholder');
        if (image.matches('img[data-category-image]')) image.closest('.qs-category-visual')?.classList.add('is-placeholder');
    }
    document.addEventListener('error', function (event) {
        if (event.target instanceof HTMLImageElement) showProductFallback(event.target);
    }, true);
    document.querySelectorAll('img[data-product-image], img[data-category-image]').forEach(function (image) {
        if (image.complete && image.naturalWidth === 0) showProductFallback(image);
    });
}());

$(function () {
    const panels = $('#ec-mobile-menu, #ec-side-cart');
    const overlay = $('.ec-side-cart-overlay');
    let opener = null;
    function focusable(panel) {
        return $(panel).find('a[href], button, input, select, textarea, [tabindex="0"]').filter(function () { return !this.disabled && this.getClientRects().length > 0; });
    }
    function closeDrawers(restoreFocus = true) {
        panels.removeClass('ec-open').attr('aria-hidden', 'true').attr('inert', '');
        overlay.hide();
        $('body').removeClass('qs-drawer-open');
        $('.ec-side-toggle').attr('aria-expanded', 'false');
        if (restoreFocus && opener) opener.focus();
    }
    $(document).on('click', '.ec-side-toggle', function (event) {
        const target = $(this).attr('href');
        if (!['#ec-mobile-menu', '#ec-side-cart'].includes(target) || !$(target).length) return;
        event.preventDefault();
        closeDrawers(false);
        opener = this;
        const panel = $(target).removeAttr('inert').attr('aria-hidden', 'false').addClass('ec-open');
        $('.ec-side-toggle[href="' + target + '"]').attr('aria-expanded', 'true');
        $('body').addClass('qs-drawer-open');
        overlay.show();
        focusable(panel).first().trigger('focus');
        if (target === '#ec-side-cart') $(document).trigger('cart:refresh');
    });
    panels.on('click', '.ec-close', function () { closeDrawers(); });
    overlay.on('click', function () { closeDrawers(); });
    $(document).on('keydown', function (event) {
        // A native cart preview dialog manages its own focus and Escape behaviour.
        if (document.getElementById('cart-share-dialog')?.open) return;
        const active = panels.filter('.ec-open');
        if (!active.length) return;
        if (event.key === 'Escape') { event.preventDefault(); closeDrawers(); }
        if (event.key === 'Tab') {
            const targets = focusable(active);
            const first = targets[0], last = targets[targets.length - 1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        }
    });
    $('.qs-menu-section').on('click', function () {
        const expanded = this.getAttribute('aria-expanded') === 'true';
        $('.qs-menu-section').attr('aria-expanded', 'false').each(function () { document.getElementById(this.getAttribute('aria-controls')).hidden = true; });
        this.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        document.getElementById(this.getAttribute('aria-controls')).hidden = expanded;
    });
});
