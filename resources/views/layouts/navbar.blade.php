<style>
    .navbar-custom {
        background: linear-gradient(135deg, #FFB300 0%, #FF8C00 100%);
        box-shadow: 0 8px 24px rgba(255, 140, 0, 0.3);
        padding: 1rem 0;
        /* position: relative; */
        z-index: 1000;
    }

    .navbar-custom .navbar-brand {
        color: white !important;
        font-weight: 700;
        font-size: 1.25rem;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .navbar-custom .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 500;
        padding: 0.5rem 1rem !important;
        margin: 0 0.25rem;
        border-radius: 15px;
        transition: all 0.3s ease;
    }

    .navbar-custom .nav-link:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white !important;
        /* transform: translateY(-2px); */
    }

    .navbar-custom .nav-link.active {
        background: rgba(255, 255, 255, 0.25);
        color: white !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .navbar-custom .dropdown-menu {
        border-radius: 16px;
        border: 1px solid rgba(255, 214, 112, 0.2);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        padding: 0.5rem;
    }

    .navbar-custom .dropdown-item {
        border-radius: 12px;
        padding: 0.625rem 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .navbar-custom .dropdown-item:hover {
        background: linear-gradient(135deg, #FFE8A3 0%, #FFD670 100%);
        color: #FF8C00;
        transform: translateX(2px);
    }

    .navbar-custom .dropdown-toggle::after {
        margin-left: 0.5rem;
    }

    .navbar-toggler {
        border: 2px solid rgba(255, 255, 255, 0.5);
        border-radius: 12px;
    }

    .navbar-toggler:focus {
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.2);
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }
</style>

<nav class="navbar navbar-expand-lg navbar-custom mb-4 sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="bi bi-bank2 me-2"></i>
            Pembayaran SPP TK DHARMA
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                {{-- <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                        <i class="bi bi-house-door me-1"></i>
                        Beranda
                    </a>
                </li> --}}
                @auth
                    @if (Auth::user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('murid.*') ? 'active' : '' }}"
                               href="{{ route('murid.index') }}">
                                <i class="bi bi-people me-1"></i>
                                Data Murid
                            </a>
                        </li>
                    @endif

                    @if (strtolower(Auth::user()->role ?? '') === 'wali')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pembayaran.*') ? 'active' : '' }}"
                               href="{{ route('pembayaran.index') }}">
                                <i class="bi bi-wallet2 me-1"></i>
                                Pembayaran
                            </a>
                        </li>
                    @endif

                    @if (Auth::user()->role === 'bendahara')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pemasukan.*') ? 'active' : '' }}"
                               href="{{ route('pemasukan.index') }}">
                                <i class="bi bi-cash-stack me-1"></i>
                                Pemasukan
                            </a>
                        </li>
                    @endif

                    @if (strtolower(Auth::user()->role ?? '') === 'wali')
                        @php($muridId = \App\Models\Murid::where('user_id', Auth::id())->value('id'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="waliMenu" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>
                                {{ Auth::user()->name ?? 'Akun' }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="waliMenu">
                                <li>
                                    <a class="dropdown-item" href="{{ $muridId ? route('profile.edit', ['id' => $muridId]) : '#' }}">
                                        <i class="bi bi-person-gear"></i>
                                        Profile
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="px-2">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <a href="#" class="nav-link" onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="bi bi-box-arrow-right me-0"></i>
                                    Logout
                                </a>
                            </form>
                        </li>
                    @endif
                @endauth
            </ul>
        </div>
    </div>
</nav>
