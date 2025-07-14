<div
    x-show="sidebarOpen"
    @click="sidebarOpen = false"
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-40 bg-black bg-opacity-50 md:hidden"
></div>

<aside
    x-show="sidebarOpen"
    x-transition:enter="transition ease-in-out duration-300"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in-out duration-300"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-xl md:hidden"
>
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Menu</h2>
            <button @click="sidebarOpen = false" class="p-2 rounded-lg hover:bg-gray-100">
                <i class="fas fa-times text-gray-600"></i>
            </button>
        </div>
    </div>

    <div class="p-6 space-y-6 overflow-y-auto">
        <!-- Konten sidebar yang sama seperti desktop -->
        <div class="space-y-6">
            <button class="w-full flex items-center justify-between px-1.5 py-1 rounded-lg hover:bg-indigo-50 transition-colors duration-200">
                <a href="{{ route('blog') }}" wire:navigate
                   class="{{ request()->is('blog*') || request()->is('/') ? 'text-blue-700' : 'text-gray-500' }} hover:text-gray-900 font-medium transition-colors duration-200">Home</a>
            </button>
            <button class="w-full flex items-center justify-between px-1.5 py-1 rounded-lg hover:bg-indigo-50 transition-colors duration-200">
                <a href="{{ route('tags') }}" wire:navigate
                   class="{{ request()->is('tag*') ? 'text-blue-700' : 'text-gray-500' }} hover:text-gray-900 font-medium transition-colors duration-200">Tags</a>
            </button>
            <button class="w-full flex items-center justify-between px-1.5 py-1 rounded-lg hover:bg-indigo-50 transition-colors duration-200">
                <a href="{{ route('categories') }}" wire:navigate
                   class="{{ request()->is('categories*') ? 'text-blue-700' : 'text-gray-500' }} hover:text-gray-900 font-medium transition-colors duration-200">Category</a>
            </button>

        </div>
    </div>

</aside>
