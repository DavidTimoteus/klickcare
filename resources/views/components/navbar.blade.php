<nav class="navbar navbar-expand-lg fixed-top">
    <a class="navbar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" width="70">
        KLIKCARE
    </a>
    <div class="container-fluid border-top justify-content-end mt-5">
        <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <i class="fas fa-align-justify"></i>
        </button>
        <nav class="navbar navbar-expand-lg fixed-top">
            <a class="navbar-brand">
                <img src="{{ asset('img/klickCare-logo.png') }}" alt="Logo" width="70">
                KLIKCARE
            </a>
            <div class="container-fluid border-top justify-content-end mt-5">
                <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fas fa-align-justify"></i>
                </button>
                <div class="navbar-collapse justify-content-end collapse" id="navbarSupportedContent">
                    <ul class="nav navbar-nav">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('poliumum') ? 'active' : '' }}" href="">INFORMASI &
                                    LAYANAN</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('TentangKami') ? 'active' : '' }}" href="">TENTANG
                                    KAMI</a>
                            </li>
                            @if (Auth::user()->user_access == 0 || Auth::user()->user_access == 1)
                                <li class="nav-item">
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                    </form>
                                    <a class="nav-link logout" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">LOGOUT</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('login') ? 'active' : '' }}"
                                    href="{{ url('/') }}">LOGIN</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</nav>
