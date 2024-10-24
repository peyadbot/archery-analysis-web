<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();

$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

define('BASE_URL', 'http://localhost/archery-analysis-web/');
define('COMP_LIST_URL', 'https://ianseo.sukanfc.com/fetch_comp_list.php');
define('COMP_SCORE_URL', 'https://ianseo.sukanfc.com/fetch_comp_score.php');
define('TRAIN_LIST_URL', 'https://ianseo.sukanfc.com/fetch_train_list.php');
define('TRAIN_SCORE_URL', 'https://ianseo.sukanfc.com/fetch_train_score.php');