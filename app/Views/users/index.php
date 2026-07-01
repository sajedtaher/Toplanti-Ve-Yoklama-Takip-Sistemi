<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php 
  /** @var array $users */
  $role = session('user.role') ?? (session('user')['role'] ?? null); 
?>

<style>
  /* ===========================
     Figma V2 — Users List
     =========================== */

  .users-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
  }

  .users-title {
    font-size: 26px;
    font-weight: 600;
    color: #111827;
  }

  .btn-new-user {
    background: #4f46e5;
    border: none;
    font-weight: 600;
    border-radius: 999px;
    padding-inline: 18px;

    position: fixed;
    top: 28px;
    right: 32px;
    z-index: 999; 
  }
  .btn-new-user:hover {
    background: #4338ca;
  }

  /* + Yeni Kullanıcı telfonda butonu için*/
@media (max-width: 768px) {
  .btn-new-user {
    top: 16px;
    right: 16px;
    padding-inline: 14px;
    font-size: 13px;
  }
}

  .users-wrapper {
    background: #ffffff;
    border-radius: 16px;
    padding: 18px 18px 10px 18px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.06);
  }

  .users-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0 10px;
}

.users-table thead th {
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #6b7280;
  font-weight: 600;
  padding: 0 14px 10px;
  white-space: nowrap;
}

/* çigilerin sütünü sola */
.users-table thead th:nth-child(1),
.users-table tbody td:nth-child(1) {
  text-align: left;
  padding-left: 1px;
}

/* ad soyad sola */
.users-table thead th:nth-child(2),
.users-table tbody td:nth-child(2) {
  text-align: left;
  padding-left: 1px;
}

/* e-posta ortada */
.users-table thead th:nth-child(3){
  text-align: left;
}
.users-table tbody td:nth-child(3) {
  text-align: center;
}

/* birim ortada */
.users-table thead th:nth-child(4),
.users-table tbody td:nth-child(4) {
  text-align: center;
}

/* rol ortada */
.users-table thead th:nth-child(5),
.users-table tbody td:nth-child(5) {
  text-align: center;
  width: 230px;
  min-width: 230px;
  max-width: 230px;
}

.users-table thead th:nth-child(6),
.users-table tbody td:nth-child(6) {
  text-align: center;
}
/* Düzenle ve Sil butonları başlığın altında ortalı kalsın */
.users-table .actions-cell {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  flex-wrap: nowrap;
}

  .users-row {
    background: #f9fafb;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    transition: 0.18s ease;
  }

  .users-row td {
    padding: 14px 14px;
    font-size: 14.5px;
    color: #111827;
    vertical-align: middle;
  }

  .users-row:hover {
    background: #eef2ff;
    transform: scale(1.01);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
  }

  /* Sol renk çubuğu (role göre) */
  .users-row.position-relative::before {
    content: "";
    display: block;
    width: 3px;
    border-radius: 999px;
    position: absolute;
    left: 0;
    top: 10px;
    bottom: 10px;
    background: transparent;
  }

  .users-row[data-role="manager"]::before {
    background: #f97316; /* turuncu */
  }
  .users-row[data-role="member"]::before {
    background: #3b82f6; /* mavi */
  }
  .users-row[data-role="superadmin"]::before {
    background: #8b5cf6; /* mor */
  }

  /* Rol Badge */
  .role-pill {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
  }

  .role-manager {
    background: #ffedd5;
    color: #9a3412;
  }

  .role-member {
    background: #dbeafe;
    color: #1d4ed8;
  }

  .role-superadmin {
    background: #ede9fe;
    color: #5b21b6;
  }


  .action-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 600;
    border: none;
    text-decoration: none;
    cursor: pointer;
    transition: 0.18s ease;
  }

  .action-pill span.icon {
    font-size: 14px;
  }

  .action-edit {
    background: #fef3c7;
    color: #92400e;
  }
  .action-edit:hover {
    filter: brightness(0.9);
  }

  .action-delete {
    background: #fee2e2;
    color: #991b1b;
  }
  .action-delete:hover {
    filter: brightness(0.9);
  }

  /*Birdirim tasarımı için */
.page-info-box {
  background: #f0f9ff;
  color: #0369a1;
  border-left: 4px solid #0ea5e9;
  padding: 10px 14px;
  border-radius: 10px;
  font-size: 13px;
  margin: 0 0 16px 0;
  display: flex;
  align-items: center;
  gap: 8px;
  width: fit-content;
  max-width: 100%;
}

.page-info-box i {
  font-size: 14px;
}


</style>

<div class="users-page-header">
  <h3 class="users-title">Kişiler</h3>

  <?php if ($canEdit): ?>
    <button class="btn btn-new-user text-white" data-bs-toggle="modal" data-bs-target="#createUserModal">
      + Yeni Kullanıcı
    </button>
  <?php endif; ?>
