document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");
    const passwordStatus = document.getElementById("passwordStatus");

    if (toggle && passwordInput && passwordStatus) {
        toggle.addEventListener("click", function () {
            const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
            passwordInput.setAttribute("type", type);

            // Ganti teks dan ikon
            passwordStatus.textContent = type === "password" ? "Show" : "Hide";

            const icon = toggle.querySelector("span.fas");
            if (icon) {
                icon.classList.toggle("fa-eye");
                icon.classList.toggle("fa-eye-slash");
            }
        });
    }
});
