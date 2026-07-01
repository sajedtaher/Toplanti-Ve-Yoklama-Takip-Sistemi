<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
  /* =============================================== */
  /*  GLOBAL PAGE LAYOUT — Figma Soft Style          */
  /* =============================================== */

  .page-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .figma-card {
    background: #ffffff;
    border-radius: 18px;
    padding: 20px 22px;
    box-shadow: 0 6px 20px rgba(15, 23, 42, 0.04);
  }

  .section-title {
    font-size: 21px;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 8px;
  }

  .section-subtitle {
    font-size: 13px;
    color: #6b7280;
  }

  /* =============================================== */
  /*  HEADER CARD                                    */
  /* =============================================== */

  .meeting-header-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 6px;
  }

  .meeting-title {
    font-size: 24px;
    font-weight: 700;
    color: #111827;
  }

  .meeting-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 999px;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 500;
  }

  .meeting-status-pill span.icon {
    font-size: 14px;
  }

  .status-pill-active {
    background: #ecfdf3;
    color: #166534;
  }

  .status-pill-ended {
    background: #f3f4f6;
    color: #374151;
  }

  .status-pill-draft {
    background: #eff6ff;
    color: #1d4ed8;
  }

  .meeting-meta {
    margin-top: 10px;
  }

  .meta-label {
    font-size: 13px;
    color: #6b7280;
  }

  .meta-value {
    font-size: 15px;
    font-weight: 500;
    color: #111827;
  }

  /* =============================================== */
  /*  AGENDA & DECISIONS                             */
  /* =============================================== */

  .agenda-card {
    background: #ffffff;
    border-radius: 18px;
    padding: 20px 20px 16px;
    box-shadow: 0 6px 20px rgba(15, 23, 42, 0.04);
  }

  .agenda-header-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
  }

  .agenda-info-text {
    font-size: 12px;
    color: #9ca3af;
  }

  .agenda-divider {
    height: 1px;
    background: #e5e7eb;
    margin: 10px 0 14px;
  }

  /* liste içeriği _agenda_list partial içinde geliyor; burada genel görünümü yumuşatıyoruz */
  .agenda-item {
    background: #f9fafb;
    padding: 12px 14px;
    border-radius: 14px;
    margin-bottom: 8px;
    border: 1px solid #e5e7eb;
    transition: background 0.18s, border-color 0.18s;
  }

  .agenda-item:hover {
    background: #eef2ff;
    border-color: #c7d2fe;
  }

  .decision-text {
    cursor: pointer;
    margin-top: 4px;
    padding: 5px 7px;
    border-radius: 8px;
    background: #f3f4f6;
    font-size: 14px;
  }

  .decision-text:hover {
    background: #e5e7eb;
  }

  /* =============================================== */
  /*  PARTICIPANTS PANEL — PASTEL FIGMA STYLE        */
  /* =============================================== */

  .participants-card {
    background: #ffffff;
    padding: 20px;
    border-radius: 18px;
    box-shadow: 0 6px 20px rgba(15, 23, 42, 0.04);
  }

  .participants-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 8px;
  }

  .participants-legend {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 11px;
    color: #6b7280;
  }

  .participants-legend-item {
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .participants-legend-item i {
    font-size: 14px;
  }

  .participant-list {
    margin-top: 6px;
  }

  .participant-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 4px;
    border-bottom: 1px solid #f1f5f9;
  }

  .participant-row:last-child {
    border-bottom: none;
  }

  .participant-left {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .participant-name {
    font-size: 15px;
    font-weight: 500;
    color: #111827;
  }

  .participant-muted {
    font-size: 12px;
    color: #9ca3af;
  }

  .status-buttons-group {
    display: flex;
    align-items: center;
    gap: 4px;
  }

  /* --- pastel, ince pill butonlar --- */
  .status-btn {
    width: 44px;
    height: 26px;
    border-radius: 999px;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 15px;
    color: #6b7280;
    transition: 0.15s ease;
  }

  .status-btn i { pointer-events: none; }

  .status-btn:hover {
    background: #f9fafb;
    transform: translateY(-1px);
  }

  /* GELMEDİ - kırmızı pastel */
  .status-btn[data-status="gelmedi"].active {
    background: #fee2e2;
    border-color: #fecaca;
    color: #b91c1c;
  }

  /* İZİNLİ / İZİNLİ - sarı pastel */
  .status-btn[data-status="izinli"].active {
    background: #fef3c7;
    border-color: #fde68a;
    color: #92400e;
  }

  /* GELDİ - yeşil pastel */
  .status-btn[data-status="geldi"].active {
    background: #dcfce7;
    border-color: #bbf7d0;
    color: #166534;
  }

  .status-text {
    margin-left: 10px;
    font-size: 13px;
    font-weight: 500;
    color: #4b5563;
    width: 72px;
  }

  /* =============================================== */
  /*  END MEETING CARD                               */
  /* =============================================== */

  .end-meeting-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
  }

  .end-info-text {
    font-size: 13px;
    color: #6b7280;
  }

  @media (max-width: 768px) {
    .meeting-header-top {
      flex-direction: column;
      align-items: flex-start;
    }
    .end-meeting-actions {
      flex-direction: column;
      align-items: flex-start;
    }
  }
