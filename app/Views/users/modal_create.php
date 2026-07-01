<!-- app/Views/users/modal_create.php -->

<style>
  /* ===========================
     Figma — User Create Modal
     =========================== */

  .figma-modal-content {
    border-radius: 18px;
    border: none;
    box-shadow: 0 18px 45px rgba(15,23,42,0.25);
  }

  .figma-modal-header {
    border-bottom: 1px solid #e5e7eb;
    padding: 16px 20px;
  }

  .figma-modal-title {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
  }

  .figma-modal-body {
    padding: 16px 20px 12px 20px;
  }

  .figma-modal-footer {
    border-top: 1px solid #f3f4f6;
    padding: 12px 20px 14px 20px;
  }

  .figma-label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 4px;
  }

  .figma-input,
  .figma-select {
    border-radius: 10px;
    border: 1px solid #d1d5db;
    padding: 8px 10px;
    font-size: 14px;
  }

  .figma-input:focus,
  .figma-select:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 2px rgba(79,70,229,0.15);
  }

  .btn-modal-cancel {
    background: #e5e7eb;
    border: none;
    border-radius: 999px;
    padding-inline: 16px;
    font-weight: 500;
    color: #374151;
  }
  .btn-modal-cancel:hover {
    filter: brightness(0.95);
  }

  .btn-modal-save {
    background: #4f46e5;
    border: none;
    border-radius: 999px;
    padding-inline: 20px;
    font-weight: 600;
  }
  .btn-modal-save:hover {
    background: #4338ca;
  }
</style>

<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content figma-modal-content">

      <div class="modal-header figma-modal-header">
        <h5 class="modal-title figma-modal-title" id="createUserModalLabel">Yeni Kullanıcı Ekle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>

      <div class="modal-body figma-modal-body">
        <form id="createUserForm" method="post">
          <?= csrf_field() ?>

          <div class="mb-3">
            <label class="figma-label">Ad Soyad</label>
            <input type="text" name="name" class="form-control figma-input" required>
          </div>

          <div class="mb-3">
            <label class="figma-label">E-posta</label>
            <input type="email" name="email" class="form-control figma-input" required>
          </div>

          <!-- 🧩 Eğer superadmin + belirli bir birim seçiliyse → sadece ROL kutusu -->
          <?php if (session('user.role') === 'superadmin' && !empty(session('selected_unit_id'))): ?>
            <div class="mb-3">
              <label class="figma-label">Rol</label>
              <select name="role" class="form-select figma-select" required>
                <option value="" disabled selected hidden>Rol seçiniz</option>
                <option value="manager">Yönetici</option>
                <option value="member">Üye</option>
              </select>
            </div>

            <input type="hidden" name="unit_id" value="<?= session('selected_unit_id') ?>">

          <!-- 🧩 Eğer superadmin + Tüm Birimler seçiliyse → Rol + Birim seç -->
          <?php elseif (session('user.role') === 'superadmin'): ?>
            <div class="mb-3">
              <label class="figma-label">Rol</label>
              <select name="role" class="form-select figma-select" required>
                <option value="" disabled selected hidden>Rol seçiniz</option>
                <option value="manager">Yönetici</option>
                <option value="member">Üye</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="figma-label">Birim</label>
              <select name="unit_id" class="form-select figma-select" required>
                <option value="" disabled selected hidden>Birim seçiniz</option>
                <?php foreach ($units as $u): ?>
                  <option value="<?= esc($u['id']) ?>"><?= esc($u['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          <?php endif; ?>

        </form>
      </div>

      <div class="modal-footer figma-modal-footer d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">Kapat</button>
        <button type="submit" class="btn btn-modal-save text-white" form="createUserForm">Kaydet</button>
      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('createUserForm');
  const modalEl = document.getElementById('createUserModal');

  modalEl.addEventListener('show.bs.modal', () => {
    form.reset();
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = new FormData(form);

    const res = await fetch("<?= base_url('users/store') ?>", {
      method: 'POST',
      body: data,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const json = await res.json();

    if (json.success) {
      const modal = bootstrap.Modal.getInstance(modalEl);
      modal.hide();
      location.reload();
    } else {
      alert('Kullanıcı eklenemedi.');
      console.error(json);
    }
  });
});

// 🎨 Placeholder rengi
const style = document.createElement('style');
style.innerHTML = `
  select:invalid {
    color: #6c757d !important;
    opacity: 0.85;
  }
  option[disabled][hidden] {
    color: #6c757d !important;
  }
`;
document.head.appendChild(style);
</script>
