<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php 
  // Bu form artık sadece YENİ toplantı içindir.
  $isEdit = false; 
?>

<style>
  /* ===========================
     Figma — New Meeting Form
     =========================== */

  .meeting-form-wrapper {
    max-width: 720px;
    margin: 0 auto;
  }

  .meeting-form-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px 24px 20px 24px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.06);
  }

  .meeting-form-title {
    font-size: 24px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 6px;
  }

  .meeting-form-subtitle {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 18px;
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

  .meeting-form-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
  }

  .btn-cancel-figma {
    background: #e5e7eb;
    border: none;
    border-radius: 999px;
    padding-inline: 16px;
    font-weight: 500;
    color: #374151;
  }

  .btn-cancel-figma:hover {
    filter: brightness(0.95);
  }

  .btn-save-figma {
    background: #4f46e5;
    border: none;
    border-radius: 999px;
    padding-inline: 20px;
    font-weight: 600;
  }

  .btn-save-figma:hover {
    background: #4338ca;
  }
</style>


<div class="meeting-form-wrapper">
  <div class="meeting-form-card">

    <h3 class="meeting-form-title">Yeni Toplantı</h3>
    <p class="meeting-form-subtitle">
      Toplantı başlangıç zamanını ve moderatörünü belirleyin.
    </p>

    <form method="post" action="<?= base_url('meetings/store') ?>">

      <?= csrf_field() ?>

      <div class="row g-3">

        <!-- Başlangıç -->
        <div class="col-md-6">
          <label class="figma-label">Başlangıç</label>
          <input 
            class="form-control figma-input" 
            type="datetime-local" 
            name="start_at"
            value="<?= date('Y-m-d\TH:i') ?>"
            required
          />
        </div>

        <!-- Moderatör -->
        <div class="col-md-6">
          <label class="figma-label">Moderatör</label>
          <select class="form-select figma-select" name="moderator_id" required>
            <?php foreach($users as $u): ?>
              <option value="<?= $u['id'] ?>">
                <?= esc($u['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

      </div>

      <div class="meeting-form-footer">
        <a href="<?= base_url('meetings') ?>" class="btn btn-cancel-figma">
          İptal
        </a>
        <button type="submit" class="btn btn-primary btn-save-figma text-white">
          Kaydet
        </button>
      </div>

    </form>

  </div>
</div>

<?= $this->endSection() ?>
