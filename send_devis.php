<?php
ob_start();

function respondJson(int $statusCode, array $payload): void
{
    if (!headers_sent()) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
    }

    $json = json_encode($payload);
    if ($json === false) {
        $json = '{"success":false,"message":"Erreur serveur inattendue."}';
    }

    if (ob_get_length()) {
        ob_clean();
    }

    echo $json;
    exit;
}

set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function (Throwable $throwable): void {
    error_log('send_devis exception: ' . $throwable->getMessage() . ' in ' . $throwable->getFile() . ':' . $throwable->getLine());
    respondJson(500, [
        'success' => false,
        'message' => 'Erreur serveur lors du traitement du devis. Veuillez nous appeler au 01.40.24.20.20.',
    ]);
});

register_shutdown_function(function (): void {
    $error = error_get_last();
    if ($error === null) {
        return;
    }

    $fatalErrors = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    if (!in_array($error['type'], $fatalErrors, true)) {
        return;
    }

    error_log('send_devis fatal: ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line']);

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
    }

    if (ob_get_length()) {
        ob_clean();
    }

    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur lors du traitement du devis. Veuillez nous appeler au 01.40.24.20.20.',
    ]);
});

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, ['success' => false, 'message' => 'Methode non autorisee.']);
}

loadEnvFile(__DIR__ . '/.env');

function clean(string $value): string
{
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

function loadEnvFile(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $delimiterPosition = strpos($line, '=');
        if ($delimiterPosition === false) {
            continue;
        }

        $name = trim(substr($line, 0, $delimiterPosition));
        $value = trim(substr($line, $delimiterPosition + 1));

        if ($name === '') {
            continue;
        }

        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

function isValidDate(string $date): bool
{
    if (!preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $matches)) {
        return false;
    }

    return checkdate((int) $matches[2], (int) $matches[1], (int) $matches[3]);
}

function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isValidPhone(string $phone): bool
{
    $phone = preg_replace('/[\s\.\-]/', '', $phone);
    return (bool) preg_match('/^(0|\+33)[1-9][0-9]{8}$/', $phone);
}

function isValidPostal(string $postalCode): bool
{
    return (bool) preg_match('/^\d{5}$/', $postalCode);
}

function getEnvValue(string $name, string $default = ''): string
{
    $value = getenv($name);
    if ($value === false) {
        return $default;
    }

    $value = trim($value);
    return $value === '' ? $default : $value;
}

function postJson(string $url, array $payload, array $headers = []): array
{
    $json = json_encode($payload);
    if ($json === false) {
        return ['ok' => false, 'status' => 0, 'body' => '', 'error' => 'JSON encoding failed.'];
    }

    if (function_exists('curl_init')) {
        $requestHeaders = array_merge(['Content-Type: application/json'], $headers);
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $requestHeaders,
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $responseBody = curl_exec($ch);
        if ($responseBody === false) {
            $error = curl_error($ch);
            curl_close($ch);

            return ['ok' => false, 'status' => 0, 'body' => '', 'error' => $error];
        }

        $statusInfoKey = defined('CURLINFO_RESPONSE_CODE') ? CURLINFO_RESPONSE_CODE : CURLINFO_HTTP_CODE;
        $status = (int) curl_getinfo($ch, $statusInfoKey);
        curl_close($ch);

        return [
            'ok' => $status >= 200 && $status < 300,
            'status' => $status,
            'body' => $responseBody,
            'error' => '',
        ];
    }

    $requestHeaders = implode("\r\n", array_merge(['Content-Type: application/json'], $headers));
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => $requestHeaders,
            'content' => $json,
            'ignore_errors' => true,
            'timeout' => 20,
        ],
    ]);

    $responseBody = @file_get_contents($url, false, $context);
    if ($responseBody === false) {
        $error = error_get_last();

        return [
            'ok' => false,
            'status' => 0,
            'body' => '',
            'error' => $error['message'] ?? 'HTTP request failed.',
        ];
    }

    $status = 0;
    if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $matches)) {
        $status = (int) $matches[1];
    }

    return [
        'ok' => $status >= 200 && $status < 300,
        'status' => $status,
        'body' => $responseBody,
        'error' => '',
    ];
}

