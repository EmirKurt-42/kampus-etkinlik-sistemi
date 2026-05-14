document.addEventListener('DOMContentLoaded', function () {

    // Tüm formları al
    var formlar = document.querySelectorAll('form');

    formlar.forEach(function (form) {

        form.addEventListener('submit', function (e) {

            // Zorunlu alanları kontrol et
            var bos = false;
            form.querySelectorAll('input[required], textarea[required], select[required]').forEach(function (alan) {
                if (alan.value.trim() === '') {
                    alan.style.border = '1px solid red';
                    bos = true;
                } else {
                    alan.style.border = '1px solid #ccc';
                }
            });

            if (bos) {
                e.preventDefault();
                alert('Lütfen zorunlu alanları doldurun.');
                return;
            }

            // E-posta formatı kontrolü
            var emailAlan = form.querySelector('input[type="email"]');
            if (emailAlan) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailAlan.value)) {
                    e.preventDefault();
                    alert('Geçerli bir e-posta adresi girin.');
                    emailAlan.style.border = '1px solid red';
                    return;
                }
            }

            // Şifre uzunluk kontrolü
            var sifreAlan = form.querySelector('input[type="password"]');
            if (sifreAlan && sifreAlan.value.length < 6) {
                e.preventDefault();
                alert('Şifre en az 6 karakter olmalıdır.');
                sifreAlan.style.border = '1px solid red';
                return;
            }
        });
    });
});