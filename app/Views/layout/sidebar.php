<?php
$user = session('user') ?? null;

$realRole  = $user['role'] ?? 'member';
$demoRole  = session('demo_role');
$roleUsed  = $demoRole ?? $realRole;

$unitModel    = new \App\Models\UnitModel();
$units        = $unitModel->orderBy('name')->findAll();
$selectedUnit = session('selected_unit_id');
?>


<style>
  /* === SOFT BLUE – FIGMA STYLE SIDEBAR === */
  .sidebar {
  width: 260px;
  height: 100vh;
  position: fixed;      /* 🔥 Sidebar sabit */
  top: 0;
  left: 0;
  background: #F8FAFC;
  border-right: 1px solid #E2E8F0;
  padding: 18px 16px;
  display: flex;
  flex-direction: column;
  z-index: 1000;        /* Üste çıksın */
}


  /* Header */
  .sidebar-header {
    display: flex;
    gap: 12px;
    align-items: center;
    padding-bottom: 0;
  }

  .sidebar-header-icon {
    width: 42px;
    height: 42px;
    border-radius: 13px;
    background: #2563EB;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 20px;
  }

  .sidebar-header-text .title {
    font-size: 18px;
    font-weight: 700;
    color: #0F172A;
  }

  .sidebar-header-text .subtitle {
    font-size: 13px;
    color: #64748B;
  }

  /* Üst Çizgi */
  .sidebar-divider {
    margin:10px 0 14px 0;
    height: 1px;
    background: #E2E8F0;
  }

  /* Menü */
  .sidebar-menu {
    display: flex;
    flex-direction: column;
    gap: 7px;
  }

  .sidebar-menu-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 13px;
    border-radius: 999px;
    text-decoration: none;
    color: #111827;
    font-size: 15px;
    transition: background 0.15s, color 0.15s;
  }

  .sidebar-menu-link:hover {
    background: #EEF2FF;
    color: #1D4ED8;
  }

  .sidebar-menu-link.active {
    background: #E0EAFF;
    color: #1D4ED8;
    font-weight: 600;
  }

  .sidebar-menu-link i {
    font-size: 17px;
    color: #6B7280;
  }

  .sidebar-menu-link.active i {
    color: #1D4ED8;
  }

  /* Birim seçici */
  .unit-select-wrapper {
    margin: 14px 0 14px 0;
  }

  .unit-select-wrapper select {
    width: 100%;
    height : 38px;
    padding: 7px 10px;
    border-radius: 10px;
    border: 1px solid #CBD5E1;
    background: #ffffff;
    font-size: 14px;
    color: #111827;
  }

  .unit-select-wrapper select:focus {
    outline: none;
    border-color: #4F46E5;
  }

  /* Rol Değiştir Kutusu */
  .role-switch-wrapper {
    margin-top: 0;
    padding: 0;
    background: transparent;
    border: none;
    border-radius: 0;
  }

  .role-switch-label {
    font-size: 12px;
    color: #64748B;
    margin-bottom: 6px;
  }

  .role-switch-wrapper select {
    width: 100%;
    height: 38px;
    padding: 7px 12px;
    border-radius: 10px;
    border: 1px solid #CBD5E1;
    background: #ffffff;
    font-size: 14px;
    color: #111827;
  }

  .role-switch-wrapper select:focus {
    outline: none;
    border-color: #4F46E5;  
  }

  /* Profil */
  .profile-box {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .profile-avatar {
    width: 40px;
    height: 40px;
    border-radius: 999px;
    background: #DBEAFE;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1D4ED8;
    font-size: 22px;
  }

  .profile-info .name {
    font-size: 15px;
    font-weight: 600;
    color: #0F172A;
  }

  .profile-info .role {
    font-size: 13px;
    color: #6B7280;
  }

  .logout-link {
    margin-top: 10px;
    display: inline-block;
    font-size: 14px;
    color: #DC2626;
    text-decoration: none;
  }

  .sidebar-content {
  flex: 1;             /* Menü yukarı, profil aşağı */
  display: flex;
  flex-direction: column;
  gap: 18px;
}
/* üst çizgiyi oluşturan, alt group */
.sidebar-bottom-group {
  border-top: 1px solid #E2E8F0;   /* sadece 1 çizgi buradan */
  padding-top: 12px;
  margin-top: 10px;
  display: flex;
  flex-direction: column;
  gap: 10px;   /* rol değiştir + profil arası doğru boşluk */
}

/* ===============================
   SIDEBAR USER CARD
================================ */
.sidebar-user-card {
  display: flex;
  align-items: center;
  gap: 11px;
  background: #eef2ff;
  border-radius: 14px;
  padding: 12px 14px;
  margin-top: 0;
  transition: 0.18s ease;
}

.sidebar-user-card:hover {
  background: #eef2ff;
}

.sidebar-user-avatar {
  width: 42px;
  height: 42px;
  min-width: 42px;
  border-radius: 50%;
  background: #dbeafe;
  color: #2563eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 19px;
}

.sidebar-user-info {
  display: flex;
  flex-direction: column;
  line-height: 1.3;
}

.sidebar-user-name {
  font-size: 15px;
  font-weight: 700;
  color: #111827;
}

.sidebar-user-role {
  font-size: 13px;
  color: #64748b;
  margin-top: 2px;
}


</style>


<aside class="sidebar">

  <!-- Logo -->
  <header class="sidebar-header">
    <div class="sidebar-header-icon">
      <i class="bi bi-calendar2-week"></i>
    </div>
    <div class="sidebar-header-text">
      <div class="title">Toplantı Takip</div>
      <div class="subtitle">Eğitim Kurumu</div>
    </div>
  </header>

  <!-- Birim seçici (logo altında) -->
  <?php if ($roleUsed === 'superadmin'): ?>
  <form action="<?= site_url('units/select') ?>" method="post">
    <?= csrf_field() ?>
    <div class="unit-select-wrapper">
      <select name="unit_id" onchange="this.form.submit()">
        <option value="">Tüm Birimler</option>
        <?php foreach ($units as $u): ?>
          <option value="<?= $u['id'] ?>" <?= ($selectedUnit == $u['id']) ? 'selected' : '' ?>>
            <?= esc($u['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </form>
  <?php endif; ?>

  <!-- ÜST ÇİZGİ -->
  <div class="sidebar-divider"></div>

  <!-- MENÜ -->
  <nav class="sidebar-menu">
    <a href="<?= site_url('meetings') ?>"
       class="sidebar-menu-link <?= uri_string() === 'meetings' ? 'active' : '' ?>">
        <i class="bi bi-bell"></i>
        <span>Toplantılar</span>
    </a>

    <a href="<?= site_url('users') ?>"
       class="sidebar-menu-link <?= uri_string() === 'users' ? 'active' : '' ?>">
        <i class="bi bi-people"></i>
        <span>Kişiler</span>
    </a>

        <?php if (in_array($roleUsed, ['superadmin','manager'])): ?>
    <a href="<?= site_url('reports/attendance') ?>"
       class="sidebar-menu-link <?= url_is('reports*') ? 'active' : '' ?>">
        <i class="bi bi-graph-up"></i>
        <span>Katılım Raporu</span>
    </a>
    <?php endif; ?>

    <?php if ($roleUsed === 'superadmin'): ?>
    <a href="<?= site_url('units') ?>"
       class="sidebar-menu-link <?= url_is('units*') ? 'active' : '' ?>">
        <i class="bi bi-building"></i>
        <span>Birimler</span>
    </a>
    <?php endif; ?>
  </nav>

  <!-- BOŞLUK OLUŞSUN DİYE FLEX YAPI, menüden sonraki boşluk -->
  <div style="flex:1;"></div>
  
  <!-- ALT GRUP (sidebar dibi) -->
<div class="sidebar-bottom-group">

<!-- Rol değişim kutusu -->
<?php if ($realRole === 'superadmin' || session('is_demo') || session()->has('demo_role')): ?>
  <form action="<?= site_url('demo/change-role') ?>" method="post">
    <?= csrf_field() ?>

    <div class="role-switch-wrapper">
      <div class="role-switch-label">Demo: Rol Değiştir</div>

      <select name="role" onchange="this.form.submit()">
        <option value="superadmin" <?= $roleUsed === 'superadmin' ? 'selected' : '' ?>>
          Sistem Yöneticisi
        </option>

        <option value="manager" <?= $roleUsed === 'manager' ? 'selected' : '' ?>>
          Birim Yöneticisi
        </option>

        <option value="member" <?= $roleUsed === 'member' ? 'selected' : '' ?>>
          Üye
        </option>
      </select>
    </div>
  </form>
<?php endif; ?>

  <!-- Profil -->
  <?php if ($user): ?>
    <div class="sidebar-profile mt-1">

      <div class="profile-box sidebar-user-card">
        <div class="profile-avatar sidebar-user-avatar">
          <i class="bi bi-person-fill"></i>
        </div>

        <div class="profile-info sidebar-user-info">
          <div class="name sidebar-user-name">
            <?= esc($user['name']) ?>
          </div>

          <div class="role sidebar-user-role">
            <?= $roleUsed === 'superadmin' ? 'Sistem Yöneticisi' : ($roleUsed === 'manager' ? 'Birim Yöneticisi' : 'Üye') ?>
          </div>
        </div>
      </div>

  <!-- Çıkış Linki (http://localhost/ci4_meetings_clean/public/index.php/logout) -->
      <?php /*
      <a class="logout-link" href="<?= site_url('logout') ?>">Çıkış Yap</a>
      */ ?>

    </div>
  <?php endif; ?>

</div>

</aside>

