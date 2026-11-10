{{-- Modern Floating Toast Notifications Component --}}
@if (session('success') || session('info') || session('error') || $errors->any())
    <div class="kc-toast-container" id="kcToastContainer" aria-live="polite" aria-atomic="true">
        @if (session('success'))
            <div class="kc-toast kc-toast-success" role="alert" data-bs-delay="4500">
                <div class="kc-toast-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="kc-toast-content">
                    <strong class="kc-toast-title">Berhasil</strong>
                    <div class="kc-toast-message">{{ session('success') }}</div>
                </div>
                <button type="button" class="kc-toast-close" onclick="dismissKcToast(this.closest('.kc-toast'))" aria-label="Tutup">
                    <i class="bi bi-x"></i>
                </button>
                <div class="kc-toast-progress kc-toast-progress-success"></div>
            </div>
        @endif

        @if (session('info'))
            <div class="kc-toast kc-toast-info" role="alert" data-bs-delay="4500">
                <div class="kc-toast-icon">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <div class="kc-toast-content">
                    <strong class="kc-toast-title">Pemberitahuan</strong>
                    <div class="kc-toast-message">{{ session('info') }}</div>
                </div>
                <button type="button" class="kc-toast-close" onclick="dismissKcToast(this.closest('.kc-toast'))" aria-label="Tutup">
                    <i class="bi bi-x"></i>
                </button>
                <div class="kc-toast-progress kc-toast-progress-info"></div>
            </div>
        @endif

        @if ($errors->any())
            <div class="kc-toast kc-toast-danger" role="alert" data-bs-delay="5500">
                <div class="kc-toast-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="kc-toast-content">
                    <strong class="kc-toast-title">Perhatian</strong>
                    @if ($errors->count() > 1)
                        <ul class="kc-toast-list mb-0 ps-3 small text-secondary">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="kc-toast-message">{{ $errors->first() }}</div>
                    @endif
                </div>
                <button type="button" class="kc-toast-close" onclick="dismissKcToast(this.closest('.kc-toast'))" aria-label="Tutup">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="kc-toast-progress kc-toast-progress-danger"></div>
            </div>
        @elseif (session('error'))
            <div class="kc-toast kc-toast-danger" role="alert" data-bs-delay="5000">
                <div class="kc-toast-icon">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div class="kc-toast-content">
                    <strong class="kc-toast-title">Terjadi Kesalahan</strong>
                    <div class="kc-toast-message">{{ session('error') }}</div>
                </div>
                <button type="button" class="kc-toast-close" onclick="dismissKcToast(this.closest('.kc-toast'))" aria-label="Tutup">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="kc-toast-progress kc-toast-progress-danger"></div>
            </div>
        @endif
    </div>

    <script>
        function dismissKcToast(el) {
            if (!el) return;
            el.classList.add('kc-toast-exit');
            setTimeout(() => {
                el.remove();
                const container = document.getElementById('kcToastContainer');
                if (container && container.children.length === 0) {
                    container.remove();
                }
            }, 300);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const toasts = document.querySelectorAll('.kc-toast');
            toasts.forEach(function (toast) {
                const delay = parseInt(toast.getAttribute('data-bs-delay') || '4500', 10);
                setTimeout(function () {
                    dismissKcToast(toast);
                }, delay);
            });
        });
    </script>
@endif
