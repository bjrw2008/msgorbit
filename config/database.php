<?php
session_start();

PGHOST = dpg-d74hh9h4tr6s73cpafn0-a
PGPORT = 5432
PGUSER = bj_auto_db_user
PGPASSWORD = SHJroyk6rwE8NALG5HWV4Ssx9RPi1pUN
PGDATABASE = bj_auto_db

try {
    $pdo = new PDO(
        "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
