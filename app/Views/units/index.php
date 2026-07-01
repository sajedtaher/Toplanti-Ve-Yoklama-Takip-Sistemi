<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php 
  /** @var array $units */
  $role = session('user.role') ?? (session('user')['role'] ?? null); 
?>

<style>
  /* ===========================
     Figma V2 — Units List
     =========================== */

  .units-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
  }

  .units-title {
    font-size: 26px;
    font-weight: 600;
    color: #111827;
  }

  .btn-new-unit {
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
  .btn-new-unit:hover {
    background: #4338ca;
  }


  /* + Yeni Birim telfonda butonu için*/
  @media (max-width: 768px) {
  .btn-new-user {
    top: 16px;
    right: 16px;
    padding-inline: 14px;
    font-size: 13px;
  }
}

  .units-wrapper {
    background: #ffffff;
    border-radius: 16px;
    padding: 18px 18px 10px 18px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.06);
  }

 .units-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0 10px;
}

.units-table thead th {
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #6b7280;
  font-weight: 600;
  padding: 0 18px 10px;
  white-space: nowrap;
}

/* Birim adı sola */
.units-table thead th:nth-child(1),
.units-table tbody td:nth-child(1) {
  text-align: left;
  padding-left: 22px;
}

/* Birim yöneticisi ortada */
.units-table thead th:nth-child(2),
.units-table tbody td:nth-child(2) {
  text-align: left;
}

/* İşlemler ortada */
.units-table thead th:nth-child(3),
.units-table tbody td:nth-child(3) {
  text-align: center;
  padding-right: 28px;
}

.units-table .actions-cell {
  justify-content: center;
}

  .unit-row {
    background: #f9fafb;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    transition: 0.18s ease;
  }

  .unit-row td {
    padding: 14px 14px;
    font-size: 14.5px;
    color: #111827;
    vertical-align: middle;
  }

  .unit-row:hover {
    background: #eef2ff;
    transform: scale(1.01);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
  }

  /* Manager badge */
  .manager-pill {
    background: #dbeafe;
    color: #1d4ed8;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
  }

  /* Actions */
  .actions-cell {
    display: flex;
    gap: 6px;
    justify-content: flex-start;
    flex-wrap: wrap;
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
    cursor: pointer;
    transition: 0.18s ease;
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
</style>


<div class="units-page-header">
  <h3 class="units-title">Birimler</h3>

  <?php if (in_array($role, ['manager','superadmin'], true)): ?>
    <button class="btn btn-new-unit text-white" data-bs-toggle="modal" data-bs-target="#createUnitModal">
      + Yeni Birim
    </button>
  <?php endif; ?>
</div>

<div class="units-wrapper position-relative">

  <!-- Yeni Birim Modal -->
  <?= view('units/modal_create', ['managers' => $managers]) ?>

  <!-- Düzenleme Modal (boş, JS ile doldurulur) -->
  <div class="modal fade" id="editUnitModal" tabindex="-1"></div>

  <table class="units-table">
    <thead>
      <tr>
        <th>Birim Adı</th>
        <th>Birim Yöneticisi</th>
        <th style="width: 200px;">İşlemler</th>
      </tr>
    </thead>

    <tbody>
      <?php foreach ($units as $u): ?>
        <tr class="unit-row position-relative"
            data-id="<?= $u['id'] ?>"
            data-name="<?= esc($u['name']) ?>"
            data-manager="<?= esc($u['manager_id']) ?>">

          <td><?= esc($u['name']) ?></td>

          <td>
            <?php if (!empty($u['manager_name'])): ?>
              <span class="manager-pill"><?= esc($u['manager_name']) ?></span>
            <?php else: ?>
              <span class="text-muted">—</span>
            <?php endif; ?>
          </td>

          <td>
            <div class="actions-cell">

              <button class="action-pill action-edit edit-unit-btn">
                <span class="icon">✏</span>
                Düzenle
              </button>

              <a class="action-pill action-delete"
                 href="<?= base_url('units/delete/' . $u['id']) ?>"
                 onclick="return confirm('Bu birimi silmek istediğinize emin misiniz?')">
                <span class="icon">🗑</span>
                Sil
              </a>

            </div>
          </td>

        </tr>
      <?php endforeach; ?>
    </tbody>

  </table>
</div>



<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.edit-unit-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      const row = btn.closest('tr');
      const id = row.dataset.id;

      const res = await fetch("<?= base_url('units/edit') ?>/" + id);
      const html = await res.text();

      document.getElementById('editUnitModal').innerHTML = html;

      const modal = new bootstrap.Modal(document.getElementById('editUnitModal'));
      modal.show();
    });
  });
});
</script>

<?= $this->endSection() ?>
