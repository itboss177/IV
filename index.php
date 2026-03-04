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

$now = $pdo->query('SELECT NOW()')->fetchColumn();

header('Content-Type: text/html; charset=utf-8');
echo <<<HTML
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700&family=Onest:wght@400;500&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg-1: #0f172a;
      --bg-2: #111827;
      --accent: #e31b23;
      --text: #e2e8f0;
      --muted: #94a3b8;
      --card: rgba(255,255,255,0.06);
      --card-border: rgba(255,255,255,0.14);
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      min-height: 100vh;
      display: grid;
      place-items: center;
      background: radial-gradient(circle at 18% 25%, rgba(227,27,35,0.24), transparent 38%),
                  radial-gradient(circle at 82% 78%, rgba(59,130,246,0.22), transparent 34%),
                  linear-gradient(135deg, var(--bg-1), var(--bg-2));
      color: var(--text);
      font-family: 'Onest', 'Manrope', system-ui, -apple-system, sans-serif;
      padding: 32px;
    }
    .card {
      width: min(560px, 100%);
      background: var(--card);
      border: 1px solid var(--card-border);
      border-radius: 18px;
      padding: 28px;
      box-shadow: 0 20px 70px rgba(0,0,0,0.35), inset 0 1px 0 rgba(255,255,255,0.08);
      backdrop-filter: blur(6px);
      text-align: center;
    }
    h1 {
      margin: 0 0 14px;
      font-size: 34px;
      letter-spacing: 0.5px;
      font-family: 'Manrope', 'Onest', sans-serif;
    }
    .db-time {
      margin: 6px 0 24px;
      color: var(--muted);
      font-size: 15px;
    }
    .btn {
      padding: 13px 24px;
      font-size: 17px;
      font-weight: 700;
      background: transparent;
      color: var(--accent);
      border: 2px solid var(--accent);
      border-radius: 10px;
      cursor: pointer;
      box-shadow: 0 8px 24px rgba(227,27,35,0.25);
      transition: transform 0.1s ease, box-shadow 0.2s ease, background 0.2s ease, color 0.2s ease;
    }
    .btn:hover {
      transform: translateY(-1px);
      background: rgba(227,27,35,0.08);
      box-shadow: 0 12px 28px rgba(227,27,35,0.3);
    }
    .btn:active {
      transform: translateY(0);
      box-shadow: 0 6px 18px rgba(227,27,35,0.25) inset;
    }
    .counter {
      margin-top: 14px;
      font-size: 17px;
    }
    .hint {
      margin-top: 10px;
      font-size: 15px;
      color: var(--muted);
    }
  </style>
</head>
<body>
  <div class="card">
    <h1>HELLO RYZHIY!!! HOW ARE YOU???</h1>
    <div class="db-time">OK: {$now}</div>
    <button id="blue-btn" class="btn">Пуск ракеты</button>
    <div class="counter">Счётчик: <span id="click-count">0</span></div>
    <div class="hint">Нажми на кнопку - получишь результат</div>
  </div>

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
</body>
</html>
HTML;
