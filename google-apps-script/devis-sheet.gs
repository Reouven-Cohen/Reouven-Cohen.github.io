/**
 * Enregistre chaque demande de devis du site assu-conseil.com dans l'onglet "Devis".
 * A coller dans Extensions > Apps Script du Google Sheet, puis deployer en "Application Web"
 * (Executer en tant que : Moi ; Qui a acces : Tout le monde).
 */
var IGNORER = ['access_key', 'botcheck', 'from_name'];

function doPost(e) {
  var lock = LockService.getScriptLock();
  lock.waitLock(10000);
  try {
    var p = (e && e.parameters) || {};
    if (p.botcheck && p.botcheck[0]) return ContentService.createTextOutput('ignored');

    var ss = SpreadsheetApp.getActiveSpreadsheet();
    var sh = ss.getSheetByName('Devis') || ss.insertSheet('Devis');

    var entetes = sh.getLastColumn() > 0
      ? sh.getRange(1, 1, 1, sh.getLastColumn()).getValues()[0]
      : ['Date'];
    Object.keys(p).forEach(function (k) {
      if (IGNORER.indexOf(k) < 0 && entetes.indexOf(k) < 0) entetes.push(k);
    });
    sh.getRange(1, 1, 1, entetes.length).setValues([entetes]).setFontWeight('bold');
    sh.setFrozenRows(1);

    var ligne = entetes.map(function (h) {
      if (h === 'Date') return new Date();
      var v = p[h] ? p[h].join(', ') : '';
      return v ? "'" + v : '';   // texte brut : garde le 0 des telephones, bloque les formules
    });
    sh.appendRow(ligne);
    return ContentService.createTextOutput('ok');
  } finally {
    lock.releaseLock();
  }
}
