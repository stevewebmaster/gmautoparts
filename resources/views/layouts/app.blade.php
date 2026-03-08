<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'G&M Autospares') - Quality Used Car Parts | NZ</title>
    <meta name="description" content="@yield('meta_description', 'G&M Autospares - quality used automotive parts in New Zealand. Browse our parts catalogue and find what you need.')">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="d-flex flex-column min-vh-100 travely-body @if(request()->routeIs('home')) page-home @endif">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-dark" href="{{ route('home') }}">G&M Autospares</a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active fw-semibold' : '' }} text-dark" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('parts.*') ? 'active fw-semibold' : '' }} text-dark" href="{{ route('parts.index') }}">Parts</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('vehicles.*') ? 'active fw-semibold' : '' }} text-dark" href="{{ route('vehicles.index') }}">Now Dismantling</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active fw-semibold' : '' }} text-dark" href="{{ route('about') }}">About</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active fw-semibold' : '' }} text-dark" href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 @yield('main_class', 'py-4')" @if(request()->routeIs('home')) style="padding-top: 0 !important;" @endif>
        @if(session('success'))
            <div class="container"><div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div>
        @endif
        @if(session('error'))
            <div class="container"><div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div>
        @endif
        @yield('content')
    </main>

    <footer class="bg-dark text-white py-4 mt-auto">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    <p class="mb-0 small opacity-75">&copy; {{ date('Y') }} G&M Autospares. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a class="text-white text-decoration-none opacity-75 me-3" href="{{ route('about') }}">About</a>
                    <a class="text-white text-decoration-none opacity-75" href="{{ route('contact') }}">Contact</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @livewireScripts
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
