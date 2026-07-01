<!-- ================================================ -->
<!--   MEETING EDIT MODAL — Figma Style               -->
<!-- ================================================ -->

<style>
  .modal-figma-card {
    padding: 24px 24px 10px 24px;
  }

  .modal-title-text {
    font-size: 24px;
    font-weight: 600;
    color: #111827;
  }

  .modal-subtitle-text {
    font-size: 14px;
    color: #6b7280;
    margin-top: 4px;
    margin-bottom: 20px;
  }

  .figma-label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 4px;
  }

  .figma-input,
  .figma-select {
    border-radius: 12px;
    border: 1px solid #d1d5db;
    padding: 10px 14px;
    font-size: 15px;
  }

  .figma-input:focus,
  .figma-select:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 2px rgba(79,70,229,0.18);
  }

  .btn-cancel-figma {
    background: #e5e7eb;
    color: #374151;
    border-radius: 999px;
    padding: 8px 20px;
    font-weight: 600;
  }

  .btn-save-figma {
    background: #4f46e5;
    color: #ffffff;
    border-radius: 999px;
    padding: 8px 26px;
    font-weight: 600;
  }

  .btn-save-figma:hover {
    background: #4338ca;
  }
</style>


<div class="modal-header">
  <h5 class="modal-title modal-title-text">Toplantıyı Düzenle</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>


<div class="modal-body modal-figma-card">

  <p class="modal-subtitle-text">
    Başlangıç zamanını ve moderatörü güncelleyebilirsiniz.
  </p>

  <form id="editMeetingForm">
    <?= csrf_field() ?>

    <input type="hidden" name="id" value="<?= $meeting['id'] ?>">

    <div class="row g-3">

      <!-- Başlangıç -->
      <div class="col-md-12">
        <label class="figma-label">Başlangıç</label>
        <input 
          type="datetime-local"
          name="start_at"
          class="form-control figma-input"
          value="<?= date('Y-m-d\TH:i', strtotime($meeting['start_at'])) ?>"
          required
        >
      </div>

      <!-- Moderatör -->
      <div class="col-md-12">
        <label class="figma-label">Moderatör</label>
        <select name="moderator_id" class="form-select figma-select" required>
          <?php foreach($users as $u): ?>
            <option value="<?= $u['id'] ?>" 
              <?= $meeting['moderator_id']==$u['id']?'selected':'' ?>>
              <?= esc($u['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

    </div>

  </form>
</div>


<div class="modal-footer">
  <button class="btn btn-cancel-figma" data-bs-dismiss="modal">İptal</button>
  <button class="btn btn-save-figma" id="saveEditMeetingBtn">Kaydet</button>
</div>
