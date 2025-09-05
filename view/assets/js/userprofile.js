$(document).ready(function () {

    // Load profile data into the modal form
    function loadUserProfile(userId) {
        $.ajax({
            url: "../app/ajax/getuserprofile.php",
            type: "POST",
            data: { user_id: userId },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    let d = response.data;

                    $("#user_id").val(d.user_id);
                    $("#first_name").val(d.firstname);
                    $("#last_name").val(d.lastname);
                    $("#address1").val(d.address1);
                    $("#address2").val(d.address2);
                    $("#city").val(d.city);
                    $("#county").val(d.county);
                    $("#postcode").val(d.postcode);

                    $("#email").val(d.email); // readonly
                    $("#phone1").val(d.phone);
                    $("#phone2").val(d.phone2);

                    $("#edit_modal").modal("show");
                } else {
                    alert(response.message || "Unable to fetch user profile.");
                }
            },
            error: function () {
                alert("Error fetching user profile.");
            }
        });
    }

    // Format postcode: ensures uppercase + space before last 3 chars
    function formatPostcode(postcode) {
        postcode = postcode.replace(/\s+/g, "").toUpperCase(); // remove spaces, uppercase
        if (postcode.length > 3) {
            return postcode.slice(0, -3) + " " + postcode.slice(-3);
        }
        return postcode;
    }

    // Format phone: converts +44 to 0, keeps spacing neat
    function formatUKPhone(phone) {
        phone = phone.replace(/\s+/g, ""); // strip spaces
        if (phone.startsWith("+44")) {
            phone = "0" + phone.substring(3); // convert +44xxx to 0xxx
        }
        return phone; // return without spaces (can add grouping if required)
    }

    // Validation helper (UK rules)
    function validateProfileForm() {
        let firstName = $("#first_name").val().trim();
        let lastName = $("#last_name").val().trim();
        let phone2 = formatUKPhone($("#phone2").val().trim());
        let postcode = formatPostcode($("#postcode").val().trim());

        // Apply formatting back into inputs
        $("#phone2").val(phone2);
        $("#postcode").val(postcode);

        // Regex patterns
        let phoneRegex = /^(?:0\d{10})$/; // strict: 11 digits starting with 0
        let postcodeRegex = /^([Gg][Ii][Rr] 0[Aa]{2})|((([A-Za-z][0-9]{1,2})|(([A-Za-z][A-Ha-hJ-Yj-y][0-9]{1,2})|(([A-Za-z][0-9][A-Za-z])|([A-Za-z][A-Ha-hJ-Yj-y][0-9][A-Za-z]?))))\s?[0-9][A-Za-z]{2})$/;

        if (firstName === "") {
            alert("First name is required.");
            return false;
        }
        if (lastName === "") {
            alert("Last name is required.");
            return false;
        }
        if (phone2 && !phoneRegex.test(phone2)) {
            alert("Phone number 2 is not a valid UK number.");
            return false;
        }
        if (postcode && !postcodeRegex.test(postcode)) {
            alert("Postcode is not valid. Please enter a valid UK postcode (e.g., SW1A 1AA).");
            return false;
        }

        return true;
    }

    // Handle update submit
    $("#editProfileForm").on("submit", function (e) {
        e.preventDefault();

        if (!validateProfileForm()) {
            return; // stop submission
        }

        $.ajax({
            url: "../app/ajax/updateuserprofile.php",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    alert("Profile updated successfully!");
                    $("#edit_modal").modal("hide");
                    location.reload();
                } else {
                    alert(response.message || "Update failed.");
                }
            },
            error: function () {
                alert("Error updating profile.");
            }
        });
    });

    // Expose loader globally
    window.loadUserProfile = loadUserProfile;
});
