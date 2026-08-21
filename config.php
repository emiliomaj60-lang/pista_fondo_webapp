<?php
// AES KEY + IV identical to StandFacile
$KEY = hash("sha256", "postgresql mariadb mysql", true);
$IV  = chr(3).chr(1).chr(4).str_repeat(chr(0), 13);

// Railway DB credentials
define("DB_HOST", getenv("MYSQLHOST"));
define("DB_USER", getenv("MYSQLUSER"));
define("DB_PASS", getenv("MYSQLPASSWORD"));
define("DB_NAME", getenv("MYSQLDATABASE"));
?>
