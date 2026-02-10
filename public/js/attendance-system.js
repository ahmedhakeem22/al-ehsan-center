/**
 * Handles the logic for the fingerprint registration page.
 * @param {object} config - Configuration object { registerBtnId, statusMessageId, optionsUrl, verificationUrl, csrfToken, redirectUrl }
 */
function initializeRegistrationPage(config) {
    const registerBtn = document.getElementById(config.registerBtnId);
    const statusMessage = document.getElementById(config.statusMessageId);
    const btnText = document.getElementById('btn-text'); // Assuming this id exists
    const spinner = document.getElementById('spinner');   // Assuming this id exists

    if (!registerBtn) {
        console.error('Registration button not found!');
        return;
    }

    registerBtn.addEventListener('click', async () => {
        // UI feedback
        registerBtn.disabled = true;
        if(btnText) btnText.style.display = 'none';
        if(spinner) spinner.style.display = 'inline-block';
        statusMessage.innerHTML = `<div class="alert alert-info">يرجى اتباع التعليمات التي تظهر على شاشتك لوضع إصبعك على المستشعر...</div>`;

        // Call the core biometric function
        const result = await registerFingerprint(config.optionsUrl, config.verificationUrl, config.csrfToken);

        // UI feedback
        registerBtn.disabled = false;
        if(btnText) btnText.style.display = 'inline-block';
        if(spinner) spinner.style.display = 'none';

        if (result.success) {
            statusMessage.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
            registerBtn.style.display = 'none'; // Hide the button on success
            setTimeout(() => {
                window.location.href = config.redirectUrl; // Redirect on success
            }, 2000);
        } else {
            statusMessage.innerHTML = `<div class="alert alert-danger">${result.message || 'An unknown error occurred.'}</div>`;
        }
    });
}


/**
 * Handles the logic for the main attendance (check-in/check-out) page.
 * @param {object} config - Configuration object { checkInBtnId, checkOutBtnId, statusMessageId, spinnerId, authOptionsUrl, checkInUrl, checkOutUrl, csrfToken }
 */
function initializeAttendancePage(config) {
    const checkInBtn = document.getElementById(config.checkInBtnId);
    const checkOutBtn = document.getElementById(config.checkOutBtnId);
    const statusMessage = document.getElementById(config.statusMessageId);
    const actionSpinner = document.getElementById(config.spinnerId);

    async function handleAttendanceAction(actionUrl, button) {
        if (button) button.style.display = 'none';
        if (actionSpinner) actionSpinner.style.display = 'block';
        statusMessage.innerHTML = '';

        // Call the core biometric function
        const result = await authenticateWithFingerprint(config.authOptionsUrl, actionUrl, config.csrfToken);

        if (result.success) {
            statusMessage.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
            // Reload the page to show the new status
            setTimeout(() => window.location.reload(), 1500);
        } else {
            statusMessage.innerHTML = `<div class="alert alert-danger">${result.message || 'An unknown error occurred.'}</div>`;
            if (button) button.style.display = 'block'; // Show button again on failure
            if (actionSpinner) actionSpinner.style.display = 'none';
        }
    }

    if (checkInBtn) {
        checkInBtn.addEventListener('click', () => handleAttendanceAction(config.checkInUrl, checkInBtn));
    }

    if (checkOutBtn) {
        checkOutBtn.addEventListener('click', () => handleAttendanceAction(config.checkOutUrl, checkOutBtn));
    }
}