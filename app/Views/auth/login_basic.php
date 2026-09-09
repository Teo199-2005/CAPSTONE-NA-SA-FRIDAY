<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <h1 class="h4 mb-3">Login to CSCS SMS</h1>

          <?php if ($error = session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc($error) ?></div>
          <?php endif; ?>
          <?php if ($success = session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc($success) ?></div>
          <?php endif; ?>

          <form method="post" action="<?= base_url('login') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required />
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" required />
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1" />
              <label for="remember" class="form-check-label">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
          </form>

          <div class="d-flex justify-content-between align-items-center mt-3">
            <a href="<?= base_url('auth/forgot') ?>">Forgot Password?</a>
            <a href="<?= base_url('register') ?>">Register as Student</a>
          </div>

          <div class="text-end mt-3">
            <div class="dropdown d-inline-block">
              <button class="btn btn-accent btn-sm dropdown-toggle" type="button" id="demoLoginMenu" data-bs-toggle="dropdown" aria-expanded="false">
                Demo Login
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="demoLoginMenu">
                <li><a class="dropdown-item" href="<?= base_url('login/demo/admin') ?>">Admin</a></li>
                <li><a class="dropdown-item" href="<?= base_url('login/demo/teacher') ?>">Teacher</a></li>
                <li><a class="dropdown-item" href="<?= base_url('login/demo/student') ?>">Student</a></li>
                <li><a class="dropdown-item" href="<?= base_url('login/demo/parent') ?>">Parent</a></li>
              </ul>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>




