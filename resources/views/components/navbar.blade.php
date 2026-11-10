<nav class="navbar navbar-expand-lg kc-navbar sticky-top">
    <div class="container-fluid px-lg-4 px-3">
        <a class="kc-brand" href="{{ route('home') }}">
            <img src="{{ asset('img/klickCare-logo.png') }}" alt="KlickCare Logo">
            <div class="kc-brand-text">
                <span class="kc-brand-title">KlickCare</span>
                <span class="kc-brand-sub">Poliklinik Polinema</span>
            </div>
        </a>

        <button class="navbar-toggler border-0 shadow-none px-2" type="button" data-bs-toggle="collapse" data-bs-target="#kcNavbarContent" aria-controls="kcNavbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list fs-2 text-dark"></i>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="kcNavbarContent">
            <ul class="navbar-nav align-items-lg-center gap-lg-1 my-3 my-lg-0">
                <li class="nav-item">
                    <a class="kc-nav-link {{ request()->routeIs('home') ? 'active' : '' }}" id="navBeranda" href="{{ route('home') }}">
                        <i class="bi bi-house-door me-1"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="kc-nav-link {{ request()->routeIs('poli.*') ? 'active' : '' }}" id="navLayananPoli" href="{{ request()->routeIs('home') ? '#layanan-poli' : route('home') . '#layanan-poli' }}">
                        <i class="bi bi-heart-pulse me-1"></i> Layanan Poliklinik
                    </a>
                </li>
                <li class="nav-item">
                    <a class="kc-nav-link" id="navAlurPeriksa" href="{{ request()->routeIs('home') ? '#alur-periksa' : route('home') . '#alur-periksa' }}">
                        <i class="bi bi-diagram-3 me-1"></i> Alur Periksa
                    </a>
                </li>

                @auth
                    @if (Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="kc-nav-link {{ request()->routeIs('admin') ? 'active' : '' }}" href="{{ route('admin') }}">
                                <i class="bi bi-speedometer2 me-1"></i> Konsol Medis
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="kc-nav-link {{ request()->routeIs('admin.pharmacy*') ? 'active' : '' }}" href="{{ route('admin.pharmacy') }}">
                                <i class="bi bi-capsule me-1"></i> Stok Farmasi
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="kc-nav-link {{ request()->routeIs('user*') ? 'active' : '' }}" href="{{ route('user') }}">
                                <i class="bi bi-ticket-perforated me-1"></i> Antrian Saya
                            </a>
                        </li>
                    @endif

                    <li class="nav-item ms-lg-2 my-2 my-lg-0">
                        <div class="kc-user-pill">
                            <span class="kc-user-avatar">
                                <i class="bi bi-person-fill text-primary"></i>
                            </span>
                            <span class="kc-user-name" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</span>
                            <span class="badge {{ Auth::user()->isAdmin() ? 'bg-primary text-white' : 'bg-light text-secondary border' }}" style="font-size: 0.675rem;">
                                {{ Auth::user()->role_label }}
                            </span>
                        </div>
                    </li>

                    <li class="nav-item ms-lg-2 my-1 my-lg-0">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline m-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 px-3 py-1 fw-semibold w-100 w-lg-auto" title="Keluar">
                                <i class="bi bi-box-arrow-right me-1"></i> Keluar
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item ms-lg-3 my-1 my-lg-0">
                        <a class="btn btn-outline-primary btn-sm px-3 py-2 fw-semibold rounded-3 me-lg-2 d-block d-lg-inline-block text-center" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                        </a>
                    </li>
                    <li class="nav-item my-1 my-lg-0">
                        <a class="btn btn-primary btn-sm px-3 py-2 fw-semibold rounded-3 d-block d-lg-inline-block text-center" href="{{ route('register') }}">
                            <i class="bi bi-person-plus me-1"></i> Daftar
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
