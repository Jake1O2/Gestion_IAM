<?php
/**
 * Connexion centrale PDO — base de données gestion_system
 */

$host    = 'localhost';
$db      = 'gestion_system';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    @file_put_contents(__DIR__ . '/../logs/db_errors.log', date('c') . " - DB connection error: " . $e->getMessage() . "\n", FILE_APPEND);
    die("Erreur de connexion à la base de données. Vérifiez que MySQL est démarré et que la base 'gestion_system' existe.");
}
