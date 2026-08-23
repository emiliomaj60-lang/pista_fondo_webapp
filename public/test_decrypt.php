<?php
$KEY = hash('sha256', "postgresql mariadb mysql", true);
$IV  = chr(3).chr(1).chr(4).chr(0).chr(0).chr(0).chr(0).chr(0)
     . chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0);

function decrypt_ws($cipherTextBase64) {
    global $KEY, $IV;
    $cipherRaw = base64_decode($cipherTextBase64);
    return openssl_decrypt($cipherRaw, "AES-256-CBC", $KEY, OPENSSL_RAW_DATA, $IV);
}

echo decrypt_ws("iM1rKm/4tfCLGVWavkWNwQ==");
