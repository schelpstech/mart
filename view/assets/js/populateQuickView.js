$(document).on('click', '.quickview', function (e) {
    e.preventDefault();

    const productId = $(this).data('id');

    $.ajax({
        url: '../app/ajax/get_product_details.php',
        type: 'POST',
        data: { id: productId },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                // Update product name & link
                $('#ec_quickview_modal .ec-quick-title a')
                    .text(response.data.name)
                    .attr('href', 'viewproduct.php?id=' + productId);

                // Update description
                $('#ec_quickview_modal .ec-quickview-desc').text(response.data.description);

                // Update prices
                $('#ec_quickview_modal .new-price').text('₦' + response.data.new_price);
                $('#ec_quickview_modal .old-price').text(response.data.old_price ? '£' + response.data.old_price : '');

                // Load product images dynamically
                let imageHtml = '';
                let thumbHtml = '';
                if (response.data.images && response.data.images.length > 0) {
                    response.data.images.forEach(function (img) {
                        imageHtml += `<div class="qty-slide"><img class="img-responsive" src="${img}" alt=""></div>`;
                        thumbHtml += `<div class="qty-slide"><img class="img-responsive" src="${img}" alt=""></div>`;
                    });
                } else {
                    // Fallback image
                    imageHtml = `<div class="qty-slide"><img class="img-responsive" src="assets/images/product-image/default.jpg" alt=""></div>`;
                    thumbHtml = imageHtml;
                }

                $('#ec_quickview_modal .qty-product-cover').html(imageHtml);
                $('#ec_quickview_modal .qty-nav-thumb').html(thumbHtml);

                // Show modal
                $('#ec_quickview_modal').modal('show');
            } else {
                alert("Product not found!");
            }
        },
        error: function () {
            alert("Error fetching product details.");
        }
    });
});
