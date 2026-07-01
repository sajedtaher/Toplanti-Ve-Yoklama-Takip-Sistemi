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
  .figma-modal-footer {
    border-top: 1px solid #f3f4f6;
    padding: 12px 20px 14px 20px;
    display: flex;
    justify-content: end;
    gap: 10px;
}

.btn-modal-cancel {
    background: #e5e7eb;
    border: none;
    border-radius: 999px;
    padding-inline: 16px;
    font-weight: 500;
    color: #374151;
}

.btn-modal-save {
    background: #4f46e5;
    border: none;
    border-radius: 999px;
    padding-inline: 20px;
    font-weight: 600;
    color: white;
}

.btn-modal-save:hover {
    background: #4338ca;
}
  
</style>

<div class="modal fade" id="createUnitModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content figma-modal-content">

      <div class="modal-header figma-modal-header">
        <h5 class="modal-title figma-modal-title">Yeni Birim</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body figma-modal-body">
        <form id="createUnitForm" method="post">
          <?= csrf_field() ?>

          <div class="mb-3">
            <label class="figma-label">Birim Adı</label>
            <input type="text" name="name" class="form-control figma-input" required>
          </div>

          <div class="mb-3">
            <label class="figma-label">Yönetici</label>
            <select name="manager_id" class="form-select figma-select">
              <option value="">(Atanmayabilir)</option>
              <?php foreach ($managers as $m): ?>
                <option value="<?= $m['id'] ?>"><?= esc($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

        </form>
      </div>

      <div class="modal-footer figma-modal-footer d-flex justify-content-end gap-2">
        <button class="btn btn-modal-cancel" data-bs-dismiss="modal">Kapat</button>
        <button class="btn btn-modal-save text-white" form="createUnitForm">Kaydet</button>
      </div>

    </div>
  </div>
</div>

<script>
document.getElementById('createUnitForm').addEventListener('submit', async function(e){
    e.preventDefault();

    let form = this;
    let formData = new FormData(form);

    let res = await fetch("<?= base_url('units/store') ?>", {
        method: "POST",
        body: formData,
        headers: { "X-Requested-With": "XMLHttpRequest" }
    });

    let json = await res.json();

    if (json.success) {
        // modalı kapat
        let modal = bootstrap.Modal.getInstance(document.getElementById('createUnitModal'));
        modal.hide();

        // sayfayı yenile
        location.reload();
    } else {
        alert(json.message);
    }
});
</script>

