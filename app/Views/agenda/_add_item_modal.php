<?php if (in_array(session('user.role'), ['manager','superadmin'], true) && $meeting['status'] !== 'ended'): ?>
<form method="post"
      action="<?= base_url('meetings/' . $meeting['id'] . '/agenda') ?>"
      class="row g-2">
  <?= csrf_field() ?>

  <!-- Gündem maddesi -->
  <div class="col-md-6">
    <label for="title" class="form-label">Gündem maddesi</label>
    <input class="form-control" type="text" name="title" id="title"
           placeholder="Gündem maddesi" required />
  </div>

  <!-- Madde yazanı seçimi -->
  <div class="col-md-4">
    <label for="author_id" class="form-label">Madde yazanı</label>
    <select name="author_id" id="author_id" class="form-select">
      <?php
      $usersForSelect = $users ?? (new \App\Models\UserModel())
        ->where('unit_id', $meeting['unit_id'] ?? null)
        ->findAll();
      foreach($usersForSelect as $u): ?>
        <option value="<?= $u['id'] ?>" <?= (session('user.id') == $u['id']) ? 'selected' : '' ?>>
          <?= esc($u['name']) ?> (<?= esc($u['role'] ?? 'uye') ?>)
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- Ekle butonu -->
  <div class="col-md-2 d-flex align-items-end">
    <button class="btn btn-secondary w-100">Ekle</button>
  </div>
</form>
<?php endif; ?>
