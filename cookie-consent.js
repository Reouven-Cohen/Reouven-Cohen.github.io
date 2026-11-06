(function () {
  var STORAGE_KEY = 'ac_cookie_consent';
  var POLICY_URL = '/pages/politique-confidentialite.html';
  var MAX_AGE_MS = 1000 * 60 * 60 * 24 * 182; // choix redemande apres ~6 mois (recommandation CNIL)

  function getConsent() {
    try {
      var c = JSON.parse(localStorage.getItem(STORAGE_KEY));
      if (!c || c.v !== 2 || (c.choice !== 'granted' && c.choice !== 'denied')) return null;
      if (!c.date || Date.now() - new Date(c.date).getTime() > MAX_AGE_MS) return null;
      return c;
    } catch (e) {
      return null;
    }
  }

  function applyConsent(choice) {
    if (typeof window.gtag !== 'function') return;
    var v = choice === 'granted' ? 'granted' : 'denied';
    window.gtag('consent', 'update', {
      ad_storage: v,
      ad_user_data: v,
      ad_personalization: v,
      analytics_storage: v
    });
  }

  function saveConsent(choice) {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify({ v: 2, choice: choice, date: new Date().toISOString() }));
    } catch (e) {}
    applyConsent(choice);
  }

  var banner, modal;

  var BTN_SECONDARY = 'border border-primary-600 text-primary-700 px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-primary-50 transition';
  var BTN_PRIMARY = 'border border-primary-600 bg-primary-600 text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-primary-700 transition';

  function buildBanner() {
    banner = document.createElement('div');
    banner.id = 'cookie-banner';
    banner.setAttribute('role', 'region');
    banner.setAttribute('aria-label', 'Gestion des cookies');
    banner.className = 'fixed inset-x-0 bottom-0 z-[100] p-4';
    banner.innerHTML =
      '<div class="max-w-4xl mx-auto bg-white border border-slate-200 shadow-xl rounded-2xl p-5 md:p-6 flex flex-col md:flex-row items-start md:items-center gap-4">' +
        '<p class="text-sm text-slate-600 leading-relaxed flex-1">' +
          'Avec votre accord, nous utilisons des cookies pour mesurer notre audience et l\'efficacité de nos campagnes publicitaires (Google Ads, Google Analytics). ' +
          'Vous pouvez accepter, refuser ou personnaliser votre choix, et le modifier à tout moment via « Gérer les cookies » en bas de page. ' +
          '<a href="' + POLICY_URL + '" class="text-primary-600 underline hover:text-primary-800">En savoir plus</a>.' +
        '</p>' +
        '<div class="flex flex-wrap items-center gap-3 flex-shrink-0">' +
          '<button type="button" id="cookie-banner-settings" class="text-sm font-semibold text-slate-600 underline hover:text-slate-900 transition">Personnaliser</button>' +
          '<button type="button" id="cookie-banner-deny" class="' + BTN_SECONDARY + '">Tout refuser</button>' +
          '<button type="button" id="cookie-banner-accept" class="' + BTN_PRIMARY + '">Tout accepter</button>' +
        '</div>' +
      '</div>';
    document.body.appendChild(banner);

    document.getElementById('cookie-banner-accept').addEventListener('click', function () {
      saveConsent('granted');
      hideBanner();
    });
    document.getElementById('cookie-banner-deny').addEventListener('click', function () {
      saveConsent('denied');
      hideBanner();
    });
    document.getElementById('cookie-banner-settings').addEventListener('click', openCookieSettings);
  }

  function hideBanner() {
    if (banner && banner.parentNode) banner.parentNode.removeChild(banner);
    banner = null;
  }

  function buildModal() {
    modal = document.createElement('div');
    modal.id = 'cookie-modal';
    modal.className = 'fixed inset-0 z-[110] hidden items-center justify-center p-4';
    modal.innerHTML =
      '<div id="cookie-modal-overlay" class="absolute inset-0 bg-slate-900/60"></div>' +
      '<div role="dialog" aria-modal="true" aria-labelledby="cookie-modal-title" class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[85vh] overflow-y-auto p-6 md:p-8">' +
        '<h2 id="cookie-modal-title" class="text-lg font-bold text-slate-800 mb-4">Gestion des cookies</h2>' +
        '<p class="text-sm text-slate-600 leading-relaxed mb-5">Ce site respecte votre vie privée. Choisissez les cookies que vous acceptez.</p>' +
        '<div class="border border-slate-200 rounded-xl p-4 mb-3">' +
          '<div class="flex items-center justify-between mb-1">' +
            '<span class="font-semibold text-slate-800 text-sm">Cookies strictement nécessaires</span>' +
            '<span class="text-xs font-semibold text-primary-600">Toujours actifs</span>' +
          '</div>' +
          '<p class="text-xs text-slate-500 leading-relaxed">Indispensables au fonctionnement du site : navigation entre les pages, sécurité, envoi du formulaire de devis, mémorisation de votre choix de cookies. Ils ne peuvent pas être désactivés.</p>' +
        '</div>' +
        '<div class="border border-slate-200 rounded-xl p-4 mb-6">' +
          '<label class="flex items-center justify-between gap-3 mb-1 cursor-pointer">' +
            '<span class="font-semibold text-slate-800 text-sm">Cookies de mesure et publicitaires</span>' +
            '<input type="checkbox" id="cookie-modal-ads" class="h-5 w-5 accent-primary-600 cursor-pointer">' +
          '</label>' +
          '<p class="text-xs text-slate-500 leading-relaxed">Utilisés pour mesurer l\'audience du site (Google Analytics) et suivre l\'efficacité de nos campagnes publicitaires (Google Ads).</p>' +
        '</div>' +
        '<div class="flex flex-wrap items-center justify-between gap-3">' +
          '<a href="' + POLICY_URL + '" class="text-sm text-primary-600 hover:underline">Politique de confidentialité</a>' +
          '<button type="button" id="cookie-modal-save" class="' + BTN_PRIMARY + '">Enregistrer mon choix</button>' +
        '</div>' +
      '</div>';
    document.body.appendChild(modal);

    document.getElementById('cookie-modal-save').addEventListener('click', function () {
      saveConsent(document.getElementById('cookie-modal-ads').checked ? 'granted' : 'denied');
      hideBanner();
      closeCookieSettings();
    });
    document.getElementById('cookie-modal-overlay').addEventListener('click', closeCookieSettings);
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeCookieSettings();
    });
  }

  function openCookieSettings() {
    if (!modal) buildModal();
    var c = getConsent();
    document.getElementById('cookie-modal-ads').checked = !!(c && c.choice === 'granted');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  // Fermer sans enregistrer : aucun choix n'est deduit, le bandeau reste affiche s'il n'y a pas de choix.
  function closeCookieSettings() {
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  window.openCookieSettings = openCookieSettings;

  document.addEventListener('DOMContentLoaded', function () {
    if (!getConsent()) buildBanner();
  });
})();