</div>

<?php if (empty(session('selected_unit_id'))): ?>
  <div class="page-info-box">
    <i class="bi bi-info-square-fill"></i>
    <span>Bir birime ait kişileri görüntülemek için sol menüden ilgili birimi seçebilirsiniz.</span>
  </div>
<?php endif; ?>

<div class="users-wrapper position-relative">

  <!-- Yeni kullanıcı modalı -->
  <?= view('users/modal_create', [
      'units' => $units ?? [],
      'showRoleSelect' => $showRoleSelect ?? false,
      'showUnitSelect' => $showUnitSelect ?? false
  ]) ?>

  <!-- Düzenleme modalı -->
  <?= $this->include('users/modal_edit') ?>

  <table class="users-table">
    <thead>
      <tr>
        <th> </th>
        <th>Ad Soyad</th>
        <th>E-posta</th>
        <th>Birim</th>
        <th>Rol</th>
        
        <?php if ($canEdit): ?>
          <th class="actions-th">İşlemler</th>
        <?php endif; ?>

      </tr>
    </thead>

    <tbody>
      <?php foreach ($users as $u): ?>
        <?php
          $userRole = $u['role'] ?? 'member';
          $roleLabel = $userRole;
          $roleClass = 'role-member';

          if ($userRole === 'manager') {
            $roleLabel = 'Yönetici';
            $roleClass = 'role-manager';
          } elseif ($userRole === 'superadmin') {
            $roleLabel = 'Süper Yönetici';
            $roleClass = 'role-superadmin';
          } else {
            $roleLabel = 'Üye';
            $roleClass = 'role-member';
          }

          $unitName = $u['unit_name'] ?? '—';
        ?>

        <tr class="users-row position-relative" data-role="<?= esc($userRole) ?>">

          <td><?= esc($u['name']) ?></td>
          <td><?= esc($u['email']) ?></td>
          <td><?= esc($unitName) ?></td>
          <td>
            <span class="role-pill <?= $roleClass ?>">
              <?= esc($roleLabel) ?>
            </span>
          </td>

          <?php if (in_array($role, ['manager','superadmin'], true)): ?>
            <td>
              <div class="actions-cell">
                <?php if ($canEdit): ?>
                <!-- Düzenle -->
                <button class="action-pill action-edit edit-btn"
                  data-id="<?= esc($u['id']) ?>"
                  data-name="<?= esc($u['name']) ?>"
                  data-email="<?= esc($u['email']) ?>"
                  data-role="<?= esc($u['role']) ?>"
                  data-unit="<?= esc($u['unit_id']) ?>" 
                >
                  <span class="icon">✏</span>
                  <span>Düzenle</span>
                </button>

                <!-- Sil -->
                <form action="<?= base_url('users/delete/' . $u['id']) ?>" method="post" style="display:inline;">
                  <button type="submit" 
                          class="action-pill action-delete"
                          onclick="return confirm('Silmek istediğinize emin misiniz?')">
                    <span class="icon">🗑</span>
                    <span>Sil</span>
                  </button>
                </form>
                 <?php endif; ?>
              </div>
            </td>
          <?php endif; ?>

        </tr>
       
      <?php endforeach; ?>
    </tbody>

  </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Düzenle butonlarına tıklanınca modalı aç
  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const modalEl = document.getElementById('editUserModal');
      if (!modalEl) { console.error('Modal #editUserModal bulunamadı'); return; }

      document.getElementById('edit_user_id').value = btn.dataset.id;
      document.getElementById('edit_name').value    = btn.dataset.name;
      document.getElementById('edit_email').value   = btn.dataset.email;
      document.getElementById('edit_password').value = '';
      
      if (document.getElementById('edit_unit_id')) {
        document.getElementById('edit_unit_id').value = btn.dataset.unit;
}


      const roleField = document.getElementById('edit_role');
      if (roleField && btn.dataset.role) {
        roleField.value = btn.dataset.role;
      }

      const modal = new bootstrap.Modal(modalEl);
      modal.show();
    });
  });

  // Form gönderimi (AJAX)
  const form = document.getElementById('editUserForm');
  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const id = document.getElementById('edit_user_id').value;
      const data = new FormData(form);

      const res  = await fetch("<?= base_url('users/update') ?>/" + id, {
        method: 'POST',
        body: data,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const json = await res.json();

      if (json.success) {
        const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
        modal?.hide();
        location.reload();
      } else {
        alert('Güncelleme başarısız.');
        console.error(json);
      }
    });
  }
});
</script>

<?= $this->endSection() ?>
