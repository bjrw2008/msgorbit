<?php
session_start();

// Database credentials from Render
$PGHOST = 'dpg-d74hh9h4tr6s73cpafn0-a';
$PGPORT = '5432';
$PGUSER = 'bj_auto_db_user';
$PGPASSWORD = 'SHJroyk6rwE8NALG5HWV4Ssx9RPi1pUN';
$PGDATABASE = 'bj_auto_db';

try {
    $pdo = new PDO(
        "pgsql:host=" . $PGHOST . ";port=" . $PGPORT . ";dbname=" . $PGDATABASE . ";",
        $PGUSER,
        $PGPASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Test connection
    // echo "Connected successfully";
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
