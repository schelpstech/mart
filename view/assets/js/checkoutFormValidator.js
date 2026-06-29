document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("checkoutForm");
    if (!form) return;

    const fulfilmentInputs = document.querySelectorAll('input[name="fulfilment_type"]');
    const deliveryZone = document.getElementById("delivery_zone_id");
    const couponInput = document.getElementById("coupon_code");
    const couponButton = document.getElementById("apply-coupon");
    const couponMessage = document.getElementById("coupon-message");
    const fulfilmentOptions = document.querySelector(".fulfilment-options");
    const fulfilmentError = document.getElementById("fulfilment-error");
    const fulfilmentSelectedLabel = document.getElementById("fulfilment-selected-label");

    function selectedFulfilment() {
        const checked = document.querySelector('input[name="fulfilment_type"]:checked');
        const hidden = document.querySelector('input[name="fulfilment_type"][type="hidden"]');
        return checked ? checked.value : (hidden ? hidden.value : "");
    }

    function displayFulfilmentLabel(value) {
        return value === "delivery" ? "Delivery" : (value === "pickup" ? "Pickup" : "");
    }

    function updateFulfilmentAnnouncement(showError = false) {
        const value = selectedFulfilment();

        document.querySelectorAll(".fulfilment-option-card").forEach(function (card) {
            const input = card.querySelector('input[name="fulfilment_type"]');
            card.classList.toggle("is-selected", !!input && input.checked);
        });

        if (fulfilmentSelectedLabel) {
            fulfilmentSelectedLabel.textContent = value
                ? displayFulfilmentLabel(value) + " selected. You can now continue checkout."
                : "Choose delivery or pickup for the product items in your cart.";
        }

        if (fulfilmentError) {
            fulfilmentError.style.display = showError && !value ? "block" : "none";
        }
    }

    function setAddressRequirements() {
        const isDelivery = selectedFulfilment() === "delivery";
        const requiredFields = ["address1", "city", "postcode"];

        requiredFields.forEach(function (id) {
            const field = document.getElementById(id);
            if (field) field.required = isDelivery;
        });

        document.querySelectorAll(".delivery-required-marker").forEach(function (marker) {
            marker.style.display = isDelivery ? "" : "none";
        });

        document.querySelectorAll(".delivery-address-field").forEach(function (wrap) {
            wrap.classList.toggle("pickup-optional", !isDelivery);
            wrap.style.display = isDelivery ? "" : "none";
        });

        const pickupBox = document.getElementById("pickup-instructions");
        if (pickupBox) pickupBox.style.display = isDelivery ? "none" : "";

        const zoneWrap = document.querySelector(".delivery-zone-wrap");
        if (zoneWrap) zoneWrap.style.display = isDelivery ? "" : "none";

        updateFulfilmentAnnouncement();
    }

    function refreshTotals() {
        if (!window.jQuery) return;
        const fulfilment = selectedFulfilment();

        $.ajax({
            url: "../app/ajax/checkout_action.php",
            type: "POST",
            dataType: "json",
            data: {
                action: "calculate_totals",
                fulfilment_type: fulfilment || "pickup",
                delivery_zone_id: deliveryZone ? deliveryZone.value : "",
                coupon_code: couponInput ? couponInput.value : "",
            },
            success: function (res) {
                if (res.subtotal) $("#checkout-subtotal").text(res.subtotal);
                if (res.delivery_fee) $("#checkout-delivery").text(res.delivery_fee);
                if (res.discount) $("#checkout-discount").text(res.discount);
                if (res.total) $("#checkout-total").text(res.total);

                if (couponMessage) {
                    couponMessage.textContent = res.msg || "";
                    couponMessage.className = "d-block mt-2 " + (res.status === "success" ? "text-success" : "text-danger");
                }
            },
        });
    }

    fulfilmentInputs.forEach(function (input) {
        input.addEventListener("change", function () {
            setAddressRequirements();
            refreshTotals();
        });
    });

    if (deliveryZone) {
        deliveryZone.addEventListener("change", refreshTotals);
    }

    if (couponButton) {
        couponButton.addEventListener("click", refreshTotals);
    }

    form.addEventListener("submit", function (event) {
        let valid = true;
        const messages = [];

        const firstname = form.firstname.value.trim();
        const lastname = form.lastname.value.trim();
        const email = form.email.value.trim();
        const phone = form.phone.value.trim();
        const consent = form.privacy_consent.checked;
        const fulfilment = selectedFulfilment();
        const isDelivery = fulfilment === "delivery";

        if (fulfilmentOptions && !fulfilment) {
            valid = false;
            messages.push("Please choose delivery or pickup before proceeding to payment.");
            updateFulfilmentAnnouncement(true);
        }

        if (firstname.length < 2) {
            valid = false;
            messages.push("First name must be at least 2 characters.");
        }

        if (lastname.length < 2) {
            valid = false;
            messages.push("Last name must be at least 2 characters.");
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            valid = false;
            messages.push("Please enter a valid email address.");
        }

        if (!/^\+44\s?\d{10}$/.test(phone)) {
            valid = false;
            messages.push("Please enter a valid UK phone number (e.g. +44 7123456789).");
        }

        if (isDelivery) {
            const address1 = form.address1.value.trim();
            const city = form.city.value.trim();
            const postcode = form.postcode.value.trim();

            if (address1.length < 5) {
                valid = false;
                messages.push("Address Line 1 must be at least 5 characters.");
            }

            if (city.length < 2) {
                valid = false;
                messages.push("City is required.");
            }

            if (!/^[A-Z]{1,2}\d[A-Z\d]? ?\d[A-Z]{2}$/i.test(postcode)) {
                valid = false;
                messages.push("Please enter a valid UK postcode.");
            }
        }

        if (!consent) {
            valid = false;
            messages.push("You must agree to the Privacy Policy.");
        }

        if (!valid) {
            event.preventDefault();
            alert(messages.join("\n"));
            if (fulfilmentOptions && !fulfilment) {
                fulfilmentOptions.scrollIntoView({ behavior: "smooth", block: "center" });
            }
        }
    });

    setAddressRequirements();
    updateFulfilmentAnnouncement();
});
