<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php 
  /** @var array $meetings */
  $role = session('user.role') ?? (session('user')['role'] ?? null); 
?>

<style>
  /* ==============================================================
     Figma — Meetings List (Same Design, NO Edit Page Redirect)
     ============================================================== */

  .meetings-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
  }

  .meetings-title {
    font-size: 26px;
    font-weight: 600;
    color: #111827;
  }

  .btn-new-meeting {
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
  .btn-new-meeting:hover {
    background: #4338ca;
  }

  /* + Yeni Toplantı telfonda butonu için*/
  @media (max-width: 768px) {
  .btn-new-meeting {
    top: 16px;
    right: 16px;
    padding-inline: 14px;
    font-size: 13px;
  }
}

  .meetings-wrapper {
    background: #ffffff;
    border-radius: 16px;
    padding: 18px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.06);
  }

  .meetings-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0 10px;
}

.meetings-table thead th {
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #6b7280;
  font-weight: 600;
  padding: 10px;
  white-space: nowrap;
}

/* 1. sütun: renk çizgisi boş sütun */
.meetings-table thead th:nth-child(1),
.meetings-table tbody td:nth-child(1) {
  width: 24px;
  min-width: 24px;
  padding: 0;
  text-align: center;
}

/* Başlangıç sütunu sola */
.meetings-table thead th:nth-child(2),
.meetings-table tbody td:nth-child(2) {
  text-align: center;
  padding-left: 0px;
}

/* Durum sütunu ortada */
.meetings-table thead th:nth-child(3),
.meetings-table tbody td:nth-child(3) {
  text-align: center;
}

/* İşlemler sütunu */
.meetings-table thead th:nth-child(4),
.meetings-table tbody td:nth-child(4) {
  text-align: center;
  width: 300px;
  min-width: 300px;
  max-width: 300px;
}

.meetings-table thead th:nth-child(5),
.meetings-table tbody td:nth-child(5) {
  text-align: center;
}

