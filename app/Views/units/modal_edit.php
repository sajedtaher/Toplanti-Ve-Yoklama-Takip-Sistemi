<style>
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
  .btn-modal-save {
    background: #4f46e5 !important;
    color: white !important;
    border-radius: 999px;
    padding-inline: 20px;
    font-weight: 600;
}
.btn-modal-save:hover {
    background: #4338ca !important;
}

</style>

<div class="modal-dialog modal-dialog-centered">
  <div class="modal-content figma-modal-content">

    <div class="modal-header figma-modal-header">
      <h5 class="modal-title figma-modal-title">Birim Düzenle</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body figma-modal-body">
      <form method="post" action="<?= base_url('units/update/' . $unit['id']) ?>">
        <?= csrf_field() ?>

        <div class="mb-3">
          <label class="figma-label">Birim Adı</label>
          <input type="text" name="name" class="form-control figma-input" value="<?= esc($unit['name']) ?>" required>
        </div>

        <div class="mb-3">
          <label class="figma-label">Yönetici</label>
          <select name="manager_id" class="form-select figma-select">
            <option value="">(Atanmayabilir)</option>
            <?php foreach ($managers as $m): ?>
              <option value="<?= $m['id'] ?>" <?= $unit['manager_id']==$m['id']?'selected':'' ?>>
                <?= esc($m['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="modal-footer figma-modal-footer d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">İptal</button>
          <button type="submit" class="btn btn-modal-save text-white">Kaydet</button>
        </div>

      </form>
    </div>

  </div>
</div>
