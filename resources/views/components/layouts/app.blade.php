<!DOCTYPE html>
<html lang="id" x-data="{
      darkMode: false}"
      :class="{'dark': darkMode === true }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {!! \Artesaos\SEOTools\Facades\SEOTools::generate(true) !!}

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
