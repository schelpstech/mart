document.addEventListener("DOMContentLoaded", function () {
    const resetPage = document.getElementById("resetpasswordpage");
    if (!resetPage) return; // Exit if not on reset password page

    const form = document.getElementById("resetPasswordForm");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm_password");
    const passwordHelp = document.getElementById("passwordHelp");
    const confirmHelp = document.getElementById("confirmHelp");
    const showPassword = document.getElementById("showPassword");

    // ---- Toggle password visibility ----
    if (showPassword && password && confirmPassword) {
        showPassword.addEventListener("change", function () {
            const type = this.checked ? "text" : "password";
            password.type = type;
            confirmPassword.type = type;
        });
    }

    // ---- Validation function ----
    function validatePassword(pwd) {
        const minLength = /.{8,}/;
        const upper = /[A-Z]/;
        const lower = /[a-z]/;
        const number = /[0-9]/;
        const symbol = /[^A-Za-z0-9]/;
        return (
            minLength.test(pwd) &&
            upper.test(pwd) &&
            lower.test(pwd) &&
            number.test(pwd) &&
            symbol.test(pwd)
        );
    }

    // ---- Form submit validation ----
    if (form && password && confirmPassword) {
        form.addEventListener("submit", function (e) {
            let valid = true;
            passwordHelp.textContent = "";
            confirmHelp.textContent = "";

            if (!validatePassword(password.value)) {
                passwordHelp.textContent =
                    "Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.";
                valid = false;
            }

            if (password.value !== confirmPassword.value) {
                confirmHelp.textContent = "Passwords do not match.";
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }
});
