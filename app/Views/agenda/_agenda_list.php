<?php
$decisionByAgenda = [];
if (!empty($decisions)) {
  foreach ($decisions as $d) {
    $decisionByAgenda[$d['agenda_item_id']] = $d;
  }
}
?>
<div id="agenda-list" class="list-group" data-meeting-status="<?= esc($meeting['status']) ?>">
  <?php foreach ($agenda as $item): ?>
    <div class="list-group-item">
      <div class="d-flex justify-content-between align-items-center">
        <!-- 🟣 GÜNDEM BAŞLIĞI -->
        <strong 
          class="agenda-title" 
          data-id="<?= $item['id'] ?>" 
          ondblclick="editAgendaTitle(this)"
        >
          <?= esc($item['title']) ?>
        </strong>
        <small class="text-muted">
          (<?= esc($item['author_name']) ?> - <?= esc($item['author_role']) ?>)
        </small>
      </div>

      <?php
      $hasDecision = isset($decisionByAgenda[$item['id']]) && !empty($decisionByAgenda[$item['id']]['decision_text']);
      $isEnded = ($meeting['status'] === 'ended');
      $role = session('user.role');
      $isEditable = in_array($role, ['manager','superadmin']);
      ?>

      <!-- 🟠 KARAR ALANI -->
      <?php if ($isEditable && !$isEnded && !$hasDecision): ?>
      <div class="decision-box mt-2">
      <form method="post"
            action="<?= base_url('agenda/' . $item['id'] . '/decision') ?>"
            class="d-flex gap-2 align-items-start">
          <?= csrf_field() ?>
          <textarea
            name="decision_text"
            class="form-control karar-textarea"
            rows="1"
            style="overflow:hidden; resize:none; width:100%;"
            placeholder="Karar metni"
            oninput="autoResize(this)"
          ></textarea>

          <button type="button" class="btn btn-outline-primary btn-sm save-decision-btn">Kaydet</button>
          <button type="button" class="btn btn-outline-secondary btn-sm cancel-decision-btn">İptal</button>
        </form>
      </div>
      <?php endif; ?>

      <?php if ($hasDecision): ?>
      <div class="mt-2 karar-display" ondblclick="enableEdit(this)">
        <small>Karar:<br><?= nl2br(esc($decisionByAgenda[$item['id']]['decision_text'])) ?></small>
      </div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>

<!--------------------------- JavaScript -------------------------------------->

<script>
// 1️⃣ Textarea otomatik büyüme
function autoResize(textarea) {
  textarea.style.height = 'auto';
  textarea.style.height = (textarea.scrollHeight) + 'px';
}

// 2️⃣ Kaydet / İptal butonları davranışı
document.addEventListener("DOMContentLoaded", function() {
  const meetingStatus = document.getElementById("agenda-list").dataset.meetingStatus;

  // 🟢 Kaydet butonu
  document.querySelectorAll(".save-decision-btn").forEach(button => {
    button.addEventListener("click", function() {
      const form = this.closest("form");
      const textarea = form.querySelector(".karar-textarea");
      const text = textarea.value.trim();
      if (!text) return;

      const formData = new FormData(form);
      fetch(form.action, { method: "POST", body: formData })
        .catch(error => console.error("Hata:", error));

      const formatted = text.replace(/\n/g, "<br>");
      const displayDiv = document.createElement("div");
      displayDiv.classList.add("mt-2", "karar-display");
      displayDiv.innerHTML = `<small>Karar:<br>${formatted}</small>`;
      displayDiv.ondblclick = function() { enableEdit(displayDiv); };

      // Form içindekileri kaldır
      textarea.remove();
      form.querySelectorAll(".save-decision-btn, .cancel-decision-btn").forEach(btn => btn.remove());
      form.insertAdjacentElement("afterend", displayDiv);
    });
  });

  // 🔵 İptal butonu
  document.querySelectorAll(".cancel-decision-btn").forEach(btn => {
    btn.addEventListener("click", function() {
      const form = this.closest("form");
      const textarea = form.querySelector(".karar-textarea");
      textarea.value = ""; // temizle
    });
  });
});

// 3️⃣ Karar düzenleme (Enter artık sadece satır atlatır!)
function enableEdit(displayDiv) {
  const meetingStatus = document.getElementById("agenda-list").dataset.meetingStatus;
  const userRole = "<?= session('user.role') ?>";
  if (meetingStatus === "ended" || userRole === "member") return;

  const html = displayDiv.innerHTML;
  const text = html
    .replace(/<small>Karar:<br>/, '')
    .replace(/<\/small>/, '')
    .replace(/<br\s*\/?>/gi, '\n')
    .trim();

  const textarea = document.createElement("textarea");
  textarea.className = "form-control karar-textarea";
  textarea.rows = 1; // 🟢 Başta tek satır
  textarea.style.overflow = "hidden";
  textarea.style.resize = "none";
  textarea.style.width = "100%";
  textarea.value = text;
  textarea.oninput = function() { autoResize(this); };

  const saveButton = document.createElement("button");
  saveButton.textContent = "Kaydet";
  saveButton.className = "btn btn-outline-primary btn-sm mt-2";

  const cancelButton = document.createElement("button");
  cancelButton.textContent = "İptal";
  cancelButton.className = "btn btn-outline-secondary btn-sm mt-2 ms-2";

  // 🟡 Artık Enter kaydetmiyor — sadece yeni satır ekler
  // (Yani burada Enter event yok 👇)

  saveButton.onclick = function() {
    const newText = textarea.value.trim();
    if (!newText) return;

    const formatted = newText.replace(/\n/g, "<br>");
    displayDiv.innerHTML = `<small>Karar:<br>${formatted}</small>`;
    displayDiv.style.display = "block";
    textarea.remove();
    saveButton.remove();
    cancelButton.remove();
  };

  cancelButton.onclick = function() {
    textarea.remove();
    saveButton.remove();
    cancelButton.remove();
    displayDiv.style.display = "block";
  };

  displayDiv.style.display = "none";
  displayDiv.insertAdjacentElement("afterend", textarea);
  textarea.insertAdjacentElement("afterend", saveButton);
  saveButton.insertAdjacentElement("afterend", cancelButton);

  autoResize(textarea);
  textarea.focus();
}

// 4️⃣ Gündem başlığı düzenleme (Enter = Kaydet)
function editAgendaTitle(element) {
  const meetingStatus = document.getElementById("agenda-list").dataset.meetingStatus;
  if (meetingStatus === "ended") return;

  const oldText = element.textContent.trim();
  const input = document.createElement("input");
  input.type = "text";
  input.className = "form-control form-control-sm";
  input.style.width = "300%";
  input.value = oldText;

  // Enter tuşu ile kaydet
  input.addEventListener("keydown", e => {
    if (e.key === "Enter") {
      e.preventDefault();
      input.blur();
    }
  });

  input.addEventListener("blur", function() {
    const newText = this.value.trim() || oldText;
    element.textContent = newText;
    this.remove();
  });

  element.textContent = "";
  element.appendChild(input);
  input.focus();
}
</script>
