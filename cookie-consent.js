(function () {
  var STORAGE_KEY = 'ac_cookie_consent';
  var POLICY_URL = '/pages/politique-confidentialite.html';

  function getConsent() {
    try {
      return JSON.parse(localStorage.getItem(STORAGE_KEY));
    } catch (e) {
      return null;
    }
  }

  function saveConsent() {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify({ ack: true, date: new Date().toISOString() }));
    } catch (e) {}
  }

  var banner, modal;

  function buildBanner() {
    banner = document.createElement('div');
    banner.id = 'cookie-banner';
    banner.setAttribute('role', 'region');
    banner.setAttribute('aria-label', 'Gestion des cookies');
    banner.className = 'fixed inset-x-0 bottom-0 z-[100] p-4';
    banner.innerHTML =
      '<div class="max-w-4xl mx-auto bg-white border border-slate-200 shadow-xl rounded-2xl p-5 md:p-6 flex flex-col md:flex-row items-start md:items-center gap-4">' +
        '<p class="text-sm text-slate-600 leading-relaxed flex-1">' +
          'Nous utilisons uniquement des cookies techniques strictement nécessaires au bon fonctionnement du site (navigation, sécurité, formulaire de devis). ' +
          'Aucun cookie de mesure d\'audience ni publicitaire n\'est utilisé. ' +
          '<a href="' + POLICY_URL + '" class="text-primary-600 hover:underline">En savoir plus</a>.' +
        '</p>' +
        '<div class="flex items-center gap-3 flex-shrink-0">' +
          '<button type="button" id="cookie-banner-settings" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition">Paramètres</button>' +
          '<button type="button" id="cookie-banner-ack" class="bg-primary-600 text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-primary-700 transition">J\'ai compris</button>' +
        '</div>' +
      '</div>';
    document.body.appendChild(banner);

    document.getElementById('cookie-banner-ack').addEventListener('click', function () {
      saveConsent();
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
        '<p class="text-sm text-slate-600 leading-relaxed mb-5">Ce site respecte votre vie privée. Voici le détail des cookies utilisés lors de votre navigation.</p>' +
        '<div class="border border-slate-200 rounded-xl p-4 mb-3">' +
          '<div class="flex items-center justify-between mb-1">' +
            '<span class="font-semibold text-slate-800 text-sm">Cookies strictement nécessaires</span>' +
            '<span class="text-xs font-semibold text-primary-600">Toujours actifs</span>' +
          '</div>' +
          '<p class="text-xs text-slate-500 leading-relaxed">Indispensables au fonctionnement du site : navigation entre les pages, sécurité, envoi du formulaire de devis. Ils ne peuvent pas être désactivés.</p>' +
        '</div>' +
        '<p class="text-xs text-slate-500 leading-relaxed mb-6">Aucun cookie de mesure d\'audience, publicitaire ou de réseau social n\'est déposé sur ce site.</p>' +
        '<div class="flex items-center justify-between gap-3">' +
          '<a href="' + POLICY_URL + '" class="text-sm text-primary-600 hover:underline">Politique de confidentialité</a>' +
          '<button type="button" id="cookie-modal-close" class="bg-primary-600 text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-primary-700 transition">Fermer</button>' +
        '</div>' +
      '</div>';
    document.body.appendChild(modal);

    document.getElementById('cookie-modal-close').addEventListener('click', closeCookieSettings);
    document.getElementById('cookie-modal-overlay').addEventListener('click', closeCookieSettings);
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeCookieSettings();
    });
  }

  function openCookieSettings() {
    if (!modal) buildModal();
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeCookieSettings() {
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    saveConsent();
    hideBanner();
  }

  window.openCookieSettings = openCookieSettings;

  document.addEventListener('DOMContentLoaded', function () {
    if (!getConsent()) buildBanner();
  });
})();
