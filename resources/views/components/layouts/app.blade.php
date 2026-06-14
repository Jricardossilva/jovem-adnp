<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Jovem ADNP') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-slate-50 font-sans text-slate-800 antialiased">
    <div class="mx-auto flex min-h-full w-full max-w-md flex-col px-4 py-6">
        <header class="mb-6 text-center">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-600 text-2xl text-white shadow-sm">
                ⚽
            </div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">{{ config('app.name', 'Jovem ADNP') }}</h1>
            <p class="text-sm text-slate-500">Lista de participantes</p>
        </header>

        <main class="flex-1">
            {{ $slot }}
        </main>

        <footer class="mt-8 text-center text-xs text-slate-400">
            Feito com fé e companheirismo • {{ date('Y') }}
        </footer>
    </div>
    @livewireScripts
</body>
</html>
