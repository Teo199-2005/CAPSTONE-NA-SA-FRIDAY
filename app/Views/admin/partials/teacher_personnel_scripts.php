<script>
(function () {
  function digitsOnly(el, maxLen) {
    el.addEventListener('input', function () {
      this.value = this.value.replace(/\D/g, '').slice(0, maxLen);
    });
  }

  document.querySelectorAll('.teacher-prc-license').forEach(function (el) {
    digitsOnly(el, 7);
  });

  document.querySelectorAll('.teacher-philsys').forEach(function (el) {
    digitsOnly(el, 12);
  });

  document.querySelectorAll('.teacher-name').forEach(function (el) {
    el.addEventListener('input', function () {
      this.value = this.value.replace(/[^a-zA-Z\s\-\']/g, '');
    });
  });

  document.querySelectorAll('.teacher-phone').forEach(function (el) {
    el.addEventListener('input', function () {
      this.value = this.value.replace(/[^0-9\s\-\(\)\+]/g, '');
    });
  });

  var tinEl = document.getElementById('tin');
  if (tinEl) {
    tinEl.addEventListener('input', function () {
      var d = this.value.replace(/\D/g, '').slice(0, 9);
      if (d.length <= 3) {
        this.value = d;
      } else if (d.length <= 6) {
        this.value = d.slice(0, 3) + '-' + d.slice(3);
      } else {
        this.value = d.slice(0, 3) + '-' + d.slice(3, 6) + '-' + d.slice(6);
      }
    });
  }

  var toggle = document.getElementById('togglePassword');
  if (toggle) {
    toggle.addEventListener('click', function () {
      var passwordField = document.getElementById('password');
      var toggleIcon = document.getElementById('toggleIcon');
      if (!passwordField) return;
      if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.replace('bi-eye', 'bi-eye-slash');
      } else {
        passwordField.type = 'password';
        toggleIcon.classList.replace('bi-eye-slash', 'bi-eye');
      }
    });
  }
})();
</script>
