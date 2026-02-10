// A simple helper library to work with ArrayBuffers -> Base64URL
// This is necessary because WebAuthn uses ArrayBuffers, which are not JSON-serializable.
const { bufferDecode, bufferEncode } = (() => {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';
    const lookup = new Uint8Array(256);
    for (let i = 0; i < chars.length; i++) {
        lookup[chars.charCodeAt(i)] = i;
    }

    const bufferEncode = (buffer) => {
        const bytes = new Uint8Array(buffer);
        let i, len = bytes.length, base64url = '';
        for (i = 0; i < len; i += 3) {
            let a = bytes[i], b = bytes[i+1], c = bytes[i+2];
            base64url += chars[a >> 2];
            base64url += chars[((a & 3) << 4) | (b >> 4)];
            base64url += chars[((b & 15) << 2) | (c >> 6)];
            base64url += chars[c & 63];
        }
        if (len % 3 === 2) {
            base64url = base64url.substring(0, base64url.length - 1) + '=';
        } else if (len % 3 === 1) {
            base64url = base64url.substring(0, base64url.length - 2) + '==';
        }
        return base64url.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
    };

    const bufferDecode = (base64string) => {
        base64string = base64string.replace(/-/g, '+').replace(/_/g, '/');
        const len = base64string.length;
        let bufferLength = len * 0.75,
            p = 0,
            encoded1, encoded2, encoded3, encoded4;

        if (base64string[len - 1] === '=') bufferLength--;
        if (base64string[len - 2] === '=') bufferLength--;
        
        const arraybuffer = new ArrayBuffer(bufferLength),
            bytes = new Uint8Array(arraybuffer);

        for (let i = 0; i < len; i+=4) {
            encoded1 = lookup[base64string.charCodeAt(i)];
            encoded2 = lookup[base64string.charCodeAt(i+1)];
            encoded3 = lookup[base64string.charCodeAt(i+2)];
            encoded4 = lookup[base64string.charCodeAt(i+3)];
            bytes[p++] = (encoded1 << 2) | (encoded2 >> 4);
            bytes[p++] = ((encoded2 & 15) << 4) | (encoded3 >> 2);
            bytes[p++] = ((encoded3 & 3) << 6) | (encoded4 & 63);
        }
        return arraybuffer;
    };

    return { bufferDecode, bufferEncode };
})();


/**
 * Registers a new fingerprint credential.
 * @param {string} registrationOptionsUrl - The URL to fetch registration options from.
 * @param {string} verificationUrl - The URL to send the verification data to.
 * @param {string} csrfToken - The CSRF token.
 * @returns {Promise<{success: boolean, message: string}>}
 */
async function registerFingerprint(registrationOptionsUrl, verificationUrl, csrfToken) {
    try {
        // 1. Fetch registration options from the server
        const response = await fetch(registrationOptionsUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
        });
        const options = await response.json();
        
        if (options.error) throw new Error(options.error);

        // 2. Decode challenge and user id from base64url
        options.challenge = bufferDecode(options.challenge);
        options.user.id = bufferDecode(options.user.id);
        
        // 3. Prompt the user to use their security key/fingerprint
        const credential = await navigator.credentials.create({ publicKey: options });

        // 4. Prepare the credential data to be sent to the server for verification
        const attestationResponse = {
            id: credential.id,
            rawId: bufferEncode(credential.rawId),
            type: credential.type,
            response: {
                attestationObject: bufferEncode(credential.response.attestationObject),
                clientDataJSON: bufferEncode(credential.response.clientDataJSON),
            },
        };

        // 5. Send the data to the server for verification
        const verificationResponse = await fetch(verificationUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(attestationResponse),
        });

        return await verificationResponse.json();

    } catch (error) {
        console.error('Registration Error:', error);
        return { success: false, message: 'فشل تسجيل البصمة. تأكد من أن متصفحك يدعم هذه الميزة وأنك تستخدم HTTPS. ' + error.message };
    }
}


/**
 * Authenticates using an existing fingerprint credential.
 * @param {string} authOptionsUrl - The URL to fetch authentication options from.
 * @param {string} checkInUrl - The URL to send the verification data to (e.g., check-in/out).
 * @param {string} csrfToken - The CSRF token.
 * @returns {Promise<{success: boolean, message: string}>}
 */
async function authenticateWithFingerprint(authOptionsUrl, verificationUrl, csrfToken) {
    try {
        // 1. Fetch authentication options (challenge) from the server
        const response = await fetch(authOptionsUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
        });
        const options = await response.json();

        if (options.error) throw new Error(options.error);

        // 2. Decode challenge and any credential IDs from base64url
        options.challenge = bufferDecode(options.challenge);
        if (options.allowCredentials) {
            options.allowCredentials.forEach(cred => {
                cred.id = bufferDecode(cred.id);
            });
        }

        // 3. Prompt the user to authenticate
        const assertion = await navigator.credentials.get({ publicKey: options });

        // 4. Prepare assertion data to be sent to the server
        const assertionResponse = {
            id: assertion.id,
            rawId: bufferEncode(assertion.rawId),
            type: assertion.type,
            response: {
                authenticatorData: bufferEncode(assertion.response.authenticatorData),
                clientDataJSON: bufferEncode(assertion.response.clientDataJSON),
                signature: bufferEncode(assertion.response.signature),
                userHandle: assertion.response.userHandle ? bufferEncode(assertion.response.userHandle) : null,
            },
        };

        // 5. Send assertion to the server to complete the action (e.g., check-in)
        const verificationResponse = await fetch(verificationUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(assertionResponse),
        });

        return await verificationResponse.json();

    } catch (error) {
        console.error('Authentication Error:', error);
        return { success: false, message: 'فشل التحقق من البصمة: ' + error.message };
    }
}