function sendViaMailjet(array $config, array $message): array
{
    if ($config['api_key'] === '' || $config['secret_key'] === '') {
        return [
            'success' => false,
            'error' => 'Mailjet credentials are incomplete. Generate the Secret Key and set MAILJET_SECRET_KEY.',
        ];
    }

    $payload = [
        'Messages' => [[
            'From' => [
                'Email' => $config['from_email'],
                'Name' => $config['from_name'],
            ],
            'To' => [[
                'Email' => $config['to_email'],
                'Name' => $config['to_name'],
            ]],
            'ReplyTo' => [
                'Email' => $message['reply_to_email'],
                'Name' => $message['reply_to_name'],
            ],
            'Subject' => $message['subject'],
            'TextPart' => $message['text_part'],
            'HTMLPart' => $message['html_part'],
        ]],
    ];

    $response = postJson(
        'https://api.mailjet.com/v3.1/send',
        $payload,
        ['Authorization: Basic ' . base64_encode($config['api_key'] . ':' . $config['secret_key'])]
    );

    $decoded = json_decode($response['body'], true);
    if ($response['ok'] && isset($decoded['Messages'][0]['Status']) && strtolower((string) $decoded['Messages'][0]['Status']) === 'success') {
        return ['success' => true, 'error' => ''];
    }

    $error = $response['error'] !== '' ? $response['error'] : 'Mailjet API request failed.';
    if (is_array($decoded) && isset($decoded['Messages'][0]['Errors'][0]['ErrorMessage'])) {
        $error = (string) $decoded['Messages'][0]['Errors'][0]['ErrorMessage'];
    } elseif (is_array($decoded) && isset($decoded['ErrorMessage'])) {
        $error = (string) $decoded['ErrorMessage'];
    }

    return ['success' => false, 'error' => $error];
}

$allowedProduits = [
    'mutuelle-senior' => 'Mutuelle Senior',
    'mutuelle-tns' => 'Mutuelle TNS',
    'mutuelle-entreprise' => "Mutuelle d'entreprise",
    'protection-obseques' => 'Protection Obsèques',
    'assurance-pret' => 'Assurance Prêt',
];

$dateNaissance = clean($_POST['dateNaissance'] ?? '');
$produit = clean($_POST['produit'] ?? '');
$regime = clean($_POST['regime'] ?? '');
$assureConjoint = clean($_POST['assureConjoint'] ?? 'non');
$prenom = clean($_POST['prenom'] ?? '');
$nom = clean($_POST['nom'] ?? '');
$codePostal = clean($_POST['codePostal'] ?? '');
$telephone = clean($_POST['telephone'] ?? '');
$courriel = clean($_POST['courriel'] ?? '');
$ageMoyenSalaries = clean($_POST['ageMoyenSalaries'] ?? '');
$nomEntreprise = clean($_POST['nomEntreprise'] ?? '');
$nomConventionCollective = clean($_POST['nomConventionCollective'] ?? '');
$montantCapital = clean($_POST['montantCapital'] ?? '');
$describeBesoins = clean($_POST['describeBesoins'] ?? '');

$isEntreprise = ($produit === 'mutuelle-entreprise');

$dateNaissanceConjoint = '';
$regimeConjoint = '';
if (!$isEntreprise && $assureConjoint === 'oui') {
    $dateNaissanceConjoint = clean($_POST['dateNaissanceConjoint'] ?? '');
    $regimeConjoint = clean($_POST['regimeConjoint'] ?? '');
}

$assureEnfants = clean($_POST['assureEnfants'] ?? 'non');
$nombreEnfants = '';
if ($assureEnfants === 'oui') {
    $nombreEnfants = clean($_POST['nombreEnfants'] ?? '');
}

$produitLabel = $allowedProduits[$produit] ?? '';
$errors = [];

if (!$isEntreprise) {
    if ($dateNaissance === '') {
        $errors[] = 'La date de naissance est requise.';
    } elseif (!isValidDate($dateNaissance)) {
        $errors[] = 'La date de naissance est invalide (JJ/MM/AAAA).';
    }
}

