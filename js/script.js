/**
 * script.js - Client-side form validation for Inventaris Laboratorium
 * Highlights required fields in red and displays "Harap di isi dulu" if they are empty upon form submission.
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Fungsi untuk mengatur atribut 'novalidate' pada semua form
    function disableNativeValidation() {
        document.querySelectorAll('form').forEach(function (form) {
            form.setAttribute('novalidate', 'true');
        });
    }

    // Jalankan saat load
    disableNativeValidation();

    // Jalankan juga saat mendeteksi interaksi klik pada tombol submit (untuk mengantisipasi form dinamis)
    document.addEventListener('click', function (event) {
        const target = event.target;
        if (target.type === 'submit' || target.tagName === 'BUTTON') {
            const form = target.closest('form');
            if (form) {
                form.setAttribute('novalidate', 'true');
            }
        }
    });

    // 2. Fungsi untuk memvalidasi input tunggal
    function validateInput(input) {
        let isValid = true;

        // Cek jika field kosong untuk input wajib
        if (input.required) {
            if (input.tagName === 'SELECT') {
                if (input.value === "" || input.value === null) {
                    isValid = false;
                }
            } else if (input.type === 'checkbox' || input.type === 'radio') {
                if (!input.checked) {
                    isValid = false;
                }
            } else {
                if (input.value.trim() === "") {
                    isValid = false;
                }
            }
        }

        // Cek validasi angka (min/max) jika bertipe number
        if (isValid && input.type === 'number') {
            const val = parseFloat(input.value);
            const min = input.getAttribute('min');
            const max = input.getAttribute('max');
            if (min !== null && !isNaN(val)) {
                if (val < parseFloat(min)) {
                    isValid = false;
                }
            }
            if (max !== null && !isNaN(val)) {
                if (val > parseFloat(max)) {
                    isValid = false;
                }
            }
        }

        // Terapkan class, style, dan pesan error jika tidak valid
        let errorEl = input.nextElementSibling;
        const hasErrorMsg = errorEl && errorEl.classList.contains('validation-error-msg');

        if (!isValid) {
            input.classList.add('is-invalid');
            // Tambahkan style visual merah yang jelas
            input.style.borderColor = '#dc3545';
            input.style.boxShadow = '0 0 0 0.25rem rgba(220, 53, 69, 0.25)';

            // Tambahkan pesan error jika belum ada
            if (!hasErrorMsg) {
                errorEl = document.createElement('div');
                errorEl.className = 'invalid-feedback validation-error-msg';
                errorEl.innerText = 'Harap di isi dulu';
                errorEl.style.display = 'block';
                errorEl.style.color = '#dc3545';
                errorEl.style.fontSize = '0.875em';
                errorEl.style.marginTop = '0.25rem';
                input.after(errorEl);
            }
        } else {
            input.classList.remove('is-invalid');
            input.style.borderColor = '';
            input.style.boxShadow = '';

            // Hapus pesan error jika ada
            if (hasErrorMsg) {
                errorEl.remove();
            }
        }

        return isValid;
    }

    // 3. Tangani event submit pada form (menggunakan Event Delegation agar mendukung form dinamis/modal)
    document.addEventListener('submit', function (event) {
        const form = event.target;
        const requiredInputs = form.querySelectorAll('[required]');

        // Jika form tidak memiliki input required, lewati
        if (requiredInputs.length === 0) return;

        // Set novalidate lagi untuk memastikan
        form.setAttribute('novalidate', 'true');

        let isFormValid = true;
        let firstInvalidInput = null;

        requiredInputs.forEach(function (input) {
            const isInputValid = validateInput(input);
            if (!isInputValid) {
                isFormValid = false;
                if (!firstInvalidInput) {
                    firstInvalidInput = input;
                }
            }
        });

        // Jika terdapat input yang tidak valid, batalkan proses pengiriman form
        if (!isFormValid) {
            event.preventDefault();
            event.stopPropagation();
            if (firstInvalidInput) {
                firstInvalidInput.focus();
            }
        }
    });

    // 4. Validasi real-time saat user mengetik atau mengubah data (menghilangkan warna merah ketika sudah diisi)
    document.addEventListener('input', function (event) {
        const input = event.target;
        if (input.hasAttribute('required') || input.classList.contains('is-invalid')) {
            validateInput(input);
        }
    });

    document.addEventListener('change', function (event) {
        const input = event.target;
        if (input.hasAttribute('required') || input.classList.contains('is-invalid')) {
            validateInput(input);
        }
    });
});
