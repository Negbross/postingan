<!DOCTYPE html>
<html lang="id" x-data="{
      darkMode: false,
      sidebarOpen: false
      }"
      :class="{'dark': darkMode === true }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {!! \Artesaos\SEOTools\Facades\SEOTools::generate(true) !!}
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .glass-effect {
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .search-glow:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .sidebar-animation {
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('style')
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">


    @livewireStyles

</head>
<body class="bg-gray-50 dark:bg-gray-700 antialiased min-h-screen">
{{ $slot }}
<footer class="text-center py-4 dark:text-white text-black">
    &copy; {{ date('Y') }} {{ config('app.name') }}
</footer>
@livewireScripts

</body>
</html>
