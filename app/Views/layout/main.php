<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= esc($title ?? 'Toplantı Kayıt') ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons (kibar ikonlar için) -->
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />

  <style>
    body {
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: #F3F4F6;
      margin: 0;
    }

    .app-layout {
      min-height: 100vh;
      display: flex;
    }

    .app-main {
      flex: 1;
      padding: 24px;
      margin-left: 260px;    /* 🔥 Sidebar genişliği kadar boşluk */
    }
  </style>
</head>

<body>

<?php
  // Şu anki sayfa login mi?
  // (Senin login view'in auth/login.php, action'ı ise site_url('login'))
  $isLoginPage = url_is('login') || url_is('auth/login');
?>

<?php if ($isLoginPage): ?>

  <!-- 🔹 Sadece login sayfası: sidebar YOK -->
  <div class="container py-5">
    <?= $this->renderSection('content') ?>
  </div>

<?php else: ?>

  <!-- 🔹 Tüm diğer sayfalar: sidebar + içerik -->
  <div class="app-layout">
    <?= view('layout/sidebar') ?>

    <main class="app-main">
      <?= $this->renderSection('content') ?>
    </main>
  </div>

<?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
