$(document).ready(function () {
    // -------------------------------
    // Show custom toast
    // -------------------------------
    function showToast(message, type = "success") {
        const $toast = $("#addtocart_toast");
        $toast.find(".desc").text(message);
        $toast.css("background-color", type === "error" ? "#FF4C4C" : "#4BB543");
        $toast.addClass("show");

        // Hide after 3 seconds
        setTimeout(function () {
            $toast.removeClass("show");
        }, 3000);
    }

    // -------------------------------
    // Update cart count
    // -------------------------------
    function updateCartCount() {
        $.ajax({
            url: "../app/ajax/cart_action.php",
            type: "POST",
            data: { action: "count" },
            dataType: "json",
            success: function (res) {
                if (res.status === "success" && res.count !== undefined) {
                    $(".cart-count-lable").text(res.count);
                }
            },
            error: function () {
                console.error("Failed to fetch cart count");
            },
        });
    }

    // -------------------------------
    // Sync buttons on page load
    // -------------------------------
    function syncCartButtons() {
        $.ajax({
            url: "../app/ajax/cart_action.php",
            type: "POST",
            data: { action: "get_cart_items" },
            dataType: "json",
            success: function (res) {
                if (res.status === "success" && Array.isArray(res.items)) {
                    res.items.forEach(function (item) {
                        $(".add-to-cart").each(function () {
                            let btnId = $(this).data("productid");
                            if (parseInt(btnId) === parseInt(item.product_id)) {
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
            error: function () {
                console.error("Failed to sync cart buttons");
            },
        });
    }

    // -------------------------------
    // Update cart sidebar
    // -------------------------------
    function refreshCartSidebar() {
        $.ajax({
            url: "../app/ajax/cart_action.php",
            type: "POST",
            data: { action: "get_cart_html" },
            dataType: "html",
            success: function (html) {
                $(".eccart-pro-items").html(html);
            },
            error: function () {
                console.error("Failed to refresh cart sidebar");
            },
        });
    }

    // -------------------------------
    // Add / Remove cart button click
    // -------------------------------
    $(document).on("click", ".add-to-cart", function (e) {
        e.preventDefault();

        const $btn = $(this);

        // ✅ Always re-fetch attributes fresh
        const product_id = $btn.attr("data-productid");
        const quantity = $btn.attr("data-quantity") || 1;
        const cart_item_id = $btn.attr("data-cartitemid") || null;

        if (!product_id && !$btn.hasClass("in-cart")) return;

        let action = "";
        let requestData = {};

        if ($btn.hasClass("in-cart")) {
            // Removing from cart
            action = "remove";
            requestData = { action: action, cart_item_id: cart_item_id };

        } else {
            // Adding to cart
            action = "add";
            requestData = {
                action: action,
                product_id: product_id,
                quantity: quantity,
            };
        }

        $.ajax({
            url: "../app/ajax/cart_action.php",
            type: "POST",
            data: requestData,
            dataType: "json",
            success: function (res) {
                if (res.status === "success") {
                    updateCartCount();
                    refreshCartSidebar();
                    syncCartButtons();

                    if (action === "add") {
                        $btn
                            .addClass("in-cart")
                            .attr("title", "Remove From Cart")
                            .attr("data-cartitemid", res.item_id) // ✅ store ID
                            .html('<i class="fi-rr-trash"></i> ');

                        showToast(res.msg || "You Have Added To Cart Successfully");
                    } else if (action === "remove") {
                        $btn
                            .removeClass("in-cart")
                            .attr("title", "Add To Cart")
                            .removeAttr("data-cartitemid")
                            .html('<i class="fi-rr-shopping-basket"></i>');

                        showToast(res.msg || "Item Removed From Cart");
                    }
                } else {
                    console.error(res);
                    showToast(res.msg || "Error updating cart", "error");
                }
            },
            error: function (xhr) {
                console.error(
                    "AJAX error:",
                    xhr.status,
                    xhr.statusText,
                    xhr.responseText
                );
                showToast("Network / server error", "error");
            },
        });
    });

    // -------------------------------
    // Initial page load
    // -------------------------------
    updateCartCount();
    syncCartButtons();
});
$(document).on("click", ".remove-from-cart", function (e) {
    e.preventDefault();

    const cart_item_id = $(this).data("cartitemid");
    if (!cart_item_id) return;

    $.ajax({
        url: "../app/ajax/cart_action.php",
        type: "POST",
        data: { action: "remove", cart_item_id: cart_item_id },
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                location.reload(); // reload cart table to update totals
            } else {
                alert(res.msg || "Error removing item.");
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            alert("Server error removing item.");
        },
    });
});

// Listen for quantity change
$(document).on("change", ".cart-plus-minus", function () {
    const $input = $(this);
    const cart_item_id = $input.data("cartitemid");
    let quantity = parseInt($input.val(), 10);


    // Validate quantity
    if (!cart_item_id || isNaN(quantity) || quantity <= 0) {
        alert("Invalid quantity");
        return;
    }

    $.ajax({
        url: "../app/ajax/cart_action.php",
        type: "POST",
        data: {
            action: "update_quantity",
            cart_item_id: cart_item_id,
            quantity: quantity,
        },
        dataType: "json",

        success: function (res) {
            if (res.status === "success") {
                // Update only this row’s line total
                $input
                    .closest("tr")
                    .find(".line-total")
                    .text("£" + res.line_total);

                // Update subtotal and grand total
                $(".cart-subtotal").text(res.cart_subtotal);
                $(".cart-grandtotal").text(res.cart_grandtotal);

                showToast(res.msg || "Quantity updated successfully");
            } else {
                console.error(res);
                showToast(res.msg || "Error updating quantity", "error");
            }
        },

        error: function (xhr) {
            console.error(
                "AJAX error:",
                xhr.status,
                xhr.statusText,
                xhr.responseText
            );
            showToast("Network / server error", "error");
        },
    });
});

//
$(document).on("click", ".add-to-cart", function () {
    const product_id = $(this).data("productid");
    const quantity = $(".qty-input").val() || 1;

    $.ajax({
        url: "../app/ajax/cart_action.php",
        type: "POST",
        data: {
            action: "add_to_cart",
            product_id: product_id,
            quantity: quantity
        },
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                showToast(res.msg || "Product added to cart!");
            } else {
                showToast(res.msg || "Error adding product", "error");
            }
        },
        error: function (xhr) {
            console.error("AJAX error:", xhr.responseText);
            showToast("Network / server error", "error");
        }
    });
});