if ($produit === '') {
    $errors[] = 'Le type d\'assurance est requis.';
} elseif (!isset($allowedProduits[$produit])) {
    $errors[] = 'Le type d\'assurance selectionne est invalide.';
}

if ($regime === '') {
    $errors[] = 'Le regime est requis.';
}

if (!$isEntreprise && $assureConjoint === 'oui') {
    if ($dateNaissanceConjoint === '') {
        $errors[] = 'La date de naissance du conjoint est requise.';
    } elseif (!isValidDate($dateNaissanceConjoint)) {
        $errors[] = 'La date de naissance du conjoint est invalide.';
    }

    if ($regimeConjoint === '') {
        $errors[] = 'Le regime du conjoint est requis.';
    }
}

if ($assureEnfants === 'oui' && $nombreEnfants === '') {
    $errors[] = 'Veuillez indiquer le nombre d\'enfants a assurer.';
}

if ($prenom === '') {
    $errors[] = 'Le prenom est requis.';
}

if ($nom === '') {
    $errors[] = 'Le nom est requis.';
}

if ($codePostal === '') {
    $errors[] = 'Le code postal est requis.';
} elseif (!isValidPostal($codePostal)) {
    $errors[] = 'Le code postal est invalide.';
}

if ($telephone === '') {
    $errors[] = 'Le telephone est requis.';
} elseif (!isValidPhone($telephone)) {
    $errors[] = 'Le numero de telephone est invalide.';
}

if ($courriel === '') {
    $errors[] = 'L\'adresse email est requise.';
} elseif (!isValidEmail($courriel)) {
    $errors[] = 'L\'adresse email est invalide.';
}

if (!empty($errors)) {
    respondJson(422, ['success' => false, 'message' => implode(' ', $errors)]);
}

$now = (new DateTime())->format('d/m/Y \a H:i');

$infoNaissanceRow = $isEntreprise
    ? "<tr style='background:#f8fafc;'><td style='padding:8px 12px;font-weight:600;width:50%;'>Âge moyen des salariés :</td><td style='padding:8px 12px;'>{$ageMoyenSalaries}</td></tr>"
    : "<tr style='background:#f8fafc;'><td style='padding:8px 12px;font-weight:600;width:50%;'>Date de naissance :</td><td style='padding:8px 12px;'>{$dateNaissance}</td></tr>";

$entrepriseRows = '';
if ($isEntreprise) {
    if ($nomEntreprise !== '') {
        $entrepriseRows .= "<tr><td style='padding:8px 12px;font-weight:600;'>Nom de l'entreprise :</td><td style='padding:8px 12px;'>{$nomEntreprise}</td></tr>";
    }
    if ($nomConventionCollective !== '') {
        $entrepriseRows .= "<tr style='background:#f8fafc;'><td style='padding:8px 12px;font-weight:600;'>Convention collective :</td><td style='padding:8px 12px;'>{$nomConventionCollective}</td></tr>";
    }
}

$capitalRow = $montantCapital !== ''
    ? "<tr><td style='padding:8px 12px;font-weight:600;'>Montant capital obsèques :</td><td style='padding:8px 12px;'>{$montantCapital}</td></tr>"
    : '';

$describeBesoinsRow = $describeBesoins !== ''
    ? "<tr style='background:#f8fafc;'><td style='padding:8px 12px;font-weight:600;vertical-align:top;'>Besoins :</td><td style='padding:8px 12px;'>" . nl2br($describeBesoins) . "</td></tr>"
    : '';

$conjointInfo = $assureConjoint === 'oui'
    ? "<tr><td style='padding:8px 12px;font-weight:600;width:50%;'><strong>Date naissance conjoint :</strong></td><td style='padding:8px 12px;'>{$dateNaissanceConjoint}</td></tr>
       <tr style='background:#f8fafc;'><td style='padding:8px 12px;font-weight:600;'><strong>Regime conjoint :</strong></td><td style='padding:8px 12px;'>{$regimeConjoint}</td></tr>"
    : "<tr><td colspan='2' style='padding:8px 12px;color:#64748b;'>Aucun conjoint a assurer</td></tr>";

