<?php

// AES KEY & IV (metti i tuoi valori reali)
$KEY = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";   // 32 bytes
$IV  = "XXXXXXXXXXXXXXXX";                  // 16 bytes

// Database Railway
define("DB_HOST", getenv("MYSQLHOST"));
define("DB_USER", getenv("MYSQLUSER"));
define("DB_PASS", getenv("MYSQLPASSWORD"));
define("DB_NAME", getenv("MYSQLDATABASE"));
define("DB_PORT", getenv("MYSQLPORT"));

?>
