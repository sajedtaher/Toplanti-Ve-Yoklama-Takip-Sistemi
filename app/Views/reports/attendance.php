<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$role = $role ?? (session('user')['role'] ?? null);

$mode = $mode ?? 'summary';

$unitSummary = $unitSummary ?? [];
$meetings    = $meetings ?? [];
$reportRows  = $reportRows ?? [];

$attendanceStats = $attendanceStats ?? [
  'geldi_count' => 0,
  'gelmedi_count' => 0,
  'izinli_count' => 0,
  'geldi_percent' => 0,
  'gelmedi_percent' => 0,
  'izinli_percent' => 0,
];
?>

<style>
/* ===============================
   GENEL
================================ */
.attendance-header {
  margin-bottom: 18px;
}

.attendance-header h2 {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 4px;
}

.attendance-header p {
  color: #6b7280;
  margin: 0;
}

.attendance-card {
  background: #ffffff;
  border-radius: 18px;
  padding: 20px;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
}

/* ===============================
   LEGEND
================================ */
.attendance-legend {
  display: flex;
  gap: 14px;
  align-items: center;
  font-size: 14px;
  margin-bottom: 12px;
}

.legend-item {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  font-weight: 500;
  color: #111827;
}

/* Legend içindeki ikonlar da Katılımcılar sayfasındaki butonlarla aynı */
.legend-icon {
  width: 44px;
  height: 26px;
  border-radius: 999px;
  border: 1px solid #e5e7eb;
  background: #ffffff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  line-height: 1;
  color: #6b7280;
}

.legend-icon i {
  pointer-events: none;
  line-height: 1;
}

.legend-geldi .legend-icon {
  background: #dcfce7;
  border-color: #bbf7d0;
  color: #166534;
}

.legend-gelmedi .legend-icon {
  background: #fee2e2;
  border-color: #fecaca;
  color: #b91c1c;
}

.legend-izinli .legend-icon {
  background: #fef3c7;
  border-color: #fde68a;
  color: #92400e;
}

/* ===============================
   TABLO GENEL
================================ */
.attendance-table {
  width: 100%;
  border-collapse: separate;
}

.attendance-table th {
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-weight: 600;
  color: #6b7280;
  padding-bottom: 10px;
  white-space: nowrap;
}

.attendance-table td {
  vertical-align: middle;
}

/* İlk sütun metin sütunu olduğu için sola */
.attendance-table thead th:first-child,
.attendance-table tbody td:first-child {
  text-align: left;
}

/* İlk sütun dışındaki sütunlar ortada */
.attendance-table thead th:not(:first-child),
.attendance-table tbody td:not(:first-child) {
  text-align: center;
}

/* ===============================
   ÖZET TABLO (Tüm Birimler)
================================ */
.attendance-summary-mode .attendance-table {
  border-spacing: 0 10px;
  min-width: unset;
}

.attendance-summary-mode .attendance-table-wrapper {
  overflow-x: hidden;
}

.attendance-summary-mode .attendance-table tbody tr {
  background: #f9fafb;
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
  transition: 0.18s ease;
}

.attendance-summary-mode .attendance-table tbody tr:hover {
  background: #eef2ff;
  transform: scale(1.01);
  box-shadow: 0 6px 16px rgba(0,0,0,0.08);
  cursor: pointer;
}

.attendance-summary-mode .attendance-table td {
  padding: 14px 14px;
  font-size: 14.5px;
  color: #111827;
}

/* Özet tabloda Birim sütunu biraz içeriden başlasın */
.attendance-summary-mode .attendance-table thead th:first-child,
.attendance-summary-mode .attendance-table tbody td:first-child {
  padding-left: 24px;
}

/* ===============================
   DETAY TABLO (Birim seçilince)
================================ */
.attendance-detail-mode .attendance-table {
  border-spacing: 0 6px;
}

.attendance-detail-mode .attendance-table-wrapper {
  overflow-x: auto;
}

.attendance-detail-mode .attendance-table tbody tr {
  background: #f9fafb;
  transition: 0.15s ease;
}

