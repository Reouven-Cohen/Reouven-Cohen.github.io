(function () {
  // Libelle de conversion Google Ads "Appel depuis le site" (a renseigner une fois l'action creee dans Google Ads).
  // Format attendu : 'AW-436792714/XXXXXXXXXXXX'. Vide = seul l'evenement GA4 clic_telephone est envoye.
  var PHONE_CONVERSION = '';
  var PHONE_HREF = 'tel:0140242020';
  var PHONE_LABEL = '01 40 24 20 20';

  document.addEventListener('click', function (e) {
    var a = e.target && e.target.closest ? e.target.closest('a[href^="tel:"]') : null;
    if (!a || typeof window.gtag !== 'function') return;
    window.gtag('event', 'clic_telephone', {
      phone_number: a.getAttribute('href').replace('tel:', ''),
      page_path: location.pathname,
      emplacement: a.getAttribute('data-call-location') || 'page'
    });
    if (PHONE_CONVERSION) window.gtag('event', 'conversion', { send_to: PHONE_CONVERSION });
  });

  // Bouton "Appeler" fixe en bas d'ecran, sur mobile uniquement.
  function addStickyCallButton() {
    if (document.getElementById('sticky-call')) return;
    var bar = document.createElement('div');
    bar.id = 'sticky-call';
    bar.className = 'md:hidden fixed inset-x-0 bottom-0 z-[90] px-4 pt-2 pb-3 bg-white/95 border-t border-slate-200 shadow-[0_-4px_16px_rgba(15,23,42,0.08)]';
    bar.style.paddingBottom = 'max(0.75rem, env(safe-area-inset-bottom))';
    bar.innerHTML =
      '<a href="' + PHONE_HREF + '" data-call-location="bouton-fixe-mobile" ' +
        'class="flex items-center justify-center gap-3 w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold text-lg rounded-full py-3.5 transition" ' +
        'aria-label="Appeler un conseiller au ' + PHONE_LABEL + '">' +
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true">' +
          '<path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24 11.36 11.36 0 003.57.57 1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.45.57 3.57a1 1 0 01-.25 1.01l-2.2 2.21z"/>' +
        '</svg>' +
        '<span>Appeler un conseiller</span>' +
      '</a>';
    document.body.appendChild(bar);
    document.body.classList.add('pb-24', 'md:pb-0');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', addStickyCallButton);
  } else {
    addStickyCallButton();
  }
})();
