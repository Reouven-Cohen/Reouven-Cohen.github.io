/* Menu mobile (burger) - actif uniquement sous 768px ; sur ordinateur le menu d'origine reste affiche. */
(function () {
  function init() {
    var header = document.querySelector('header');
    var nav = header && header.querySelector('nav.hidden');
    if (!nav || document.getElementById('m-burger')) return;
    var bar = nav.parentElement;

    var home = nav.querySelector('a[href$="index.html"]');
    var base = home ? home.getAttribute('href').replace(/index\.html$/, '') : '/';
    var devis = nav.querySelector('a[href*="devis.html"]');
    var devisHref = devis ? devis.getAttribute('href') : base + 'devis.html';

    var produits = [
      ['Mutuelle Senior', 'pages/mutuelle-senior.html'],
      ['Mutuelle TNS', 'pages/mutuelle-tns.html'],
      ["Mutuelle d'entreprise", 'pages/mutuelle-collective.html'],
      ['Protection Obsèques', 'pages/protection-obseques.html'],
      ['Assurance Prêt', 'pages/assurance-pret.html'],
      ['Chien & Chat', 'pages/assurance-animaux.html']
    ];

    var actions = document.createElement('div');
    actions.className = 'm-actions md:hidden';
    actions.innerHTML =
      (/devis\.html/.test(location.pathname) ? '' : '<a class="m-devis" href="' + devisHref + '">Devis gratuit</a>') +
      '<button id="m-burger" type="button" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="m-menu">' +
        '<span></span><span></span><span></span>' +
      '</button>';
    bar.appendChild(actions);

    var menu = document.createElement('div');
    menu.id = 'm-menu';
    menu.className = 'md:hidden';
    menu.hidden = true;
    var html = '<div class="m-group">';
    nav.querySelectorAll('a').forEach(function (a) {
      var href = a.getAttribute('href');
      if (/^tel:|devis\.html/.test(href)) return;
      html += '<a href="' + href + '">' + a.textContent.trim() + '</a>';
    });
    html += '</div><div class="m-title">Nos assurances</div><div class="m-grid">';
    produits.forEach(function (p) { html += '<a href="' + base + p[1] + '">' + p[0] + '</a>'; });
    html += '</div>' +
      '<a class="m-call" href="tel:0140242020" data-call-location="menu-mobile">Appeler un conseiller · 01 40 24 20 20</a>';
    menu.innerHTML = html;
    header.appendChild(menu);

    var btn = document.getElementById('m-burger');
    function toggle(open) {
      menu.hidden = !open;
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      btn.classList.toggle('is-open', open);
    }
    btn.addEventListener('click', function () { toggle(menu.hidden); });
    menu.addEventListener('click', function (e) { if (e.target.closest('a')) toggle(false); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') toggle(false); });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
