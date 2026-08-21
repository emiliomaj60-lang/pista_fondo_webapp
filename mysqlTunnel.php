<?php
header("Content-Type: application/json");

// =========================
// CONFIG
// =========================
require_once("config.php");

// =========================
// AES DECRYPT
// =========================
function decrypt_ws($cipherText) {
    global $KEY, $IV;

    $cipherText = base64_decode($cipherText);
    return openssl_decrypt(
        $cipherText,
        "AES-256-CBC",
        $KEY,
        OPENSSL_RAW_DATA,
        $IV
    );
}

// =========================
// AES ENCRYPT
// =========================
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
// READ POST
// =========================
$host      = decrypt_ws($_POST["host"]);
$dbname    = decrypt_ws($_POST["dbname"]);
$user      = decrypt_ws($_POST["user"]);
$password  = decrypt_ws($_POST["password"]);
$query     = decrypt_ws($_POST["query"]);

// =========================
// CONNECT TO MYSQL
// =========================
$conn = new mysqli($host, $user, $password, $dbname);

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
