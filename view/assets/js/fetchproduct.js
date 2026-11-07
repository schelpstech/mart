document.addEventListener('DOMContentLoaded', function () {
    const path = window.location.pathname.split('/').pop();
    const container = document.getElementById(path === 'shop.php' ? 'product-container' : 'product-category-container');
    if (!container || !['shop.php', 'viewcategory.php'].includes(path)) return;
    const endpoint = path === 'shop.php' ? '../app/ajax/fetchproduct.php' : '../app/ajax/fetchproductbycat.php';
    const loader = document.getElementById('loader');
    const message = document.createElement('div');
    const status = document.createElement('p');
    const retry = document.createElement('button');
    message.className = 'qs-catalog-message'; message.hidden = true;
    status.setAttribute('role', 'status'); status.setAttribute('aria-live', 'polite');
    retry.type = 'button'; retry.className = 'qs-catalog-retry'; retry.textContent = 'Try again'; retry.hidden = true;
    message.append(status, retry); container.after(message);
    if (loader) { loader.setAttribute('role', 'status'); loader.setAttribute('aria-live', 'polite'); }
    let offset = 0, loading = false, finished = false, failed = false;
    const limit = 8;
    async function loadProducts() {
        if (loading || finished || failed) return;
        loading = true;
        container.setAttribute('aria-busy', 'true');
        if (loader) loader.style.display = 'block';
        message.hidden = true;
        const params = new URLSearchParams({offset: String(offset), limit: String(limit)});
        // Keep the category tied to this tab instead of a shared session value.
        if (path === 'viewcategory.php') params.set('category_id', container.dataset.categoryId || '0');
        try {
            const response = await fetch(endpoint + '?' + params);
            if (!response.ok) throw new Error('Catalog request failed');
            const markup = await response.text();
            if (!markup.trim()) {
                finished = true;
                if (offset === 0) {
                    status.textContent = 'No products are available here yet. Please browse another category.';
                    retry.hidden = true; message.hidden = false;
                }
            } else {
                container.insertAdjacentHTML('beforeend', markup);
                offset += limit;
                if (window.jQuery) window.jQuery(document).trigger('catalog:loaded');
            }
        } catch (error) {
            failed = true;
            status.textContent = 'We couldn’t load the products. Check your connection and try again.';
            retry.hidden = false; message.hidden = false;
        } finally {
            loading = false;
            container.setAttribute('aria-busy', 'false');
            if (loader) loader.style.display = 'none';
        }
    }
    retry.addEventListener('click', function () { failed = false; loadProducts(); });
    loadProducts();
    window.addEventListener('scroll', function () {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 300) loadProducts();
    }, {passive: true});
});
