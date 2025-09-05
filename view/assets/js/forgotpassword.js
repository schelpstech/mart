$("#forgotPasswordForm").on("submit", function(e) {
    e.preventDefault();

    let $btn = $(this).find("button[type=submit]");
    $btn.prop("disabled", true).text("Sending...");

    $.ajax({
        url: "../app/ajax/forgot_password.php",
        type: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function(response) {
            $("#forgotMessage").html(
                `<div class='alert alert-${response.success ? "success" : "danger"}'>
                    ${response.message}
                </div>`
            );
        },
        error: function() {
            $("#forgotMessage").html("<div class='alert alert-danger'>Something went wrong.</div>");
        },
        complete: function() {
            // Always re-enable the button
            $btn.prop("disabled", false).text("Send Reset Link");
        }
    });
});
