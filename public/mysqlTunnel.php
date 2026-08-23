<?php
header("Content-Type: application/json");

// =========================
// AES KEY (SHA256 RAW)
// =========================
$KEY = hash('sha256', "postgresql mariadb mysql", true);

// =========================
// AES IV (16 bytes)
// =========================
$IV = chr(3) . chr(1) . chr(4) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0)
    . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0);

// AES DECRYPT
function decrypt_ws($cipherText) {
    global $KEY, $IV;

    if (!isset($cipherText)) return null;

    $cipherText = base64_decode($cipherText);
    if ($cipherText === false) return null;

    return openssl_decrypt(
        $cipherText,
        "AES-256-CBC",
        $KEY,
        OPENSSL_RAW_DATA,
        $IV
    );
}

// AES ENCRYPT
function encrypt_ws($plainText) {
    global $KEY, $IV;

    $encrypted = openssl_encrypt(
        $plainText,
        "AES-256-CBC",
        $KEY,
        OPENSSL_RAW_DATA,
        $IV
    );

    return base64_encode($encrypted);
}

// =========================
// READ POST (DECRYPT)
// =========================
$dbname   = decrypt_ws($_POST["dbname"] ?? null);
$password = decrypt_ws($_POST["password"] ?? null);
$query    = decrypt_ws($_POST["query"] ?? null);

// =========================
// VALIDATION
// =========================
if (!$dbname || !$password || !$query) {
    echo encrypt_ws(json_encode([
        "errornumber" => 99,
        "errordescr"  => "Invalid or missing parameters"
    ]));
    exit;
}

// =========================
// CONNECT TO MYSQL (Railway)
// =========================

// ⚠️ SOSTITUISCI QUESTO VALORE CON IL TUO DB HOST DI RAILWAY
$DB_HOST = "containers-us-west-123.railway.app";

$conn = new mysqli($DB_HOST, "root", $password, $dbname);

if ($conn->connect_error) {
    echo encrypt_ws(json_encode([
        "errornumber" => 1,
        "errordescr"  => "Connection failed: " . $conn->connect_error
    ]));
    exit;
}

// =========================
// EXECUTE QUERY
// =========================
$result = $conn->query($query);

if (!$result) {
    echo encrypt_ws(json_encode([
        "errornumber" => 2,
        "errordescr"  => $conn->error
    ]));
    exit;
}

// =========================
// FORMAT RESULT (StandFacile format)
// =========================
$rows = [];

if ($result !== true) {
    while ($row = $result->fetch_row()) {
        $rows[] = $row;
    }
}

echo encrypt_ws(json_encode([
    "errornumber" => 0,
    "errordescr"  => "",
    "rows"        => $rows
]));

$conn->close();
?>