</style>

<div class="page-container">

  <!-- ===================================================== -->
  <!--                      HEADER CARD                       -->
  <!-- ===================================================== -->
  <div class="figma-card">
    <?php
      $status = $meeting['status'];
      $statusClass = 'status-pill-active';
      $statusIcon  = 'bi bi-play-circle';
      $statusLabel = 'Aktif';

      if ($status === 'ended') {
          $statusClass = 'status-pill-ended';
          $statusIcon  = 'bi bi-check-circle';
          $statusLabel = 'Sonlandırıldı';
      } elseif ($status === 'draft') {
          $statusClass = 'status-pill-draft';
          $statusIcon  = 'bi bi-pencil';
          $statusLabel = 'Taslak';
      }
    ?>

    <div class="meeting-header-top">
      <div>
        <!-- İSTEDİĞİN GİBİ: sadece "Toplantılar", ID yok -->
        <div class="meeting-title">Toplantılar</div>
        <div class="section-subtitle">Bu toplantının detayları, gündem ve katılımcı durumu</div>
      </div>

      <div class="meeting-status-pill <?= $statusClass ?>">
        <span class="icon"><i class="<?= $statusIcon ?>"></i></span>
        <span><?= esc($statusLabel) ?></span>
      </div>
    </div>

    <div class="row meeting-meta g-3 mt-2">
      <div class="col-md-4">
        <div class="meta-label">Başlangıç</div>
        <div class="meta-value"><?= esc($meeting['start_at']) ?></div>
      </div>
      <div class="col-md-4">
        <div class="meta-label">Durum (ham)</div>
        <div class="meta-value"><?= esc($meeting['status']) ?></div>
      </div>
      <div class="col-md-4">
        <div class="meta-label">Moderatör</div>
        <div class="meta-value">
          <?php foreach ($users as $u) { if ($u['id'] == $meeting['moderator_id']) echo esc($u['name']); } ?>
        </div>
      </div>
    </div>
  </div>

  <!-- ===================================================== -->
  <!--                      MAIN CONTENT                      -->
  <!-- ===================================================== -->
  <div class="row g-4">

    <!-- -------------------------------------------- -->
    <!--           LEFT SIDE: Gündem & Kararlar       -->
    <!-- -------------------------------------------- -->
    <div class="col-lg-8">
      <div class="agenda-card">

        <div class="agenda-header-line">
          <div>
            <h4 class="section-title mb-0">Gündem & Kararlar</h4>
            <div class="agenda-info-text">
              Toplantı sırasında eklenen gündem maddeleri ve bu maddelere ait kararlar
            </div>
          </div>
        </div>

        <div class="agenda-divider"></div>

        <!-- Gündem ekleme formu (partial) -->
        <div class="mb-3">
          <?= view('agenda/_add_item_modal', compact('meeting', 'users')) ?>
        </div>

        <!-- Gündem listesi -->
        <div id="agenda-list" data-meeting-status="<?= esc($meeting['status']) ?>">
          <?= view('agenda/_agenda_list', compact('agenda', 'decisions', 'meeting', 'users', 'participants')) ?>
        </div>
      </div>
    </div>

    <!-- -------------------------------------------- -->
    <!--           RIGHT SIDE: Katılımcılar           -->
    <!-- -------------------------------------------- -->
    <div class="col-lg-4">
      <div class="participants-card">

        <?php 
          $pmap         = array_column($participants, 'status', 'user_id');
          $meetingEnded = ($meeting['status'] === 'ended');
          $userRole     = session('user.role');
          $canEdit      = in_array($userRole, ['manager','moderator','superadmin'], true);
        ?>

        <div class="participants-header">
          <h4 class="section-title mb-0">Katılımcılar</h4>

          <div class="participants-legend">
            <div class="participants-legend-item">
              <i class="bi bi-check" style="color:#16a34a;"></i>
              <span>Geldi</span>
            </div>
            <div class="participants-legend-item">
              <i class="bi bi-x" style="color:#dc2626;"></i>
              <span>Gelmedi</span>
            </div>
            <div class="participants-legend-item">
              <i class="bi bi-clock" style="color:#f97316;"></i>
              <span>İzinli</span>
            </div>
          </div>
        </div>

        <div class="participant-list">
          <?php foreach ($users as $u): 
            $status = $pmap[$u['id']] ?? 'geldi';
          ?>
            <div class="participant-row">

              <div class="participant-left">

                <div class="status-buttons-group">

                  <!-- GELMEDİ -->
                  <div
                    class="status-btn <?= $status === 'gelmedi' ? 'active' : '' ?>"
                    data-user-id="<?= $u['id'] ?>"
                    data-status="gelmedi"
                    <?= (!$canEdit || $meetingEnded) ? 'style="pointer-events:none;opacity:.5"' : '' ?>
                  >
                    <i class="bi bi-x"></i>
                  </div>

                  <!-- MAZERETLİ / İZİNLİ -->
                  <div
                    class="status-btn <?= $status === 'izinli' ? 'active' : '' ?>"
                    data-user-id="<?= $u['id'] ?>"
                    data-status="izinli"
                    <?= (!$canEdit || $meetingEnded) ? 'style="pointer-events:none;opacity:.5"' : '' ?>
                  >
                    <i class="bi bi-clock"></i>
                  </div>

                  <!-- GELDİ -->
                  <div
                    class="status-btn <?= $status === 'geldi' ? 'active' : '' ?>"
                    data-user-id="<?= $u['id'] ?>"
                    data-status="geldi"
                    <?= (!$canEdit || $meetingEnded) ? 'style="pointer-events:none;opacity:.5"' : '' ?>
                  >
                    <i class="bi bi-check"></i>
                  </div>

                </div>

                <div class="status-text">
                  <?=
                    $status === 'geldi'      ? 'Geldi'   :
                    ($status === 'izinli' ? 'İzinli'  : 'Gelmedi')
                  ?>
                </div>

              </div>

              <div class="text-end">
                <div class="participant-name"><?= esc($u['name']) ?></div>
                <div class="participant-muted">
                  <!-- istersen buraya rol / birim bilgisini ekleyebilirsin -->
                </div>
              </div>

            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

  </div>

  <!-- ===================================================== -->
  <!--                     END MEETING BUTTON                 -->
  <!-- ===================================================== -->
  <div class="figma-card">
    <div class="end-meeting-actions">

      <div>
        <h4 class="section-title mb-1">Toplantı Durumu</h4>
        <div class="end-info-text">
          Toplantıyı sonlandırdığınızda katılımcı durumu ve kararlar kilitlenir.
        </div>
      </div>

      <div>
        <?php if ($meeting['status'] !== 'ended' && in_array(session('user.role'), ['manager','superadmin'], true)): ?>

          <form action="<?= base_url('meetings/end/' . $meeting['id']) ?>" method="post" class="d-inline">
            <?= csrf_field() ?>
            <button class="btn btn-danger px-4"
              onclick="return confirm('Bu toplantıyı sonlandırmak istediğinizden emin misiniz?');">
              Toplantıyı Sonlandır
            </button>
          </form>

        <?php elseif ($meeting['status'] === 'ended'): ?>

          <span class="badge bg-secondary px-3 py-2">Toplantı Sonlandırıldı</span>

        <?php else: ?>

          <span class="badge bg-info px-3 py-2">Sadece görüntüleme izni</span>

        <?php endif; ?>
      </div>

    </div>
  </div>

