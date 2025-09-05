document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("registerForm");
    if (!form) return; // Exit if registration form is not on the page

    // ---- Human verification (simple math captcha) ----
    const num1 = document.getElementById("num1");
    const num2 = document.getElementById("num2");
    const humanAnswer = document.getElementById("humanAnswer");
    const humanVerified = document.getElementById("humanVerified");

    if (num1 && num2 && humanAnswer && humanVerified) {
        let a = Math.floor(Math.random() * 10) + 1;
        let b = Math.floor(Math.random() * 10) + 1;

        num1.textContent = a;
        num2.textContent = b;

        humanAnswer.addEventListener("input", function () {
            if (parseInt(humanAnswer.value) === a + b) {
                humanVerified.disabled = false;
                humanAnswer.disabled = true;
            } else {
                humanVerified.disabled = true;
                humanVerified.checked = false;
            }
        });
    }

    // ---- Password validation ----
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm_password");
    const passwordMsg = document.getElementById("passwordMsg");
    const confirmMsg = document.getElementById("confirmMsg");

    function validatePasswordStrength(pass) {
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        return regex.test(pass);
    }

    if (password && confirmPassword) {
        password.addEventListener("input", () => {
            if (!validatePasswordStrength(password.value)) {
                passwordMsg.textContent =
                    "Password must be at least 8 chars, include uppercase, lowercase, number, and symbol.";
            } else {
                passwordMsg.textContent = "";
            }
        });

        confirmPassword.addEventListener("input", () => {
            if (password.value !== confirmPassword.value) {
                confirmMsg.textContent = "Passwords do not match.";
            } else {
                confirmMsg.textContent = "";
            }
        });

        form.addEventListener("submit", function (e) {
            if (!validatePasswordStrength(password.value)) {
                e.preventDefault();
                alert("Password does not meet requirements.");
            } else if (password.value !== confirmPassword.value) {
                e.preventDefault();
                alert("Passwords do not match.");
            }
        });
    }
});
