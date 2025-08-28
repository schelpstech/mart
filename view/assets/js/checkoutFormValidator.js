document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("checkoutForm");

    form.addEventListener("submit", function (event) {
        let valid = true;
        let messages = [];

        const firstname = form.firstname.value.trim();
        const lastname = form.lastname.value.trim();
        const email = form.email.value.trim();
        const phone = form.phone.value.trim();
        const address1 = form.address1.value.trim();
        const city = form.city.value.trim();
        const postcode = form.postcode.value.trim();
        const consent = form.privacy_consent.checked;

        // Name validation
        if (firstname.length < 2) {
            valid = false;
            messages.push("First name must be at least 2 characters.");
        }

        if (lastname.length < 2) {
            valid = false;
            messages.push("Last name must be at least 2 characters.");
        }

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            valid = false;
            messages.push("Please enter a valid email address.");
        }

        // Phone validation (UK style)
        const phoneRegex = /^\+44\s?\d{10}$/;
        if (!phoneRegex.test(phone)) {
            valid = false;
            messages.push("Please enter a valid UK phone number (e.g. +44 7123456789).");
        }

        // Address
        if (address1.length < 5) {
            valid = false;
            messages.push("Address Line 1 must be at least 5 characters.");
        }

        if (city.length < 2) {
            valid = false;
            messages.push("City is required.");
        }

        // UK Postcode validation
        const postcodeRegex = /^[A-Z]{1,2}\d[A-Z\d]? ?\d[A-Z]{2}$/i;
        if (!postcodeRegex.test(postcode)) {
            valid = false;
            messages.push("Please enter a valid UK postcode.");
        }

        // Privacy consent
        if (!consent) {
            valid = false;
            messages.push("You must agree to the Privacy Policy.");
        }

        // Prevent submission if invalid
        if (!valid) {
            event.preventDefault();
            alert(messages.join("\n"));
        }
    });
});