.attendance-detail-mode .attendance-table tbody tr:hover { 
  background: #eef2ff;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.attendance-detail-mode .attendance-table td {
  padding: 10px 12px;
  font-size: 14px;
  color: #111827;
}

/* Detay tabloda katılımcı adı biraz içeriden başlasın */
.attendance-detail-mode .attendance-table thead th:first-child,
.attendance-detail-mode .attendance-table tbody td:first-child {
  padding-left: 18px;
}

/* ===============================
   katılım raporu bildirim kutusu
================================ */

.attendance-info {
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

.attendance-info i {
  font-size: 14px;
}

/* ===============================
   STATUS KUTULARI
================================ */
/* ===============================
   STATUS KUTULARI
   Katılımcılar sayfasındaki status-btn ile aynı
================================ */
.status-box {
  width: 44px;
  height: 26px;
  border-radius: 999px;
  border: 1px solid #e5e7eb;
  background: #ffffff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  line-height: 1;
  color: #6b7280;
  transition: 0.15s ease;
}

.status-box i {
  pointer-events: none;
  line-height: 1;
}

.status-box:hover {
  background: #f9fafb;
  transform: translateY(-1px);
}

/* GELMEDİ - kırmızı pastel */
.status-gelmedi {
  background: #fee2e2;
  border-color: #fecaca;
  color: #b91c1c;
}

/* İZİNLİ - sarı pastel */
.status-izinli {
  background: #fef3c7;
  border-color: #fde68a;
  color: #92400e;
}

/* GELDİ - yeşil pastel */
.status-geldi {
  background: #dcfce7;
  border-color: #bbf7d0;
  color: #166534;
}

/* ===============================
   YÜZDE ROZETİ
================================ */
.percent-badge {
  background: #ede9fe;
  color: #5b21b6;
  border: 1px solid #ddd6fe;
  padding: 4px 11px;
  border-radius: 999px;
  font-weight: 700;
  font-size: 13px;
  display: inline-block;
  min-width: 52px;
  text-align: center;
}

/* ===============================
   RAPOR ÖZET KUTULARI
================================ */
.attendance-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
  margin-top: 24px;
}

.attendance-stat-box {
  border-radius: 14px;
  padding: 18px 20px;
  min-height: 118px;
}

.attendance-stat-title {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 15px;
  font-weight: 600;
  margin-bottom: 12px;
}

.attendance-stat-title i {
  font-size: 20px;
}

.attendance-stat-number {
  font-size: 28px;
  font-weight: 600;
  line-height: 1;
  margin-bottom: 10px;
}

.attendance-stat-average {
  font-size: 13px;
  font-weight: 500;
}

/* Geldi kutusu */
.stat-geldi {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  color: #166534;
}

/* Gelmedi kutusu */
.stat-gelmedi {
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #991b1b;
}

/* İzinli kutusu */
.stat-izinli {
  background: #fffbeb;
  border: 1px solid #fde68a;
  color: #92400e;
}

@media (max-width: 900px) {
  .attendance-stats {
    grid-template-columns: 1fr;
  }
}

</style>

<!-- ===============================
     BAŞLIK
================================= -->
<div class="attendance-header">
  <h3>Katılım Raporu</h3>

  <?php if (($mode ?? null) !== 'summary'): ?>
    <p>Son 10 toplantıya katılım durumlarını görüntüleyin.</p>
  <?php endif; ?>  
</div>

<!-- ===============================
   katılım raporu bildirim kutusu
  ================================ -->

  <?php if (($mode ?? null) === 'summary'): ?>
  <div class="attendance-info">
    <i class="bi bi-info-square-fill"></i>
    <span>Bir birimin detaylı "katılım raporunu" görmek için sol menüden ilgili birimi seçebilir veya satırına tıklayabilirsiniz.</span>
  </div>
<?php endif; ?>

<div class="attendance-card <?= ($mode === 'summary') ? 'attendance-summary-mode' : 'attendance-detail-mode' ?>">

