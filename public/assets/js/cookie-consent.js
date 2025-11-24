(function () {
  const consentRoot = document.getElementById('customCookieConsent');
  const acceptBtn = consentRoot.querySelector('.custom-cookie-btn.accept');
  const rejectBtn = consentRoot.querySelector('.custom-cookie-btn.reject');
  const preferencesBtn = consentRoot.querySelector('.custom-cookie-btn.preferences');
  const links = consentRoot.querySelector('.custom-cookie-links');
  const modal = consentRoot.querySelector('.custom-cookie-preferences-modal');
  const closeModalBtn = consentRoot.querySelector('.custom-cookie-close');
  const savePreferencesBtn = consentRoot.querySelector('.custom-cookie-btn.save-preferences');
  const analyticsCheckbox = consentRoot.querySelector('.analytics-category');
  const marketingCheckbox = consentRoot.querySelector('.marketing-category');

  const COOKIE_NAME = 'my_custom_cookie_consent';
  const PREFS_NAME = 'my_custom_cookie_preferences';
  const COOKIE_DAYS = 365;

  function setCookie(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
    document.cookie =
      name + '=' + encodeURIComponent(value) + '; expires=' + date.toUTCString() + '; path=/; SameSite=Lax';
  }
  function getCookie(name) {
    const nameEQ = name + '=';
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == ' ') c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
    }
    return null;
  }
  function showConsent() {
    consentRoot.style.display = 'flex';
  }
  function hideConsent() {
    consentRoot.style.display = 'none';
  }
  function showPreferences() {
    modal.style.display = 'flex';
  }
  function hidePreferences() {
    modal.style.display = 'none';
  }
  function setPreferences(prefs) {
    setCookie(PREFS_NAME, JSON.stringify(prefs), COOKIE_DAYS);
  }
  function getPreferences() {
    const val = getCookie(PREFS_NAME);
    if (!val) return null;
    try {
      return JSON.parse(val);
    } catch {
      return null;
    }
  }
  // Show banner if not accepted/rejected
  const consent = getCookie(COOKIE_NAME);
  if (!consent) {
    showConsent();
  }
  // Accept all
  acceptBtn.addEventListener('click', function () {
    setCookie(COOKIE_NAME, 'accepted', COOKIE_DAYS);
    setPreferences({ analytics: true, marketing: true });
    hideConsent();
  });
  // Reject all
  rejectBtn.addEventListener('click', function () {
    setCookie(COOKIE_NAME, 'rejected', COOKIE_DAYS);
    setPreferences({ analytics: false, marketing: false });
    hideConsent();
  });
  // Open preferences
  preferencesBtn.addEventListener('click', function () {
    // Set checkboxes from saved prefs if available
    const prefs = getPreferences();
    if (prefs) {
      analyticsCheckbox.checked = !!prefs.analytics;
      marketingCheckbox.checked = !!prefs.marketing;
    } else {
      analyticsCheckbox.checked = false;
      marketingCheckbox.checked = false;
    }
    showPreferences();
  });
  // Close preferences modal
  closeModalBtn.addEventListener('click', function () {
    hidePreferences();
  });
  // Save preferences
  savePreferencesBtn.addEventListener('click', function () {
    const prefs = {
      analytics: analyticsCheckbox.checked,
      marketing: marketingCheckbox.checked
    };
    setPreferences(prefs);
    setCookie(COOKIE_NAME, 'custom', COOKIE_DAYS);
    hidePreferences();
    hideConsent();
  });
  // Hide modal on outside click
  modal.addEventListener('click', function (e) {
    if (e.target === modal) hidePreferences();
  });
})();
