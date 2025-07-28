// js/login.js
function loadCaptcha(id, type) {
    fetch(`captcha.php?type=${type}`)
        .then(res => res.text())
        .then(text => document.getElementById(id).innerText = text);
}

document.addEventListener("DOMContentLoaded", () => {
    loadCaptcha("captcha-login-text", "captcha_login");
    loadCaptcha("captcha-signup-text", "captcha_signup");
});

document.addEventListener('DOMContentLoaded', function() {
    // Get references to the tab buttons
    const showSignupBtn = document.getElementById('show-signup');
    const showLoginBtn = document.getElementById('show-login');

    // Get references to the forms
    const signupForm = document.querySelector('.signup-form');
    const loginForm = document.querySelector('.login-form');

    // Get references to the switch links within the forms
    const switchLoginLink = document.getElementById('switch-login');
    const switchSignupLink = document.getElementById('switch-signup');

    // Get references to password toggle icons
    const passwordToggleIcons = document.querySelectorAll('.toggle-password');

    // Function to show the signup form
    function showSignupForm() {
        signupForm.classList.remove('hidden');
        loginForm.classList.add('hidden');
        showSignupBtn.classList.add('active');
        showLoginBtn.classList.remove('active');
    }

    // Function to show the login form
    function showLoginForm() {
        loginForm.classList.remove('hidden');
        signupForm.classList.add('hidden');
        showLoginBtn.classList.add('active');
        showSignupBtn.classList.remove('active');
    }

    // Event listeners for tab buttons
    if (showSignupBtn) {
        showSignupBtn.addEventListener('click', showSignupForm);
    }
    if (showLoginBtn) {
        showLoginBtn.addEventListener('click', showLoginForm);
    }

    // Event listeners for switch links
    if (switchLoginLink) {
        switchLoginLink.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default link behavior
            showLoginForm();
        });
    }
    if (switchSignupLink) {
        switchSignupLink.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default link behavior
            showSignupForm();
        });
    }

    // Event listeners for password toggle icons
    passwordToggleIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const passwordInput = this.previousElementSibling; // The input field is the previous sibling
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            }
        });
    });

    // Handle redirection with success/error messages
    const urlParams = new URLSearchParams(window.location.search);

    // Prioritize showing signup if there were signup-specific errors
    if (urlParams.get('error') === 'signup' || (urlParams.has('errors') && urlParams.has('old_input'))) {
        showSignupForm();
    } 
    // Prioritize showing login if there was a successful registration OR login errors
    else if (urlParams.get('registered') === 'true' || urlParams.get('signup_success') === 'true' || urlParams.get('error') === 'login' || (urlParams.has('errors') && urlParams.has('old_input_login'))) {
        showLoginForm();
    }
    // No specific URL parameters, so let the HTML default take over (which is now login)
    // No explicit JS call needed here, as the HTML already sets the initial state correctly.
});
