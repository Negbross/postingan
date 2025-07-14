{{-- Navbar Section --}}
<div class="mx-auto px-4 py-4">
    <div class="flex items-center justify-center h-16 relative">

        <div class="absolute inset-y-0 left-0 flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg gradient-bg flex items-center justify-center">
                <i class="fas fa-book-open text-white text-lg"></i>
            </div>
            <h1 class="text-xl font-bold dark:text-white text-gray-900">{{ config('app.name') }}</h1>
        </div>

        <nav class="hidden md:flex space-x-8">
            <a href="{{ route('blog') }}" wire:navigate
               class="{{ request()->is('blog*') || request()->is('/') ? 'text-blue-700' : 'text-gray-500' }} hover:text-gray-900 font-medium transition-colors duration-200">Home</a>
            <a href="{{ route('tags') }}" wire:navigate
               class="{{ request()->is('tag*') ? 'text-blue-700' : 'text-gray-500' }} hover:text-gray-900 font-medium transition-colors duration-200">Tags</a>
            <a href="{{ route('categories') }}" wire:navigate
               class="{{ request()->is('categories*') ? 'text-blue-700' : 'text-gray-500' }} hover:text-gray-900 font-medium transition-colors duration-200">Category</a>
            <a href="#" class="text-gray-500 hover:text-gray-900 font-medium transition-colors duration-200">About me</a>
        </nav>

        <div class="absolute inset-y-0 right-0 flex items-center">
            <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                <i class="fas fa-bars text-gray-600"></i>
            </button>
            <button
                type="button"
                @click="darkMode = !darkMode"
                class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                {{-- Ikon Bulan (saat mode terang) --}}
                <svg x-show="!darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
                {{-- Ikon Matahari (saat mode gelap) --}}
                <svg x-show="darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m-6.364 1.636l-.707.707M21.213 3.787l-.707.707M3.787 21.213l.707-.707M20.5 12h-1M4.5 12H3m16.5 6.364l-.707-.707M6.364 3.787l.707.707M12 16a4 4 0 110-8 4 4 0 010 8z" />
                </svg>
            </button>
        </div>

    </div>

</div>

