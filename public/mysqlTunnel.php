<?php

// =========================
// AES KEY (SHA256 RAW)
// =========================
$KEY = hash('sha256', "postgresql mariadb mysql", true);

// =========================
// AES IV (16 bytes)
// =========================
$IV = chr(3).chr(1).chr(4).chr(0).chr(0).chr(0).chr(0).chr(0)
    .chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0);

// AES DECRYPT
function decrypt_ws($cipherTextBase64) {
    global $KEY, $IV;
    if (!$cipherTextBase64) return null;
    $cipherRaw = base64_decode($cipherTextBase64);
    if ($cipherRaw === false) return null;
    return openssl_decrypt($cipherRaw, "AES-256-CBC", $KEY, OPENSSL_RAW_DATA, $IV);
}

// AES ENCRYPT
function encrypt_ws($plainText) {
    global $KEY, $IV;
    $cipherRaw = openssl_encrypt($plainText, "AES-256-CBC", $KEY, OPENSSL_RAW_DATA, $IV);
    return base64_encode($cipherRaw);
}

// =========================
// LEGGI PARAMETRI (GET!)
// =========================
$hostEnc     = $_GET["host"]     ?? null;
$dbnameEnc   = $_GET["dbname"]   ?? null;
$passwordEnc = $_GET["password"] ?? null;
$queryEnc    = $_GET["query"]    ?? null;

// decrypt
$host     = decrypt_ws($hostEnc);
$dbname   = decrypt_ws($dbnameEnc);
$password = decrypt_ws($passwordEnc);
$query    = decrypt_ws($queryEnc);

// =========================
// VALIDAZIONE
// =========================
if (!$dbname || !$password || !$query) {
    echo encrypt_ws("PARAM_ERROR");
    exit;
}

// =========================
// CONNECT TO MYSQL (Railway)
// =========================
$DB_HOST = "altaria.proxy.rlwy.net";
$DB_PORT = 40984;

$conn = new mysqli($DB_HOST, "root", $password, $dbname, $DB_PORT);

if ($conn->connect_error) {
    echo encrypt_ws("CONN_ERROR");
    exit;
}

// =========================
// EXECUTE QUERY
// =========================
$result = $conn->query($query);

if (!$result) {
    echo encrypt_ws("QUERY_ERROR");
    exit;
}

// =========================
// RISPOSTA PER STANDFACILE
// =========================
echo encrypt_ws("NO_DB_ERRORS");

$conn->close();
?>
