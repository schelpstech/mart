// Load cart when the cart toggle button is clicked
$(document).on('click', '.ec-side-toggle', function(e){
    e.preventDefault(); // prevent default anchor behavior
    $('#ec-side-cart').show(); // show the side cart
    loadCart(); // fetch cart items via AJAX
});

// Function to fetch and display cart items
function loadCart() {
    $.ajax({
        url: '../app/ajax/get_cart_items.php',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            $('#ajax-cart-items').html(res.html || '<li>Your cart is empty</li>');
            $('#ajax-subtotal').text(res.subTotal || '£0.00');
            $('#ajax-vat').text(res.vat || '£0.00');
            $('#ajax-total').text(res.total || '£0.00');
        },
        error: function(xhr) {
            console.error('Error loading cart:', xhr.responseText);
        }
    });
}

// Remove item from cart dynamically
$(document).on('click', '.ec-pro-content .removed', function (e) {
    e.preventDefault();

    const cartItemId = $(this).data('cartitemid');
    if (!cartItemId) return;

    const $li = $(this).closest('li');

    $.ajax({
        url: '../app/ajax/cart_action.php',
        type: 'POST',
        data: { action: 'remove', cart_item_id: cartItemId },
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success') {
                // 1) Remove the item line from the side cart (with a tiny animation)
                $li.slideUp(200, function () { $(this).remove(); });

                // 2) Update cart count bubble
                $('.cart-count-lable').text(res.count || 0);

                // 3) Update totals in the footer
                $('#ajax-subtotal').text(res.subTotal || '£0.00');
                $('#ajax-vat').text(res.vat || '£0.00');
                $('#ajax-total').text(res.total || '£0.00');

                // 4) Flip the corresponding product button back to "Add To Cart"
                //    We target by *cart item id* because your product buttons carry data-cartitemid
                const $productBtns = $(`.add-to-cart[data-cartitemid="${cartItemId}"]`);
                if ($productBtns.length) {
                    $productBtns.each(function () {
                        $(this)
                            .removeClass('in-cart')
                            .attr('title', 'Add To Cart')
                            .data('cartitemid', '')         // jQuery cache
                            .attr('data-cartitemid', '')     // DOM attribute
                            .find('i')
                                .removeClass('fi-rr-trash')
                                .addClass('fi-rr-shopping-basket');
                    });
                }
            } else {
                console.warn(res.message || 'Unable to remove item.');
            }
        },
        error: function (xhr) {
            console.error('Remove cart item error:', xhr.responseText);
        }
    });
});




;
