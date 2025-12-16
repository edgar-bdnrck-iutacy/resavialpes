<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <script>
        (function () {
            try {
                const key = 'resavialpes-theme'; // même clé que admin.js
                const saved = localStorage.getItem(key); // "theme-dark" | "theme-light" | null
                const prefersDark =
                    window.matchMedia &&
                    window.matchMedia('(prefers-color-scheme: dark)').matches;

                const theme =
                    (saved === 'theme-light' || saved === 'theme-dark')
                        ? saved
                        : (prefersDark ? 'theme-dark' : 'theme-light');

                const html = document.documentElement;
                html.classList.remove('theme-dark', 'theme-light');
                html.classList.add(theme);

                // évite certains flashs sur inputs/scrollbars
                html.style.colorScheme = (theme === 'theme-dark') ? 'dark' : 'light';
            } catch (e) {}
        })();
    </script>

    <style>
        html.theme-dark,
        html.theme-dark body {
            background: #020617;
        }

        html.theme-light,
        html.theme-light body {
            background: #f3f4f6;
        }
    </style>

    <title>ResAvialpes - @yield('title', 'Accueil')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    {{-- Lucide icons CDN --}}
    <script defer src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body>
    <div class="layout">

        {{-- SIDEBAR --}}
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-logo">RA</div>
                <div class="brand-text">
                    <span>ResAvialpes</span>
                    <span>Planning & Réservations</span>
                </div>
            </div>

            <div>
                <p class="sidebar-section-title">Navigation</p>

                <ul class="sidebar-nav">
                    <li>
                        <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">
                            <i data-lucide="home" class="icon"></i>
                            <span>Accueil</span>
                        </a>
                    </li>

                    <li>
                        <a
                            href="{{ route('admin.aircraft.index') }}"
                            class="{{ request()->is('admin/aircraft') ? 'active' : '' }}"
                        >
                            <i data-lucide="plane" class="icon"></i>
                            <span>Aéronefs</span>
                        </a>
                    </li>

                    <li>
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="{{ request()->is('admin/users*') ? 'active' : '' }}"
                        >
                            <i data-lucide="users" class="icon"></i>
                            <span>Utilisateurs</span>
                        </a>
                    </li>

                    @if(\Illuminate\Support\Facades\Route::has('admin.planning.day'))
                    <li>
                      <a href="{{ route('admin.planning.day') }}"
                         class="{{ request()->is('admin/planning*') ? 'active' : '' }}">
                        <i data-lucide="calendar" class="icon"></i>
                        <span>Planning</span>
                      </a>
                    </li>
                    @endif

                    @if (\Illuminate\Support\Facades\Route::has('admin.status.global.edit'))
                        <li>
                            <a
                                href="{{ route('admin.status.global.edit') }}"
                                class="{{ request()->is('admin/status*') ? 'active' : '' }}"
                            >
                                <i data-lucide="message-square" class="icon"></i>
                                <span>Statut global</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="sidebar-footer">
                @auth
                    <form method="POST" action="{{ route('logout') }}" style="margin-bottom:10px;">
                        @csrf
                        <button class="btn btn-logout" type="submit">Déconnexion</button>
                    </form>
                @endauth

                <div>Prototype local ResAvialpes</div>
                <div>Avialpes · Annecy</div>
            </div>
        </aside>

        {{-- MAIN AREA --}}
        <div class="main">
            <header class="main-header">
                <div class="page-heading">
                    <button
                        class="sidebar-toggle"
                        type="button"
                        onclick="toggleSidebar()"
                    >
                        ☰ Menu
                    </button>

                    <h1 class="page-title">@yield('title', 'Accueil')</h1>

                    @hasSection('subtitle')
                        <p class="page-subtitle">@yield('subtitle')</p>
                    @endif
                </div>

                <div class="header-actions">
                    <button id="themeToggle" class="btn-theme" type="button">
                        <i id="themeIcon" data-lucide="moon" class="icon"></i>
                    </button>

                    @if (($alertsCount ?? 0) > 0)
                        <button
                            id="alertsToggle"
                            class="alerts-btn alerts-{{ $alertsMaxLevel ?? 'info' }}"
                            type="button"
                        >
                            <i data-lucide="bell" class="icon"></i>
                            <span class="alerts-count">{{ $alertsCount }}</span>
                        </button>
                    @endif

                    <div class="user-pill">
                        @auth
                            {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                        @endauth

                        @guest
                            Non connecté
                        @endguest
                    </div>
                </div>
            </header>

            <script>
                window.__ALERTS__ = @json($alerts ?? []);
            </script>

            <section class="main-content">
                <div class="main-inner">
                    @yield('content')
                </div>
            </section>
        </div>
    </div>

    <div class="alerts-panel" id="alertsPanel">
        <div class="alerts-panel-header">
            Alertes ({{ $alertsCount }})
        </div>

        @forelse ($alerts as $alert)
            <div
                class="alert alert-{{ $alert['level'] }}"
                style="cursor:pointer"
                onclick="window.location='{{ $alert['url'] ?? '/admin/aircraft' }}'"
            >
                <strong>{{ $alert['title'] }}</strong><br>
                {{ $alert['message'] }}
            </div>
        @empty
            <div class="alert alert-info">Aucune alerte</div>
        @endforelse
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
