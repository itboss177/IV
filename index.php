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
echo '<div style="margin:8px 0;font-family:Arial,Helvetica,sans-serif;">OK: '.$pdo->query('SELECT NOW()')->fetchColumn().'</div>';

echo <<<HTML
<button id="blue-btn" style="padding:12px 20px;font-size:16px;background:#1e6de0;color:#fff;border:none;border-radius:6px;cursor:pointer;box-shadow:0 2px 6px rgba(0,0,0,0.15);">
    Синяя кнопка
</button>
<div style="margin-top:10px;font-size:16px;font-family:Arial,Helvetica,sans-serif;">
    Счётчик: <span id="click-count">0</span>
</div>
<p style="font-family:Arial,Helvetica,sans-serif;font-size:14px;margin-top:8px;">Нажми на кнопку - получишь результат</p>
<script>
  (function() {
    var btn = document.getElementById('blue-btn');
    var counter = document.getElementById('click-count');
    var count = 0;
    if (btn && counter) {
      btn.addEventListener('click', function() {
        count += 1;
        counter.textContent = count;
      });
    }
  })();
</script>
HTML;
