$(document).ready(function () {
    $(document).on('click', '.protected-link', function (e) {
        e.preventDefault();
        const targetUrl = $(this).data('target-url');

        Swal.fire({
            title: 'Enter Admin Password',
            input: 'password',
            inputLabel: 'Password',
            inputPlaceholder: 'Enter admin password',
            inputAttributes: { autocapitalize: 'off', autocorrect: 'off' },
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            showLoaderOnConfirm: true,
            preConfirm: (password) => {
                if (!password) {
                    Swal.showValidationMessage('Password is required');
                    return;
                }

                return fetch('assets/php/table-helper/verify_admin_password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `admin_password=${encodeURIComponent(password)}`
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status !== 'success') {
                        throw new Error(res.message || 'Invalid password');
                    }
                    return true;
                })
                .catch(err => Swal.showValidationMessage(err.message));
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = targetUrl;
            }
        });
    });
});