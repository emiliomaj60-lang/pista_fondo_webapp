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

    if (!isset($cipherText)) {
        return null;
    }

    $cipherText = base64_decode($cipherText);

    if ($cipherText === false) {
        return null;
    }

    $plain = openssl_decrypt(
        $cipherText,
        "AES-256-CBC",
        $KEY,
        OPENSSL_RAW_DATA,
        $IV
    );

    return $plain;
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
$host     = decrypt_ws($_POST["host"] ?? null);
$port     = decrypt_ws($_POST["port"] ?? null);
$dbname   = decrypt_ws($_POST["dbname"] ?? null);
$user     = decrypt_ws($_POST["user"] ?? null);
$password = decrypt_ws($_POST["password"] ?? null);
$query    = decrypt_ws($_POST["query"] ?? null);

// =========================
// VALIDATION
// =========================
if (!$host || !$port || !$dbname || !$user || !$password || !$query) {
    echo encrypt_ws(json_encode([
        "errornumber" => 99,
        "errordescr"  => "Invalid or missing parameters"
    ]));
    exit;
}

// =========================
// CONNECT TO MYSQL
// =========================
$conn = new mysqli($host, $user, $password, $dbname, intval($port));

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
// FORMAT RESULT
// =========================
$rows = [];
if ($result !== true) {
    while ($row = $result->fetch_assoc()) {
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