/* İşlem butonları başlığın altında ortalı dursun */
.meetings-table .actions-cell {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  flex-wrap: nowrap;
}

  .meetings-row {
    background: #f9fafb;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    transition: 0.18s ease;
    position: relative;
  }

  .meetings-row:hover {
    background: #eef2ff;
    transform: scale(1.01);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
  }

  .meetings-row td {
    padding: 14px;
    font-size: 14.5px;
    color: #111827;
    vertical-align: middle;
  }

  .meetings-row::before {
    content: "";
    display: block;
    width: 3px;
    border-radius: 999px;
    position: absolute;
    left: 0;
    top: 10px;
    bottom: 10px;
  }

  .meetings-row[data-status="ended"]::before {
    background: #6b7280;
  }
  .meetings-row[data-status="active"]::before {
    background: #22c55e;
  }
  .meetings-row[data-status="planned"]::before {
    background: #3b82f6;
  }

  .status-pill {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
  }

  .status-pill-default { background: #e5e7eb; color: #374151; }
  .status-pill-active { background: #dcfce7; color: #166534; }
  .status-pill-ended { background: #e5e7eb; color: #4b5563; }
  .status-pill-planned { background: #dbeafe; color: #1d4ed8; }

  .action-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
  }

  .action-view { background: #ede9fe; color: #4c1d95; }
  .action-edit { background: #fef3c7; color: #92400e; }
  .action-delete { background: #fee2e2; color: #991b1b; }

  .action-view:hover,
  .action-edit:hover,
  .action-delete:hover { filter: brightness(0.9); }

  /*bildirim tasarımı için*/
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


<!-- ================================================= -->
<!-- PAGE HEADER -->
<!-- ================================================= -->
<div class="meetings-page-header">
  <h3 class="meetings-title">Toplantılar</h3>

<?php if ($canEdit): ?>
  <button class="btn btn-new-meeting text-white" 
          data-bs-toggle="modal"
          data-bs-target="#createMeetingModal">
    + Yeni Toplantı
  </button>
<?php endif; ?>

</div>

<?php if (empty(session('selected_unit_id'))): ?>
  <div class="page-info-box">
    <i class="bi bi-info-square-fill"></i>
    <span>Bir birimin toplantılarını görüntülemek için sol menüden ilgili birimi seçebilirsiniz.</span>
  </div>
<?php endif; ?>


<div class="meetings-wrapper">

  <!-- Load Create Modal -->
  <?php if (isset($users)): ?>
    <?= view('meetings/modal_create', [
      'users' => $users,
      'units' => $units,
      'showUnitSelect' => $showUnitSelect
    ]) ?>
  <?php endif; ?>


  <!-- ================================================= -->
  <!-- MEETINGS LIST -->
  <!-- ================================================= -->
  <table class="meetings-table">
    <thead>
      <tr>
        <th style="width: 24px;"></th>
        <th>Başlangıç</th>
        <th>Birim</th>
        <th>Durum</th>
        <th style="width: 260px;">İşlemler</th>
      </tr>
    </thead>

    <tbody>
      <?php foreach ($meetings as $m): ?>
        <?php
          $statusRaw = $m['status'] ?? '';
          $rowStatusAttr = 'default';
          $statusLabel = $statusRaw;
          $statusClass = 'status-pill-default';

          if ($statusRaw === 'ended') {
            $statusLabel = 'Sonlandırıldı';
            $statusClass = 'status-pill-ended';
            $rowStatusAttr = 'ended';
          } elseif ($statusRaw === 'active' || $statusRaw === 'ongoing') {
            $statusLabel = 'Devam Ediyor';
            $statusClass = 'status-pill-active';
            $rowStatusAttr = 'active';
          } elseif ($statusRaw === 'planned' || $statusRaw === 'draft') {
            $statusLabel = 'Planlandı';
            $statusClass = 'status-pill-planned';
            $rowStatusAttr = 'planned';
          }

          $startFormatted = '';
          if (!empty($m['start_at'])) {
            $ts = strtotime($m['start_at']);
            $startFormatted = $ts ? date('d.m.Y H:i', $ts) : esc($m['start_at']);
          }
        ?>
        <tr class="meetings-row" data-status="<?= esc($rowStatusAttr) ?>">

          <td><?= esc($startFormatted) ?></td>

          <td>
  <span class="meeting-unit-pill">
    <?= esc($m['unit_name'] ?? '-') ?>
  </span>
</td>

          <td>
            <span class="status-pill <?= esc($statusClass) ?>">
              <?= esc($statusLabel) ?>
            </span>
          </td>

          <td>
            <div class="actions-cell">

              <a class="action-pill action-view"
                 href="<?= base_url('meetings/' . $m['id']) ?>">
                👁 Görüntüle
              </a>

              <?php if ($canEdit && $statusRaw !== 'ended'): ?>

                <a class="action-pill action-edit edit-meeting-btn"
                   href="#"
                   data-id="<?= $m['id'] ?>">
                  ✏ Düzenle
                </a>

                <a class="action-pill action-delete"
                   href="<?= base_url('meetings/delete/' . $m['id']) ?>"
                   onclick="return confirm('Silinsin mi?')">
                  🗑 Sil
                </a>

              <?php endif; ?>

            </div>
          </td>

        </tr>
      <?php endforeach; ?>
    </tbody>

  </table>
</div>


<!-- ================================================= -->
<!-- EDIT MODAL (modal_edit.php AJAX ile doldurulacak) -->
<!-- ================================================= -->
<div class="modal fade" id="editMeetingModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" id="editMeetingModalContent">
      <!-- AJAX ile içerik yüklenecek -->
    </div>
  </div>
</div>


<!-- ================================================= -->
<!-- JAVASCRIPT — MODAL AJAX LOAD                     -->
<!-- ================================================= -->
<script>
document.addEventListener("DOMContentLoaded", function() {

  // ⭐ Düzenle butonuna tıklayınca modal aç
  document.querySelectorAll(".edit-meeting-btn").forEach(btn => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();

      let id = this.dataset.id;

      fetch("<?= base_url('meetings/modalEdit/') ?>" + id)
        .then(res => res.text())
        .then(html => {
          document.getElementById("editMeetingModalContent").innerHTML = html;

          let modal = new bootstrap.Modal(document.getElementById('editMeetingModal'));
          modal.show();

          // ⭐ Modal yüklendikten sonra SAVE butonu aktif olmalı
          bindSaveMeeting();
        });
    });
  });

});

// ⭐ Modal içindeki Kaydet butonunu çalıştıran fonksiyon
function bindSaveMeeting() {

  let btn = document.getElementById("saveEditMeetingBtn");
  if (!btn) return;

  btn.addEventListener("click", function () {

    const form = document.getElementById("editMeetingForm");
    const formData = new FormData(form);
    const meetingId = formData.get("id");

    fetch("<?= base_url('meetings/update/') ?>" + meetingId, {
      method: "POST",
      body: formData
    })
    .then(() => {
      bootstrap.Modal.getInstance(document.getElementById('editMeetingModal')).hide();
      location.reload();
    })
    .catch(err => {
      alert("Hata oluştu");
      console.error(err);
    });

  });
}
</script>


<?= $this->endSection() ?>
