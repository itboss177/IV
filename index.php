<?php
// простой разбор .env без parse_ini_file
$env = array();
$envFile = __DIR__ . '/.env';
if (is_readable($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos($line, '=') === false) continue;
        list($k, $v) = explode('=', $line, 2);
        $env[trim($k)] = trim($v);
    }
}

if (empty($env['DB_HOST'])) { die('Не прочитан .env'); }

$dsn = 'mysql:host='.$env['DB_HOST'].';port='.$env['DB_PORT'].';dbname='.$env['DB_NAME'].';charset=utf8';
$pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASS'], array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
));

echo '<h1 style="font-size:48px;margin:16px 0;font-family:Arial,Helvetica,sans-serif;">HELLO RYZHIY!!! HOW ARE YOU???</h1>';
echo 'OK: '.$pdo->query('SELECT NOW()')->fetchColumn();
