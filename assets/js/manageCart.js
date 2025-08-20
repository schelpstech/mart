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
            url: "./app/ajax/cart_action.php",
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
            url: "./app/ajax/cart_action.php",
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
            url: "./app/ajax/cart_action.php",
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
    const product_id = $btn.data("productid");
    const quantity = $btn.data("quantity") || 1;
    const cart_item_id = $btn.data("cartitemid") || null;

    if (!product_id) return;

    let postData = {};
    let action = ""; // 🔑 keep track of action locally

    if ($btn.hasClass("in-cart")) {
        // Removing from cart
        action = "remove";
        postData = {
            action: action,
            cart_item_id: cart_item_id,
        };
    } else {
        // Adding to cart
        action = "add";
        postData = {
            action: action,
            product_id: product_id,
            quantity: quantity,
        };
    }

    $.ajax({
        url: "./app/ajax/cart_action.php",
        type: "POST",
        data: postData,
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                updateCartCount();
                refreshCartSidebar();

                if (action === "add") {
                    $btn
                        .addClass("in-cart")
                        .attr("title", "Remove From Cart")
                        .attr("data-cartitemid", res.cart_item_id) // cart_item_id returned from server
                        .html('<i class="fi-rr-trash"></i>');
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
