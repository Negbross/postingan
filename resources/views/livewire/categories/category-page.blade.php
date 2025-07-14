<div class="min-h-screen bg-gray-50">
    @include('livewire.partials.navbar-header')
    {{-- Header Section --}}
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $pageTitle }}</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Jelajahi artikel berdasarkan kategori yang tersedia
                </p>
            </div>
        </div>
    </div>

    {{-- Search and Controls --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @include('livewire.header-search')

        @include('livewire.loading')

        {{-- Categories Grid --}}
        <div wire:loading.remove class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            @if($category)
                @forelse($categories as $post)
                    <article
                        class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                        {{-- Featured Image --}}
                        @if($post->thumbnail)
                            <div class="aspect-video bg-gray-200 overflow-hidden">
                                <img
                                    src="{{ $post->thumbnail }}"
                                    alt="{{ $post->title }}"
                                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                >
                            </div>
                        @else
                            <div
                                class="aspect-video bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif

                        <div class="p-6">
                            {{-- Category Badge --}}
                            @if($post->category)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mb-3">
                                {{ ucfirst($post->category->name) }}
                            </span>
                            @endif

                            {{-- Title --}}
                            <h2 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">
                                <a href="{{ $post->getUrlAttribute() }}" class="hover:text-blue-600 transition-colors">
                                    {{ $post->title }}
                                </a>
                            </h2>

                            {{-- Excerpt --}}
                            <p class="text-gray-600 mb-4 line-clamp-3">
                                {!! $post->getExcerptAttribute($post->excerpt) !!}
                            </p>

                            {{-- Meta Information --}}
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <div class="flex items-center space-x-2">
                                    @if($post->user)
                                        <span class="underline"><a href="#" class="">{{ $post->user->name }}</a></span>
                                        <span>•</span>
                                    @endif
                                    <time datetime="{{ $post->created_at->toISOString() }}">
                                        {{ $post->created_at->diffForHumans() }}
                                    </time>
                                </div>

                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>{{ $post->read_time }} min</span>
                                </div>
                            </div>

                            {{-- Read More Button --}}
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <a
                                    href="{{ $post->getUrlAttribute() }}"
                                    class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors"
                                >
                                    Baca Selengkapnya
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada artikel ditemukan</h3>
                        <p class="text-gray-500">Coba ubah filter pencarian atau kategori untuk menemukan artikel yang Anda
                            cari.</p>
                    </div>
                @endforelse
            @else
                @forelse($categories as $category)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    @if($category->icon)
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-[#10B98120]">
                                            <i class="{{ $category->icon }} text-lg" style="color: {{ $category->color }}"></i>
                                        </div>
                                    @else
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: {{ $category->color }}20">
                                            <svg class="w-5 h-5" style="color: #10B981" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $category->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $category->posts_count }} artikel</p>
                                    </div>
                                </div>
                            </div>

                            @if($category->description)
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $category->description }}</p>
                            @endif

                            <a
                                href="{{ $category->getUrlAttribute() }}"
                                class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors"
                            >
                                Lihat Artikel
                                <svg class="w-4 h-4 ml-1 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada kategori ditemukan</h3>
                        <p class="text-gray-500">Coba ubah pencarian untuk menemukan kategori yang Anda cari.</p>
                    </div>
                @endforelse
            @endif

        </div>

        {{-- Pagination --}}
        @if($categories->hasPages())
            <div class="mt-8">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
