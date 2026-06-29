$(document).ready(function () {
    function showToast(message, type = "success") {
        const $toast = $("#addtocart_toast");
        $toast.find(".desc").text(message);
        $toast.css("background-color", type === "error" ? "#FF4C4C" : "#4BB543");
        $toast.addClass("show");
        setTimeout(() => $toast.removeClass("show"), 3000);
    }

    function updateCartCount(count) {
        if (count !== undefined) {
            $(".cart-count-lable").text(count);
            return;
        }

        $.ajax({
            url: "../app/ajax/cart_action.php",
            type: "POST",
            data: { action: "count" },
            dataType: "json",
            success: function (res) {
                if (res.status === "success") {
                    $(".cart-count-lable").text(res.count || 0);
                }
            },
        });
    }

    function syncCartButtons() {
        $.ajax({
            url: "../app/ajax/cart_action.php",
            type: "POST",
            data: { action: "get_cart_items" },
            dataType: "json",
            success: function (res) {
                $(".add-to-cart").removeClass("in-cart").attr("title", "Add To Cart").removeAttr("data-cartitemid").html('<i class="fi-rr-shopping-basket"></i>');

                if (res.status === "success" && Array.isArray(res.items)) {
                    res.items.forEach(function (item) {
                        $(".add-to-cart").each(function () {
                            const btnId = $(this).data("productid");
                            if (parseInt(btnId, 10) === parseInt(item.product_id, 10)) {
                                $(this)
                                    .addClass("in-cart")
                                    .attr("title", "Remove From Cart")
                                    .attr("data-cartitemid", item.cart_item_id)
                                    .html('<i class="fi-rr-trash"></i>');
                            }
                        });
                    });
                }
            },
        });
    }

    function refreshCartSidebar() {
        $.ajax({
            url: "../app/ajax/get_cart_items.php",
            type: "GET",
            dataType: "json",
            success: function (res) {
                $(".eccart-pro-items").html(res.html || '<li><p class="emp-cart-msg">Your cart is empty!</p></li>');
                $("#ajax-subtotal").text(res.subTotal || "£0.00");
                $("#ajax-total").text(res.total || "£0.00");
                updateCartCount(res.count || 0);
            },
        });
    }

    $(document).on("click", ".add-to-cart", function (e) {
        e.preventDefault();

        const $btn = $(this);
        const product_id = $btn.attr("data-productid");
        const quantity = $btn.attr("data-quantity") || 1;
        const cart_item_id = $btn.attr("data-cartitemid") || null;

        if (!product_id && !$btn.hasClass("in-cart")) return;

        const removing = $btn.hasClass("in-cart");
        const requestData = removing
            ? { action: "remove", cart_item_id: cart_item_id, item_type: "product" }
            : { action: "add", product_id: product_id, quantity: quantity };

        $.ajax({
            url: "../app/ajax/cart_action.php",
            type: "POST",
            data: requestData,
            dataType: "json",
            success: function (res) {
                if (res.status === "success") {
                    updateCartCount(res.count || 0);
                    refreshCartSidebar();
                    syncCartButtons();
                    showToast(res.msg || (removing ? "Item removed from cart" : "Added to cart"));
                } else {
                    showToast(res.msg || "Error updating cart", "error");
                }
            },
            error: function (xhr) {
                console.error("AJAX error:", xhr.responseText);
                showToast("Network / server error", "error");
            },
        });
    });

    $(document).on("click", ".remove-from-cart, .ec-pro-content .removed", function (e) {
        e.preventDefault();

        const cart_item_id = $(this).data("cartitemid");
        const item_type = $(this).data("itemtype") || "product";
        if (!cart_item_id) return;

        $.ajax({
            url: "../app/ajax/cart_action.php",
            type: "POST",
            data: { action: "remove", cart_item_id: cart_item_id, item_type: item_type },
            dataType: "json",
            success: function (res) {
                if (res.status === "success") {
                    if ($("body").hasClass("cart_page")) {
                        location.reload();
                        return;
                    }
                    refreshCartSidebar();
                    syncCartButtons();
                    updateCartCount(res.count || 0);
                    showToast(res.msg || "Item removed from cart");
                } else {
                    showToast(res.msg || "Error removing item.", "error");
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                showToast("Server error removing item.", "error");
            },
        });
    });

    $(document).on("change", ".cart-plus-minus", function () {
        const $input = $(this);
        const cart_item_id = $input.data("cartitemid");
        const item_type = $input.data("itemtype") || "product";
        const quantity = parseInt($input.val(), 10);

        if (!cart_item_id || isNaN(quantity) || quantity <= 0) {
            showToast("Invalid quantity", "error");
            return;
        }

        $.ajax({
            url: "../app/ajax/cart_action.php",
            type: "POST",
            data: {
                action: "update_quantity",
                cart_item_id: cart_item_id,
                item_type: item_type,
                quantity: quantity,
            },
            dataType: "json",
            success: function (res) {
                if (res.status === "success") {
                    $input.closest("tr").find(".line-total").text(res.line_total);
                    $(".cart-subtotal").text(res.cart_subtotal);
                    $(".cart-grandtotal").text(res.cart_grandtotal);
                    updateCartCount(res.count || 0);
                    refreshCartSidebar();
                    showToast(res.msg || "Quantity updated successfully");
                } else {
                    showToast(res.msg || "Error updating quantity", "error");
                }
            },
            error: function (xhr) {
                console.error("AJAX error:", xhr.responseText);
                showToast("Network / server error", "error");
            },
        });
    });

    updateCartCount();
    syncCartButtons();
    refreshCartSidebar();
});
