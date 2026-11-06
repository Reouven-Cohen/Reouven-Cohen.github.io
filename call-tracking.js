(function () {
  // Libelle de conversion Google Ads "Appel depuis le site" (a renseigner une fois l'action creee dans Google Ads).
  // Format attendu : 'AW-436792714/XXXXXXXXXXXX'. Vide = seul l'evenement GA4 clic_telephone est envoye.
  var PHONE_CONVERSION = '';

  document.addEventListener('click', function (e) {
    var a = e.target && e.target.closest ? e.target.closest('a[href^="tel:"]') : null;
    if (!a || typeof window.gtag !== 'function') return;
    window.gtag('event', 'clic_telephone', {
      phone_number: a.getAttribute('href').replace('tel:', ''),
      page_path: location.pathname
    });
    if (PHONE_CONVERSION) window.gtag('event', 'conversion', { send_to: PHONE_CONVERSION });
  });
})();
