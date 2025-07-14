<div class="min-h-screen bg-gray-50 dark:bg-gray-700">
    @include('livewire.partials.navbar-header')
    {{-- Header Section --}}
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Tags</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Temukan artikel berdasarkan tag yang tersedia
                </p>
            </div>
        </div>
    </div>

    {{-- Search and Controls --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @include('livewire.header-search')
        @include('livewire.loading')
        {{-- Tags Display --}}
        <div wire:loading.remove class="bg-white rounded-lg shadow-sm">

            @if($tag)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse($tags as $post)
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
                </div>

            @else
                @if($tags->count() > 0)
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($tags as $tag)
                                <div class="bg-gradient-to-br from-white to-gray-50 rounded-lg p-6 border border-gray-200 hover:shadow-md transition-shadow duration-300">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            <a href="{{ $tag->getUrlAttribute() }}" class="hover:text-blue-600">
                                                {{ $tag->name }}
                                            </a>
                                        </h3>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                            {{ $tag->posts_count }}
                                        </span>
                                    </div>
                                    @if($tag->description)
                                        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($tag->description, 80) }}</p>
                                    @endif
                                    <a
                                        href="{{ $tag->getUrlAttribute() }}"
                                        class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium"
                                    >
                                        Lihat artikel
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Pagination --}}
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $tags->links() }}
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada tag ditemukan</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if($search)
                                Coba kata kunci yang berbeda atau hapus filter pencarian.
                            @else
                                Belum ada tag yang tersedia untuk saat ini.
                            @endif
                        </p>
                        @if($search)
                            <button
                                wire:click="$set('search', '')"
                                class="mt-4 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Hapus filter
                            </button>
                        @endif
                    </div>
                @endif
            @endif


        </div>
    </div>

</div>
