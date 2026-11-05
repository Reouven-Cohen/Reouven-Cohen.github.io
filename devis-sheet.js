// Copie de chaque demande de devis dans le Google Sheet (en plus de l'email Web3Forms).
// URL de l'application Web Google Apps Script (voir google-apps-script/devis-sheet.gs).
window.DEVIS_SHEET_URL = '';
window.saveDevis = function (form) {
  try {
    if (!window.DEVIS_SHEET_URL) return;
    var data = new URLSearchParams(new FormData(form));
    data.append('page', location.pathname);
    fetch(window.DEVIS_SHEET_URL, { method: 'POST', mode: 'no-cors', body: data, keepalive: true }).catch(function () {});
  } catch (e) {}
};
