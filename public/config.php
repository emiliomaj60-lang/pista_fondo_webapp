<?php

// =========================
// AES KEY (SHA256)
// =========================
$KEY = hash('sha256', "postgresql mariadb mysql", true);

// =========================
// AES IV (16 bytes)
// =========================
$IV = chr(3) . chr(1) . chr(4) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0)
    . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0);

// =========================
// DATABASE (Railway)
// =========================
define("DB_HOST", getenv("MYSQLHOST"));
define("DB_USER", getenv("MYSQLUSER"));
define("DB_PASS", getenv("MYSQLPASSWORD"));
define("DB_NAME", getenv("MYSQLDATABASE"));
define("DB_PORT", getenv("MYSQLPORT"));

?>
