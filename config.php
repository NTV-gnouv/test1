<?php
// Load biến môi trường từ .env
if (file_exists(__DIR__ . "/.env")) {
    $lines = file(__DIR__ . "/.env", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), "#") === 0) continue;
        list($name, $value) = explode("=", $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

$DB_HOST = $_ENV["DB_HOST"] ?? "localhost";
$DB_USER = $_ENV["DB_USER"] ?? "root";
$DB_PASS = $_ENV["DB_PASS"] ?? "";
$DB_NAME = $_ENV["DB_NAME"] ?? "demo_zalo";

$TELEGRAM_BOT_TOKEN = $_ENV["TELEGRAM_BOT_TOKEN"] ?? "";
$TELEGRAM_CHAT_ID   = $_ENV["TELEGRAM_CHAT_ID"] ?? "";

// Kết nối MySQL (MySQLi, prepared statement để tránh SQL injection)
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("Kết nối DB thất bại: " . $conn->connect_error);
}
?>
