{{-- SweetAlert2 Default Flash Notifications & Logout Confirmation --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function() {
    function fireAlerts() {
        if (typeof Swal === 'undefined') {
            setTimeout(fireAlerts, 50);
            return;
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: {!! json_encode(session('success')) !!},
                timer: 3500,
                showConfirmButton: true
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: {!! json_encode(session('error')) !!}
            });
        @elseif($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: {!! json_encode($errors->first()) !!}
            });
        @endif

        @if(session('warning') || session('alert'))
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: {!! json_encode(session('warning') ?? session('alert')) !!}
            });
        @endif

        @if(session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: {!! json_encode(session('info')) !!}
            });
        @endif
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fireAlerts);
    } else {
        fireAlerts();
    }

    // Tangani konfirmasi pop-up saat pengguna mau logout
    document.addEventListener('submit', function(e) {
        var form = e.target;
        if (form && form.action && form.action.indexOf('/logout') !== -1) {
            if (form.dataset.confirmed === 'true') {
                return;
            }
            e.preventDefault();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi Logout',
                    text: 'Yakin mau log out dari akun?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Logout',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(function(result) {
                    if (result.isConfirmed) {
                        form.dataset.confirmed = 'true';
                        form.submit();
                    }
                });
            } else {
                if (confirm('Yakin mau log out dari akun?')) {
                    form.dataset.confirmed = 'true';
                    form.submit();
                }
            }
        }
    });
})();
</script>