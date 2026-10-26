<?php
$dbHost = getenv("DB_HOST") ?: "db";
$dbName = getenv("DB_DATABASE") ?: "gusti_db";
$dbUser = getenv("DB_USERNAME") ?: "gusti";
$dbPass = getenv("DB_PASSWORD") ?: "change_this_password";

$message = "";
$error = "";

try {
    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );

    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        message VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $input = trim($_POST["message"] ?? "");
        if ($input === "") {
            $error = "Pesan tidak boleh kosong.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO messages (message) VALUES (?)");
            $stmt->execute([$input]);
            $message = "Data berhasil disimpan ke database.";
        }
    }

    $messages = $pdo->query("SELECT message, created_at FROM messages ORDER BY id DESC LIMIT 10")->fetchAll();
} catch (Throwable $e) {
    $messages = [];
    $error = "Database belum siap atau tidak dapat diakses.";
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Web Gusti</title>
<style>
*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;font-family:Arial,sans-serif;background:#f3f6fb;color:#1f2937}
.card{width:min(720px,100%);background:#fff;border-radius:18px;padding:36px;box-shadow:0 12px 35px rgba(0,0,0,.1)}
h1{margin-top:0}.badge{display:inline-block;padding:7px 12px;border-radius:999px;background:#eef2ff;color:#4338ca;font-size:13px;font-weight:bold}
form{display:flex;gap:10px;margin:24px 0}input{flex:1;padding:12px;border:1px solid #d1d5db;border-radius:10px}
button{border:0;border-radius:10px;padding:12px 18px;background:#2563eb;color:#fff;font-weight:bold;cursor:pointer}
.ok{color:#15803d}.err{color:#b91c1c}li{margin-bottom:8px}small{color:#6b7280}
</style>
</head>
<body>
<main class="card">
<h1>Halo 👋</h1>
<p>Ini web <strong>Gusti</strong>, berjalan menggunakan Docker.</p>
<span class="badge">PHP <?= htmlspecialchars(PHP_VERSION) ?></span>
<?php if ($message): ?><p class="ok"><?= htmlspecialchars($message) ?></p><?php endif; ?>
<?php if ($error): ?><p class="err"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<form method="post">
<input name="message" maxlength="255" placeholder="Tulis pesan untuk disimpan..." required>
<button type="submit">Simpan</button>
</form>
<h3>Data dari MySQL</h3>
<?php if (!$messages): ?><p><small>Belum ada data.</small></p>
<?php else: ?><ul><?php foreach ($messages as $row): ?>
<li><?= htmlspecialchars($row["message"]) ?> <small>— <?= htmlspecialchars($row["created_at"]) ?></small></li>
<?php endforeach; ?></ul><?php endif; ?>
</main>
</body>
</html>
