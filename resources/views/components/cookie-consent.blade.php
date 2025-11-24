<div class="custom-cookie-consent-root" id="customCookieConsent" style="display:none;">
    <div class="custom-cookie-modal">
        <div class="custom-cookie-content">
            <h2 class="custom-cookie-title">Cookie Disclaimer</h2>
            <p class="custom-cookie-desc">
                This website uses cookies to enhance your browsing experience, analyze site traffic, and personalize
                content. By continuing to use this site, you consent to our use of cookies.
            </p>
            <div class="custom-cookie-actions">
                <button class="custom-cookie-btn accept">Accept</button>
                <button class="custom-cookie-btn reject">Reject</button>
                {{-- <button class="custom-cookie-btn preferences">Manage Preferences</button> --}}
            </div>
            {{-- <div class="custom-cookie-links">
                <a href="/privacy-policy" target="_blank">Privacy Policy</a>
                <span>|</span>
                <a href="/terms-and-conditions" target="_blank">Terms and Conditions</a>
            </div> --}}
        </div>
    </div>
    <!-- Preferences Modal (hidden by default) -->
    <div class="custom-cookie-preferences-modal" id="customCookiePreferences" style="display:none;">
        <div class="custom-cookie-modal-content">
            <div class="custom-cookie-modal-header">
                <h3>Cookie Preferences</h3>
                <button class="custom-cookie-close">&times;</button>
            </div>
            <div class="custom-cookie-modal-body">
                <p>You can customize your cookie preferences below.</p>
                <div class="custom-cookie-category">
                    <label>
                        <input type="checkbox" checked disabled>
                        <span>Essential Cookies (always on)</span>
                    </label>
                    <p class="custom-cookie-category-desc">These cookies are essential for the website to function
                        properly.</p>
                </div>
                <div class="custom-cookie-category">
                    <label>
                        <input type="checkbox" class="analytics-category">
                        <span>Analytics Cookies</span>
                    </label>
                    <p class="custom-cookie-category-desc">These cookies help us understand how visitors interact with
                        our website.</p>
                </div>
                <div class="custom-cookie-category">
                    <label>
                        <input type="checkbox" class="marketing-category">
                        <span>Marketing Cookies</span>
                    </label>
                    <p class="custom-cookie-category-desc">These cookies are used for advertising and tracking purposes.
                    </p>
                </div>
            </div>
            <div class="custom-cookie-modal-footer">
                <button class="custom-cookie-btn save-preferences">Save preferences</button>
            </div>
        </div>
    </div>
</div>
