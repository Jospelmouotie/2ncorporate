<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2Ncorporate | @yield('title', 'Boutique')</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        // Injection sécurisée de l'utilisateur
        window.user = {!! auth()->check()
            ? json_encode([
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'is_admin' => (bool) auth()->user()->is_admin,
            ])
            : 'null' !!};

        console.log('Window user injected:', window.user);
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50">
    <div id="app"></div>

    <!-- Script de test pour vérifier l'authentification -->
    <script>
        // Test d'authentification
        console.log('=== AUTH TEST ===');
        console.log('Authenticated:', {{ auth()->check() ? 'true' : 'false' }});
        console.log('User:', {!! json_encode(auth()->user()) !!});

        // Configuration axios
        if (typeof axios !== 'undefined') {
            axios.defaults.baseURL = '/api';
            axios.defaults.withCredentials = true;
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
            }
        }
    </script>
</body>
</html>