$enfantsInfo = $assureEnfants === 'oui' && $nombreEnfants !== ''
    ? "<tr><td style='padding:8px 12px;font-weight:600;width:50%;'>Nombre d'enfants :</td><td style='padding:8px 12px;'>{$nombreEnfants}</td></tr>"
    : "<tr><td colspan='2' style='padding:8px 12px;color:#64748b;'>Aucun enfant a assurer</td></tr>";

$htmlBody = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Inter,Arial,sans-serif;">
  <div style="max-width:600px;margin:0 auto;padding:32px 16px;">
    <div style="background:linear-gradient(135deg,#1e40af,#2563eb);border-radius:16px 16px 0 0;padding:32px;text-align:center;">
      <div style="display:inline-flex;align-items:center;justify-content:center;width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:12px;margin-bottom:12px;">
        <span style="font-size:24px;">AC</span>
      </div>
      <h1 style="margin:0;color:#fff;font-size:22px;font-weight:800;">Nouvelle demande de devis</h1>
      <p style="margin:8px 0 0;color:#bfdbfe;font-size:14px;">Recue le {$now}</p>
    </div>

    <div style="background:#fff;padding:32px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
      <h2 style="margin:0 0 16px;font-size:16px;color:#1e3a8a;border-bottom:2px solid #dbeafe;padding-bottom:8px;">Informations personnelles</h2>
      <table style="width:100%;border-collapse:collapse;font-size:14px;color:#334155;">
        <tr style="background:#f8fafc;"><td style="padding:8px 12px;font-weight:600;width:50%;">Assurance demandee :</td><td style="padding:8px 12px;">{$produitLabel}</td></tr>
        <tr><td style="padding:8px 12px;font-weight:600;">Prenom :</td><td style="padding:8px 12px;">{$prenom}</td></tr>
        <tr style="background:#f8fafc;"><td style="padding:8px 12px;font-weight:600;">Nom :</td><td style="padding:8px 12px;">{$nom}</td></tr>
        <tr><td style="padding:8px 12px;font-weight:600;">Telephone :</td><td style="padding:8px 12px;">{$telephone}</td></tr>
        <tr style="background:#f8fafc;"><td style="padding:8px 12px;font-weight:600;">Email :</td><td style="padding:8px 12px;"><a href="mailto:{$courriel}" style="color:#2563eb;">{$courriel}</a></td></tr>
        <tr><td style="padding:8px 12px;font-weight:600;">Code postal :</td><td style="padding:8px 12px;">{$codePostal}</td></tr>
      </table>

      <h2 style="margin:24px 0 16px;font-size:16px;color:#1e3a8a;border-bottom:2px solid #dbeafe;padding-bottom:8px;">Informations</h2>
      <table style="width:100%;border-collapse:collapse;font-size:14px;color:#334155;">
        {$infoNaissanceRow}
        <tr><td style="padding:8px 12px;font-weight:600;">Regime :</td><td style="padding:8px 12px;">{$regime}</td></tr>
        {$entrepriseRows}
        {$capitalRow}
        {$describeBesoinsRow}
      </table>

      <h2 style="margin:24px 0 16px;font-size:16px;color:#1e3a8a;border-bottom:2px solid #dbeafe;padding-bottom:8px;">Conjoint(e)</h2>
      <table style="width:100%;border-collapse:collapse;font-size:14px;color:#334155;">
        <tr style="background:#f8fafc;"><td style="padding:8px 12px;font-weight:600;width:50%;">Assurer le conjoint :</td><td style="padding:8px 12px;">{$assureConjoint}</td></tr>
        {$conjointInfo}
      </table>

      <h2 style="margin:24px 0 16px;font-size:16px;color:#1e3a8a;border-bottom:2px solid #dbeafe;padding-bottom:8px;">Enfant(s)</h2>
      <table style="width:100%;border-collapse:collapse;font-size:14px;color:#334155;">
        <tr style="background:#f8fafc;"><td style="padding:8px 12px;font-weight:600;width:50%;">Assurer des enfants :</td><td style="padding:8px 12px;">{$assureEnfants}</td></tr>
        {$enfantsInfo}
      </table>
    </div>

    <div style="background:#f1f5f9;border:1px solid #e2e8f0;border-top:none;border-radius:0 0 16px 16px;padding:20px 32px;text-align:center;">
      <p style="margin:0;font-size:12px;color:#64748b;">
        Email envoye automatiquement depuis le formulaire de devis d'<strong>assu-conseil.com</strong><br>
        Ne pas repondre directement a cet email - contacter le demandeur a <a href="mailto:{$courriel}" style="color:#2563eb;">{$courriel}</a>
      </p>
    </div>
  </div>