</div>

<!-- ========================================================== -->
<!--                      JAVASCRIPT AREA                       -->
<!-- ========================================================== -->


<script>
document.addEventListener('DOMContentLoaded', () => {

  const csrfHash   = '<?= csrf_hash() ?>';
  const csrfName   = '<?= csrf_token() ?>';
  const meetingId  = <?= (int) $meeting['id'] ?>;
  const meetingEnded = "<?= esc($meeting['status']) ?>" === "ended";
  const userRole   = "<?= session('user.role') ?>";
  const canEdit    = ['manager','moderator','superadmin'].includes(userRole);

  if (canEdit && !meetingEnded) {
    document.querySelectorAll('.status-btn').forEach(btn => {
      btn.addEventListener('click', function () {

        const userId = this.dataset.userId;
        const status = this.dataset.status;

        // aynı satırdaki diğer butonlardan active sınıfını kaldır
        const row = this.closest('.participant-row');
        row.querySelectorAll('.status-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        // text güncelle
        const label = row.querySelector('.status-text');
        label.textContent =
          status === 'geldi'     ? 'Geldi' :
          status === 'izinli' ? 'İzinli' : 'Gelmedi';

        // backend'e gönder
        const payload = {
          meeting_id: meetingId,
          user_id: userId,
          status: status
        };

        fetch("<?= base_url('meetings/updateParticipantStatus') ?>", {
        method: "POST",
        credentials: "same-origin",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
          "<?= csrf_token() ?>": "<?= csrf_hash() ?>",
          meeting_id: meetingId,
          user_id: userId,
          status: status
        })
        }).catch(() => {});
      });
    });
  }

});
</script>

        

<!--        

<script>
document.addEventListener('DOMContentLoaded', () => {

  const meetingId = <?= (int) $meeting['id'] ?>;

  document.querySelectorAll('.status-btn').forEach(btn => {
    btn.addEventListener('click', function () {

      const userId = this.dataset.userId;
      const status = this.dataset.status;

      fetch("<?= base_url('meetings/updateParticipantStatus') ?>", {
        method: "POST",
        credentials: "same-origin",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
          "<?= csrf_token() ?>": "<?= csrf_hash() ?>",
          meeting_id: meetingId,
          user_id: userId,
          status: status
        })
      });
    });
  });

});
</script>
        -->

<?= $this->endSection() ?>
