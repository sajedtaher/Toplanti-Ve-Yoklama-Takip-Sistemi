<!-- app/Views/meetings/modal_create.php -->

<style>
  /* ===========================
     Figma — Create Meeting Modal
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

<div class="modal fade" id="createMeetingModal" tabindex="-1" aria-labelledby="createMeetingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content figma-modal-content">

      <div class="modal-header figma-modal-header">
        <h5 class="modal-title figma-modal-title" id="createMeetingModalLabel">Yeni Toplantı</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>

      <div class="modal-body figma-modal-body">
        <form id="modalCreateMeetingForm" 
              method="post" 
              action="<?= base_url('meetings/store') ?>">
          <?= csrf_field() ?>

          <!-- 📅 Başlangıç Zamanı -->
          <div class="mb-3">
            <label class="figma-label">Başlangıç</label>
            <input class="form-control figma-input" 
                   type="datetime-local" 
                   name="start_at" 
                   value="<?= date('Y-m-d\TH:i') ?>" 
                   required>
          </div>

          <!-- 🏢 Birim Seçimi (Sadece superadmin ve Tüm Birimler seçiliyse) -->
          <?php if (isset($showUnitSelect) && $showUnitSelect): ?>
            <div class="mb-3">
              <label class="figma-label">Birim</label>
              <select name="unit_id" id="unitSelect" class="form-select figma-select" required>
                <option value="" disabled selected hidden>Birim seçiniz</option>
                <?php foreach($units as $unit): ?>
                  <option value="<?= esc($unit['id']) ?>"><?= esc($unit['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          <?php endif; ?>

          <!-- 🧑‍🏫 Moderatör Seçimi -->
          <div class="mb-3">
            <label class="figma-label">Moderatör</label>
            <select name="moderator_id" id="moderatorSelect" class="form-select figma-select" required>
              <option value="" disabled selected hidden>Moderatör seçiniz</option>
              <?php foreach($users as $u): ?>
                <option value="<?= esc($u['id']) ?>"><?= esc($u['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Formun butonları footer'da, o yüzden burada buton yok -->

        </form>
      </div>

      <div class="modal-footer figma-modal-footer d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">İptal</button>
        <button type="submit" class="btn btn-primary btn-modal-save text-white" form="modalCreateMeetingForm">
          Kaydet
        </button>
      </div>

    </div>
  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function(){
  const form = document.getElementById('modalCreateMeetingForm');

  form.addEventListener('submit', function(e){
    e.preventDefault();

    const data = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: data
    })
    .then(async r => {
      const text = await r.text();
      if (!r.ok) {
        console.error('HTTP error', r.status, text);
        alert('Sunucu hatası. Konsola bakınız.');
        return null;
      }
      try {
        return JSON.parse(text);
      } catch (err) {
        console.error('Beklenmeyen cevap (non-JSON):', text);
        alert('Sunucudan beklenmeyen cevap. Konsolu kontrol et.');
        return null;
      }
    })
    .then(json => {
      if (!json) return;
      if (json.success) {
        const modalEl = document.getElementById('createMeetingModal');
        const bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        bsModal.hide();

        if (json.meeting_id) {
          window.location.href = "<?= base_url('meetings') ?>/" + json.meeting_id;
        } else {
          location.reload();
        }
      } else {
        console.error('Sunucu başarı false döndü:', json);
        alert('Toplantı oluşturulamadı. Detaylar konsolda.');
      }
    })
    .catch(err => {
      console.error('Fetch hatası', err);
      alert('İstek sırasında hata. Konsolu kontrol et.');
    });
  });
});

// 🎨 Placeholder (ilk seçenek) rengini gri / soluk yapmak
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

// =============================
// 🆕 Birim seçilince moderatörleri yükle
// =============================
document.addEventListener("DOMContentLoaded", () => {
    
    const unitSelect = document.getElementById("unitSelect");
    const moderatorSelect = document.getElementById("moderatorSelect");

    if (!unitSelect || !moderatorSelect) return;

    moderatorSelect.innerHTML = `<option value="">Önce birim seçiniz</option>`;

    unitSelect.addEventListener("change", () => {
        const unitId = unitSelect.value;

        if (!unitId) {
            moderatorSelect.innerHTML = `<option value="">Önce birim seçiniz</option>`;
            return;
        }

        fetch("<?= base_url('meetings/getUsersByUnit') ?>/" + unitId)
            .then(r => r.json())
            .then(data => {
                moderatorSelect.innerHTML = "";

                if (data.length === 0) {
                    moderatorSelect.innerHTML = `<option value="">Bu birimde kullanıcı yok</option>`;
                    return;
                }

                data.forEach(user => {
                    moderatorSelect.innerHTML += `
                        <option value="${user.id}">${user.name}</option>
                    `;
                });
            });
    });
});


</script>