</body>
</html>
HTML;

$textBody = "Nouvelle demande de devis {$produitLabel}\n"
    . "Prenom: {$prenom}\n"
    . "Nom: {$nom}\n"
    . "Telephone: {$telephone}\n"
    . "Email: {$courriel}\n"
    . "Code postal: {$codePostal}\n"
    . ($isEntreprise ? "Age moyen des salaries: {$ageMoyenSalaries}\n" : "Date de naissance: {$dateNaissance}\n")
    . "Regime: {$regime}\n"
    . ($nomEntreprise !== '' ? "Nom de l'entreprise: {$nomEntreprise}\n" : '')
    . ($nomConventionCollective !== '' ? "Convention collective: {$nomConventionCollective}\n" : '')
    . ($montantCapital !== '' ? "Montant capital obseques: {$montantCapital}\n" : '')
    . (!$isEntreprise ? "Conjoint: {$assureConjoint}\n" : '')
    . ($dateNaissanceConjoint !== '' ? "Date naissance conjoint: {$dateNaissanceConjoint}\n" : '')
    . ($regimeConjoint !== '' ? "Regime conjoint: {$regimeConjoint}\n" : '')
    . "Enfants: {$assureEnfants}\n"
    . ($nombreEnfants !== '' ? "Nombre d'enfants: {$nombreEnfants}\n" : '')
    . ($describeBesoins !== '' ? "Besoins: {$describeBesoins}\n" : '');

$mailjetConfig = [
    'api_key' => getEnvValue('MAILJET_API_KEY', 'cf05daeafebc35169500f4d064a79d17'),
    'secret_key' => getEnvValue('MAILJET_SECRET_KEY'),
    'from_email' => getEnvValue('MAILJET_FROM_EMAIL', 'assu.conseil@orange.fr'),
    'from_name' => getEnvValue('MAILJET_FROM_NAME', 'Assu-Conseil'),
    'to_email' => getEnvValue('MAILJET_TO_EMAIL', 'nqrypro@gmail.com'),
    'to_name' => getEnvValue('MAILJET_TO_NAME', 'Assu-Conseil'),
];

$result = sendViaMailjet($mailjetConfig, [
    'reply_to_email' => $courriel,
    'reply_to_name' => trim("{$prenom} {$nom}"),
    'subject' => "Nouvelle demande de devis {$produitLabel} - {$prenom} {$nom}",
    'text_part' => $textBody,
    'html_part' => $htmlBody,
]);

if ($result['success']) {
    respondJson(200, ['success' => true, 'message' => 'Votre demande a ete envoyee avec succes.']);
}

error_log('Mailjet error: ' . $result['error']);
$isLocalRequest = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)
    || in_array($_SERVER['SERVER_NAME'] ?? '', ['127.0.0.1', 'localhost'], true);
$frontendMessage = 'Erreur lors de l\'envoi de l\'email. Veuillez nous appeler au 01.40.24.20.20.';

if ($isLocalRequest && str_contains($result['error'], 'MAILJET_SECRET_KEY')) {
    $frontendMessage = 'Configuration Mailjet incomplete : ajoutez la Secret Key dans `site_assu_conseil/.env` via `MAILJET_SECRET_KEY=...`.';
}

respondJson(500, [
    'success' => false,
    'message' => $frontendMessage,
]);

