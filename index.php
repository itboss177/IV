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
      overflow: hidden;
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
      position: relative;
      z-index: 2;
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
    .rocket {
      position: fixed;
      width: 6px;
      height: 16px;
      background: var(--accent);
      border-radius: 8px;
      box-shadow: 0 0 12px rgba(227,27,35,0.6);
      transform: translate(-50%, -50%);
      animation: rocket-move 0.65s cubic-bezier(0.18, 0.6, 0.35, 1) forwards;
      pointer-events: none;
      z-index: 6;
    }
    .rocket::after {
      content: '';
      position: absolute;
      bottom: -6px;
      left: 50%;
      width: 4px;
      height: 10px;
      background: linear-gradient(180deg, rgba(255,255,255,0.8), rgba(227,27,35,0));
      transform: translateX(-50%);
      filter: blur(1px);
    }
    @keyframes rocket-move {
      to {
        transform: translate(calc(-50% + var(--dx)), calc(-50% + var(--dy)));
        opacity: 0.95;
      }
    }
    .particle {
      position: fixed;
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: var(--accent);
      filter: drop-shadow(0 0 6px rgba(227,27,35,0.9));
      transform: translate(-50%, -50%);
      animation: particle-burst 0.9s ease-out forwards;
      pointer-events: none;
      z-index: 6;
    }
    @keyframes particle-burst {
      to {
        transform: translate(calc(-50% + var(--dx)), calc(-50% + var(--dy))) scale(0.6);
        opacity: 0;
      }
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

      function rand(min, max) {
        return Math.random() * (max - min) + min;
      }

      function spawnParticles(x, y) {
        for (var i = 0; i < 18; i++) {
          var angle = Math.random() * Math.PI * 2;
          var speed = rand(40, 120);
          var dx = Math.cos(angle) * speed;
          var dy = Math.sin(angle) * speed;
          var p = document.createElement('span');
          p.className = 'particle';
          p.style.left = x + 'px';
          p.style.top = y + 'px';
          p.style.setProperty('--dx', dx + 'px');
          p.style.setProperty('--dy', dy + 'px');
          document.body.appendChild(p);
          setTimeout(function(el) { el.remove(); }, 950, p);
        }
      }

      function createRocket() {
        var bodyRect = document.body.getBoundingClientRect();
        var cx = bodyRect.width / 2;
        var cy = bodyRect.height / 2;
        var angle = Math.random() * Math.PI * 2;
        var distance = rand(180, 340);
        var dx = Math.cos(angle) * distance;
        var dy = Math.sin(angle) * distance - rand(40, 120);

        var rocket = document.createElement('div');
        rocket.className = 'rocket';
        rocket.style.left = cx + 'px';
        rocket.style.top = cy + 'px';
        rocket.style.setProperty('--dx', dx + 'px');
        rocket.style.setProperty('--dy', dy + 'px');
        document.body.appendChild(rocket);

        setTimeout(function() {
          var tx = cx + dx;
          var ty = cy + dy;
          spawnParticles(tx, ty);
          rocket.remove();
        }, 650);
      }

      function launchFireworks() {
        for (var i = 0; i < 6; i++) {
          setTimeout(createRocket, i * 90);
        }
      }

      if (btn && counter) {
        btn.addEventListener('click', function() {
          count += 1;
          counter.textContent = count;
          launchFireworks();
        });
      }
    })();
  </script>
</body>
</html>
HTML;
