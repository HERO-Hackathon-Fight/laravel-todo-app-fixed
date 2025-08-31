<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Todo App') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-theme="basic">
    
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light shadow-sm" role="navigation" aria-label="メインナビゲーション">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}" tabindex="1">
                    <i class="fas fa-tasks me-2" aria-hidden="true"></i>
                    {{ config('app.name', 'Todo App') }}
                </a>
                
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto" role="menubar">
                    <!-- Theme Selector -->
                    <li class="nav-item me-3" role="none">
                        <label for="theme-select" class="form-label mb-0 me-2" id="theme-label">
                            <i class="fas fa-palette me-1" aria-hidden="true"></i>テーマ:
                        </label>
                        <select id="theme-select" 
                                class="form-select form-select-sm" 
                                style="width: auto;"
                                tabindex="2"
                                aria-labelledby="theme-label"
                                aria-describedby="theme-help"
                                role="combobox"
                                aria-expanded="false">
                            <option value="theme-basic">ベーシック</option>
                            <option value="theme-blue">ブルー</option>
                            <option value="theme-green">グリーン</option>
                        </select>
                        <div id="theme-help" class="visually-hidden">
                            矢印キーで選択、Enterで決定
                        </div>
                    </li>
                    
                    <!-- Font Selector -->
                    <li class="nav-item me-3" role="none">
                        <label for="font-select" class="form-label mb-0 me-2" id="font-label">
                            <i class="fas fa-font me-1" aria-hidden="true"></i>フォント:
                        </label>
                        <select id="font-select" 
                                class="form-select form-select-sm" 
                                style="width: auto;"
                                tabindex="3"
                                aria-labelledby="font-label"
                                aria-describedby="font-help"
                                role="combobox"
                                aria-expanded="false">
                            <option value="font-standard">標準（16px）</option>
                            <option value="font-child">幼児向け（18px・丸文字）</option>
                            <option value="font-young">ヤング（16px・モダン）</option>
                            <option value="font-senior">シニア（20px・明朝）</option>
                        </select>
                        <div id="font-help" class="visually-hidden">
                            矢印キーで選択、Enterで決定
                        </div>
                    </li>
                    
                    <!-- Authentication Links -->
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item" role="none">
                                <a class="nav-link" href="{{ route('login') }}" tabindex="4" role="menuitem">
                                    <i class="fas fa-sign-in-alt me-1" aria-hidden="true"></i>{{ __('Login') }}
                                </a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item" role="none">
                                <a class="nav-link" href="{{ route('register') }}" tabindex="5" role="menuitem">
                                    <i class="fas fa-user-plus me-1" aria-hidden="true"></i>{{ __('Register') }}
                                </a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown" role="none">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" tabindex="6">
                                <i class="fas fa-user me-1" aria-hidden="true"></i>{{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown" role="menu">
                                <a class="dropdown-item" href="{{ route('home') }}" role="menuitem">
                                    <i class="fas fa-home me-2" aria-hidden="true"></i>{{ __('Home') }}
                                </a>
                                
                                <a class="dropdown-item" href="{{ route('todo.index') }}" role="menuitem">
                                    <i class="fas fa-tasks me-2" aria-hidden="true"></i>Todo リスト
                                </a>
                                
                                <div class="dropdown-divider"></div>
                                
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();" role="menuitem">
                                    <i class="fas fa-sign-out-alt me-2" aria-hidden="true"></i>{{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </nav>

        <main class="py-4" id="main-content" role="main" tabindex="-1">
            @yield('content')
        </main>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
