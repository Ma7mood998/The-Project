document.addEventListener('DOMContentLoaded', function() {
    // Check if a theme cookie is set
    const theme = getCookie('theme');
    
    // If theme cookie exists, apply the theme
    if (theme) {
        document.body.setAttribute('data-theme', theme);
        if (theme === 'dark') {
            document.getElementById('theme-toggle').checked = true;
        }
    } else {
        // Default to light theme
        document.body.setAttribute('data-theme', 'light');
    }

    // Event listener for the theme toggle checkbox
    const checkbox = document.getElementById('theme-toggle');
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            document.body.setAttribute('data-theme', 'dark');
            setCookie('theme', 'dark', 365);  // Set the cookie for dark theme
        } else {
            document.body.setAttribute('data-theme', 'light');
            setCookie('theme', 'light', 365);  // Set the cookie for light theme
        }
    });
});

// Helper functions for cookies
function setCookie(name, value, days) {
    const expires = new Date();
    expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000)); // Expiration in days
    document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
}

function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i].trim();
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}
