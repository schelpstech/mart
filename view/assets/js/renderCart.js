$(document).on("click", '.ec-side-toggle[href="#ec-side-cart"], .toggle-cart', function (e) {
    e.preventDefault();
    $("#ec-side-cart").show();
    loadCart();
});

function loadCart() {
    $.ajax({
        url: "../app/ajax/get_cart_items.php",
        type: "GET",
        dataType: "json",
        success: function (res) {
            $("#ajax-cart-items").html(res.html || '<li><p class="emp-cart-msg">Your cart is empty!</p></li>');
            $("#ajax-subtotal").text(res.subTotal || "£0.00");
            $("#ajax-total").text(res.total || "£0.00");
            $(".cart-count-lable").text(res.count || 0);
        },
        error: function (xhr) {
            console.error("Error loading cart:", xhr.responseText);
        },
    });
}
