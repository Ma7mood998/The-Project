// To make sure the script runs only when DOM finished loading
document.addEventListener('DOMContentLoaded', function() {
    // checks if a cookie named "theme" exists and retrieves its value ('dark' or 'light')
    const theme = getCookie('theme');
    
    // If theme cookie exists, apply the theme
    if (theme) {
        document.body.setAttribute('data-theme', theme);
        if (theme === 'dark') {
            document.getElementById('theme-toggle').checked = true;
        }
    } else {
        // Default to light theme if no cookie is found
        document.body.setAttribute('data-theme', 'light');
    }

    // Event listener for the theme toggle checkbox in all pages
    const checkbox = document.getElementById('theme-toggle');
    checkbox.addEventListener('change', function() {
        // If checked "dark" apply theme and store it in the cookie for a year
        if (this.checked) {
            document.body.setAttribute('data-theme', 'dark');
            setCookie('theme', 'dark', 365);
        } else {
            // If not apply default "theme" and store in cookie for a year
            document.body.setAttribute('data-theme', 'light');
            setCookie('theme', 'light', 365); 
        }
    });
});

// Helper functions for cookies

// Sets a cookie with a specified name, value, and expiration
function setCookie(name, value, days) {
    // Create "date" object to calculate expiration time
    const expires = new Date();
    expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
    // Assign cookie string to "document.cookie" for the getCookie function
    document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
}

// Retrieve the value of a cookie by name by splitting each cookie with ";" and iterating over them
function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i].trim();
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}
