/**
 * Global function to toggle password visibility.
 * 
 * @param {string} inputId - The ID of the password input field.
 * @param {HTMLElement|string} btnOrIconId - The button element or the icon element itself.
 */
window.togglePassword = function (inputId, btnOrIconId) {
    const input = document.getElementById(inputId);
    if (!input) return;

    let icon;
    if (typeof btnOrIconId === 'string') {
        icon = document.getElementById(btnOrIconId);
    } else if (btnOrIconId.tagName === 'BUTTON') {
        icon = btnOrIconId.querySelector('i');
    } else {
        icon = btnOrIconId; // Assume it's the icon element directly
    }

    if (!icon) return;

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
};

/**
 * Validates a standard email format.
 */
window.validateEmailFormat = function (mail) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(mail);
};

/**
 * Configure live validation for forms with Email and/or Passwords
 * 
 * @param {Object} options Options object containing input IDs and translated strings
 */
window.setupFormValidation = function (options) {
    const config = {
        emailInputId: null,
        emailHelpId: null,
        passwordInputId: null,
        passwordHelpId: null,
        confirmInputId: null,
        confirmHelpId: null,
        submitBtnId: 'submitBtn',
        passwordMinLength: 8,
        requireComplexPassword: true, // Requires uppercase and number
        ...options
    };

    // Use globally injected localized messages if available, or fallback to english defaults
    const defaultMessages = window.validationMessages || {
        validEmail: '<i class="bi bi-check-circle me-1"></i>Valid email format',
        invalidEmail: '<i class="bi bi-x-circle me-1"></i>Please enter a valid email address',
        passwordInfo: '<i class="bi bi-info-circle me-1"></i>Minimum length required',
        passwordValid: '<i class="bi bi-check-circle me-1"></i>Password meets requirements',
        passwordInvalid: '<i class="bi bi-x-circle me-1"></i>Password does not meet requirements',
        passwordsMatch: '<i class="bi bi-check-circle me-1"></i>Passwords match',
        passwordsNoMatch: '<i class="bi bi-x-circle me-1"></i>Passwords do not match'
    };

    config.messages = { ...defaultMessages, ...(options.messages || {}) };

    const emailInput = config.emailInputId ? document.getElementById(config.emailInputId) : null;
    const emailHelp = config.emailHelpId ? document.getElementById(config.emailHelpId) : null;
    const passwordInput = config.passwordInputId ? document.getElementById(config.passwordInputId) : null;
    const passwordHelp = config.passwordHelpId ? document.getElementById(config.passwordHelpId) : null;
    const confirmInput = config.confirmInputId ? document.getElementById(config.confirmInputId) : null;
    const confirmHelp = config.confirmHelpId ? document.getElementById(config.confirmHelpId) : null;
    const submitBtn = document.getElementById(config.submitBtnId);

    let eValid = emailInput ? (emailInput.value.trim() !== '' && validateEmailFormat(emailInput.value.trim())) : true;
    let pValid = !passwordInput;
    let cValid = !confirmInput;

    let emailTypingTimer;

    function updateSubmitStatus() {
        if (!submitBtn) return;
        
        // Login special case: no confirmation, no complexity required
        if (emailInput && passwordInput && !confirmInput && !config.requireComplexPassword) {
            submitBtn.disabled = !(eValid && passwordInput.value.length > 0);
        } else {
            // General case setup
            // Wait: If password logic is active, pValid must be verified based on complexity.
            // When fields are completely empty initially, handle carefully:
            if (!emailInput && passwordInput && confirmInput && passwordInput.value === '' && confirmInput.value === '') {
                 submitBtn.disabled = true;
                 return;
            }
            submitBtn.disabled = !(eValid && pValid && cValid);
        }
    }

    function validateEmailNow(val) {
        eValid = validateEmailFormat(val);
        if (emailHelp) {
            if (eValid) {
                emailHelp.classList.remove('d-none');
                emailHelp.className = 'form-text text-success small';
                emailHelp.innerHTML = config.messages.validEmail;
            } else {
                emailHelp.classList.remove('d-none');
                emailHelp.className = 'form-text text-danger small';
                emailHelp.innerHTML = config.messages.invalidEmail;
            }
        }
        updateSubmitStatus();
    }

    function handleEmailInput() {
        clearTimeout(emailTypingTimer);
        const val = emailInput.value.trim();
        
        if (val.length === 0) {
            if (emailHelp) emailHelp.classList.add('d-none');
            eValid = false;
            updateSubmitStatus();
            return;
        }

        if (emailHelp) emailHelp.classList.add('d-none');
        emailTypingTimer = setTimeout(() => validateEmailNow(val), 600);
    }

    function handlePasswordInput() {
        if (!passwordInput) return;
        
        const pVal = passwordInput.value;
        const cVal = confirmInput ? confirmInput.value : '';

        // If simple logic (Login)
        if (!confirmInput && !config.requireComplexPassword) {
            pValid = pVal.length > 0;
            updateSubmitStatus();
            return;
        }

        let pwdIsValid = false;
        if (config.requireComplexPassword) {
            const passwordRegex = new RegExp(`^(?=.*[A-Z])(?=.*\\d).{${config.passwordMinLength},}$`);
            pwdIsValid = passwordRegex.test(pVal);
        } else {
            pwdIsValid = pVal.length >= config.passwordMinLength;
        }

        pValid = pwdIsValid;

        if (passwordHelp) {
            if (pwdIsValid) {
                passwordHelp.className = 'form-text text-success small';
                passwordHelp.innerHTML = config.messages.passwordValid;
            } else if (pVal.length > 0) {
                passwordHelp.className = 'form-text text-danger small';
                passwordHelp.innerHTML = config.messages.passwordInvalid;
            } else {
                passwordHelp.className = 'form-text text-muted small';
                passwordHelp.innerHTML = config.messages.passwordInfo;
                pValid = false; // Overwrite
            }
        }

        if (confirmInput) {
            if (cVal.length > 0) {
                if (confirmHelp) confirmHelp.classList.remove('d-none');
                
                if (pVal === cVal) {
                    if (confirmHelp) {
                        confirmHelp.className = 'form-text text-success small';
                        confirmHelp.innerHTML = config.messages.passwordsMatch;
                    }
                    cValid = true;
                } else {
                    if (confirmHelp) {
                        confirmHelp.className = 'form-text text-danger small';
                        confirmHelp.innerHTML = config.messages.passwordsNoMatch;
                    }
                    cValid = false;
                }
            } else {
                if (confirmHelp) confirmHelp.classList.add('d-none');
                cValid = false;
            }
        }
        updateSubmitStatus();
    }

    if (emailInput) {
        emailInput.addEventListener('input', handleEmailInput);
        emailInput.addEventListener('blur', () => {
             clearTimeout(emailTypingTimer);
             const val = emailInput.value.trim();
             if (val.length > 0) validateEmailNow(val);
        });
        if (emailInput.value.trim().length > 0) {
             validateEmailNow(emailInput.value.trim());
        }
    }

    if (passwordInput) passwordInput.addEventListener('input', handlePasswordInput);
    if (confirmInput) confirmInput.addEventListener('input', handlePasswordInput);

    // Initial explicit validation to set button states correctly based on form population
    if (passwordInput) handlePasswordInput();
    updateSubmitStatus();
};
