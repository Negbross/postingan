<div x-data="{
        scrollProgress: 0,
        updateScrollProgress() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            this.scrollProgress = (scrollTop / docHeight) * 100;
        }
    }" @scroll.window="updateScrollProgress()">
    @push('style')
        <style>

            .reading-progress {
                height: 3px;
                background: linear-gradient(to right, #667eea, #764ba2);
                transition: width 0.1s ease;
            }

            .article-content {
                line-height: 1.8;
            }

            .article-content h2 {
                font-size: 1.5rem;
                font-weight: 600;
                margin: 2rem 0 1rem 0;
                color: #1f2937;
            }

            .article-content p {
                margin-bottom: 1.5rem;
                color: #374151;
            }

            .tag-cloud {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .tag-item {
                background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.875rem;
                color: #6b7280;
                transition: all 0.2s ease;
            }

            .tag-item:hover {
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
                transform: scale(1.05);
            }
        </style>
    @endpush
    <!-- Reading Progress Bar -->
    <div class="fixed top-0 left-0 right-0 z-50">
        <div class="reading-progress" :style="`width: ${scrollProgress}%`"></div>
    </div>

    @if(!$isEmbedded)
            @include('livewire.partials.navbar-header')
            @include('livewire.partials.sidebar-menu')
    @endif

    <!-- Main ArticleDetail Content -->
    <main class="flex-1 min-w-0 bg-gray-50 dark:bg-gray-700">
        <!-- ArticleDetail Header -->
        <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="h-64 bg-cover bg-center relative"
                 style="background-image: url({{ $post->getFeaturedImageUrlAttribute() }})">
                <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                <div class="absolute bottom-6 left-6 right-6 text-white">
                    <div class="flex items-center space-x-2 mb-3">
                        <span
                            class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm backdrop-blur-sm">{{ $post->category->name }}</span>
                        <span class="text-sm opacity-90">{{ $post->created_at->format('d M Y') }}</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold leading-tight">{{ $post->title }}</h1>
                    <p class="text-lg opacity-90 mt-2">{!! $post->getExcerptAttribute($post->excerpt) !!}</p>
                </div>
            </div>

            <!-- ArticleDetail Meta -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <i class="fas fa-user text-indigo-600"></i>
                            </div>
{{--                            <div>--}}
{{--                                <p class="font-medium text-gray-900">{{ $post->user->name }}</p>--}}
{{--                                <p class="text-sm text-gray-500">{{ $post->user->getRoleClass() }}</p> --}}{{-- need role --}}
{{--                            </div>--}}
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span class="flex items-center">
                                <i class="fas fa-clock mr-1"></i>
                                <span>{{ $post->getReadTimeAttribute($post->read_time) }} min</span>
                            </span>
{{--                        <span class="flex items-center">--}}
{{--                                <i class="fas fa-eye mr-1"></i>--}}
{{--                                <span x-text="currentPost.views"></span> --}}{{-- Engangement system --}}
{{--                            </span>--}}
                    </div>
                </div>
            </div>

            <!-- ArticleDetail Content -->
            <div class="p-6 md:p-8">
                <div class="prose prose-lg max-w-none article-content">{!! str($post->content)->sanitizeHtml() !!}</div>

                <!-- referensi link -->
                @if(!empty($post->references))
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Referensi</h3>
                        <ul class="list-disc list-inside space-y-2">
                            @foreach($post->references as $reference)
                                <li>
                                    @if(array_key_exists('link', $reference))
                                        <a href="{{ $reference['link'] }}" target="_blank" rel="noopener noreferrer"
                                           class="text-blue-600 hover:underline">
                                            {{ $reference['title'] }}
                                        </a>
                                    @endif
                                    <p class="italic text-black dark:text-white">{{ $reference['title'] }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Tags -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h4 class="font-medium text-gray-900 mb-3">Tags:</h4>
                    <div class="tag-cloud">
                        @foreach($post->tags as $tag)
                            <span class="tag-item">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </article>

        @if(!$isEmbedded)
            <!-- Related Articles -->
            @if($relatedPosts->isNotEmpty())
                <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6">Artikel Terkait</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($relatedPosts as $relatedPost)
                            <a href="{{ route('blog.detail', $relatedPost->slug) }}" wire:navigate
                               class="cursor-pointer group bg-gray-50 rounded-lg overflow-hidden hover:shadow-md transition-all duration-300">
                                <div class="h-32 bg-gradient-to-r from-indigo-400 to-purple-400"></div>
                                <div class="p-4">
                                    <h4 class="font-medium text-gray-900 group-hover:text-indigo-600 transition-colors duration-200 line-clamp-2">{{ $relatedPost->title }}</h4>
                                    <p class="text-sm text-gray-600 mt-2 line-clamp-2">{!! $relatedPost->excerpt !!}</p>
                                    <div class="flex items-center justify-between mt-3">
                                    <span
                                        class="text-xs text-indigo-600 font-medium">{{ $relatedPost->category->name }}</span>
                                        <span
                                            class="text-xs text-gray-500">{{ $relatedPost->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

    </main>
</div>
