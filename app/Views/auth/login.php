<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center mt-5">
  <div class="col-md-4">
    <h3 class="mb-4 text-center">Giriş</h3>

    <!-- ✅ Login form -->
    <form method="post" action="<?= site_url('login') ?>">
      <?= csrf_field() ?> <!-- Bu satır CodeIgniter’in güvenlik token’ını üretir -->

      <div class="mb-3">
        <label class="form-label">E-posta</label>
        <input class="form-control" type="email" name="email" required autofocus />
      </div>

      <div class="mb-3">
        <label class="form-label">Şifre</label>
        <input class="form-control" type="password" name="password" required />
      </div>

      <!-- ✅ Hata mesajı gösterimi -->
      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif; ?>

      <button class="btn btn-primary w-100">Giriş Yap</button>
    </form>
  </div>
</div>

<?= $this->endSection() ?>
