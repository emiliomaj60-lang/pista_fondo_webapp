<?php
// NIENTE header JSON: StandFacile si aspetta solo testo criptato
// header("Content-Type: application/json");

// =========================
// AES KEY (SHA256 RAW)
// =========================
$KEY = hash('sha256', "postgresql mariadb mysql", true);

// =========================
// AES IV (16 bytes)
// =========================
$IV = chr(3) . chr(1) . chr(4) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0)
    . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0);

// AES DECRYPT (compatibile con Encrypt_WS + Base64Encode)
function decrypt_ws($cipherTextBase64) {
    global $KEY, $IV;

    if (!isset($cipherTextBase64)) return null;

    $cipherRaw = base64_decode($cipherTextBase64);
    if ($cipherRaw === false) return null;

    return openssl_decrypt(
        $cipherRaw,
        "AES-256-CBC",
        $KEY,
        OPENSSL_RAW_DATA,
        $IV
    );
}

// AES ENCRYPT (compatibile con Decrypt_WS lato C#)
function encrypt_ws($plainText) {
    global $KEY, $IV;

    $encryptedRaw = openssl_encrypt(
        $plainText,
        "AES-256-CBC",
        $KEY,
        OPENSSL_RAW_DATA,
        $IV
    );

    return base64_encode($encryptedRaw);
}

// =========================
// LEGGI PARAMETRI (GET, come rdbCheckConnection)
// =========================
$hostEnc    = $_GET["host"]     ?? null;
$dbnameEnc  = $_GET["dbname"]   ?? null;
$passwordEnc= $_GET["password"] ?? null;
$queryEnc   = $_GET["query"]    ?? null;

// decrypt: prima Base64, poi AES
$host     = decrypt_ws($hostEnc);
$dbname   = decrypt_ws($dbnameEnc);
$password = decrypt_ws($passwordEnc);
$query    = decrypt_ws($queryEnc);

// =========================
// VALIDAZIONE
// =========================
if (!$dbname || !$password || !$query) {
    $resp = [
        "errornumber" => 99,
        "errordescr"  => "Invalid or missing parameters"
    ];
    echo encrypt_ws(json_encode($resp));
    exit;
}

// =========================
/* CONNECT TO MYSQL (Railway) */
// =========================
$DB_HOST = "altaria.proxy.rlwy.net";
$DB_PORT = 40984;

$conn = new mysqli($DB_HOST, "root", $password, $dbname, $DB_PORT);

if ($conn->connect_error) {
    $resp = [
        "errornumber" => 1,
        "errordescr"  => "Connection failed: " . $conn->connect_error
    ];
    echo encrypt_ws(json_encode($resp));
    exit;
}

// =========================
// EXECUTE QUERY
// =========================
$result = $conn->query($query);

if (!$result) {
    $resp = [
        "errornumber" => 2,
        "errordescr"  => $conn->error
    ];
    echo encrypt_ws(json_encode($resp));
    exit;
}

// =========================
// FORMAT RESULT (StandFacile-style)
// =========================
$rows = [];

if ($result !== true) {
    while ($row = $result->fetch_row()) {
        $rows[] = $row;
    }
}

// ⚠️ QUI METTIAMO ANCHE LA STRINGA "NO_DB_ERRORS"
// perché rdbCheckConnection fa solo text.Contains("NO_DB_ERRORS")
$resp = [
    "errornumber" => 0,
    "errordescr"  => "NO_DB_ERRORS",
    "rows"        => $rows
];

echo encrypt_ws(json_encode($resp));

$conn->close();
?>