<?php if (($mode ?? null) === 'summary'): ?>

  <!-- ===============================
       ÖZET TABLO (SUPERADMIN)
  ================================= -->
  <div class="attendance-table-wrapper">
    <table class="attendance-table">
      <thead>
        <tr>
          <th>Birim</th>
          <th>Toplantı Sayısı</th>
          <th>Ortalama Katılım</th>
          <th>Son Toplantı</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($unitSummary as $u): ?>
          <tr onclick="window.location='<?= base_url('reports/selectUnit/'.$u['unit_id']) ?>'"
              style="cursor:pointer">
            <td><?= esc($u['unit_name']) ?></td>
            <td><?= $u['meeting_count'] ?></td>
            <td>
              <span class="percent-badge">
                <?= $u['avg_attendance'] ?>%
              </span>
            </td>
            <td><?= $u['last_meeting'] ? date('d M', strtotime($u['last_meeting'])) : '-' ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

<?php else: ?>

  <!-- ===============================
       LEGEND
  ================================= -->
 <div class="attendance-legend">
  <div class="legend-item legend-geldi">
    <span class="legend-icon"><i class="bi bi-check"></i></span>
    Geldi
  </div>

  <div class="legend-item legend-gelmedi">
    <span class="legend-icon"><i class="bi bi-x"></i></span>
    Gelmedi
  </div>

  <div class="legend-item legend-izinli">
    <span class="legend-icon"><i class="bi bi-clock"></i></span>
    İzinli
  </div>
</div>


  <!-- ===============================
       DETAY TABLO
  ================================= -->
  <div class="attendance-table-wrapper">
    <table class="attendance-table">
      <thead>
        <tr>
          <th>Katılımcı</th>
          <?php foreach ($meetings as $m): ?>
            <th><?= date('d M', strtotime($m['start_at'])) ?></th>
          <?php endforeach; ?>
          <th>İstatistik</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reportRows as $row): ?>
          <tr>
            <td><?= esc($row['name']) ?></td>

            <?php foreach ($meetings as $m): ?>
              <?php $status = $row['statuses'][$m['id']] ?? null; ?>
              <td>
                <?php if ($status === 'geldi'): ?>
  <span class="status-box status-geldi">
    <i class="bi bi-check"></i>
  </span>
<?php elseif ($status === 'gelmedi'): ?>
  <span class="status-box status-gelmedi">
    <i class="bi bi-x"></i>
  </span>
<?php elseif ($status === 'izinli'): ?>
  <span class="status-box status-izinli">
    <i class="bi bi-clock"></i>
  </span>
<?php else: ?>
  -
<?php endif; ?>
              </td>
            <?php endforeach; ?>

            <td>
                <span class="percent-badge">
                  <?= $row['percent'] ?>%
                </span>

                <?= $row['present_count'] ?>/<?= $row['meeting_count'] ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
        </table>
  </div>

  <!-- ===============================
       DETAY RAPOR ÖZET KUTULARI
  ================================= -->
  <div class="attendance-stats">

    <div class="attendance-stat-box stat-geldi">
      <div class="attendance-stat-title">
        <i class="bi bi-check-circle"></i>
        <span>Toplam Katılım</span>
      </div>

      <div class="attendance-stat-number">
        <?= $attendanceStats['geldi_count'] ?>
      </div>

      <div class="attendance-stat-average">
        Ortalama: %<?= $attendanceStats['geldi_percent'] ?>
      </div>
    </div>

    <div class="attendance-stat-box stat-gelmedi">
      <div class="attendance-stat-title">
        <i class="bi bi-x"></i>
        <span>Toplam Devamsızlık</span>
      </div>

      <div class="attendance-stat-number">
        <?= $attendanceStats['gelmedi_count'] ?>
      </div>

      <div class="attendance-stat-average">
        Ortalama: %<?= $attendanceStats['gelmedi_percent'] ?>
      </div>
    </div>

    <div class="attendance-stat-box stat-izinli">
      <div class="attendance-stat-title">
        <i class="bi bi-clock"></i>
        <span>Toplam İzinli</span>
      </div>

      <div class="attendance-stat-number">
        <?= $attendanceStats['izinli_count'] ?>
      </div>

      <div class="attendance-stat-average">
        Ortalama: %<?= $attendanceStats['izinli_percent'] ?>
      </div>
    </div>

  </div>

<?php endif; ?>

</div>



<?= $this->endSection() ?>
