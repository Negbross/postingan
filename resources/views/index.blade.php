<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Reading Experience</title>
    @vite(['resources/css/app.css'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }

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
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

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
</head>
<body class="bg-gray-50 min-h-screen" x-data="blogApp()">
<!-- Reading Progress Bar -->
<div class="fixed top-0 left-0 right-0 z-50">
    <div class="reading-progress" :style="`width: ${scrollProgress}%`"></div>
</div>

<!-- Header dengan Search -->
<header class="sticky top-0 z-40 bg-white/80 backdrop-blur-lg border-b border-gray-200/50">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg gradient-bg flex items-center justify-center">
                    <i class="fas fa-book-open text-white text-lg"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-900">BlogReader</h1>
            </div>

            <!-- Search Bar (Desktop) -->
            <div class="hidden md:flex items-center flex-1 max-w-lg mx-8">
                <div class="relative w-full">
                    <input
                        type="text"
                        x-model="searchQuery"
                        @input="searchPosts"
                        placeholder="Cari artikel, kategori, atau tag..."
                        class="w-full px-4 py-3 pl-12 pr-4 bg-gray-100 rounded-full border-0 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all duration-200 search-glow"
                    >
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>

                    <!-- Search Results Dropdown -->
                    <div x-show="searchResults.length > 0 && searchQuery.length > 0"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute top-full mt-2 w-full bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-y-auto">
                        <template x-for="result in searchResults" :key="result.id">
                            <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                                 @click="selectPost(result)">
                                <h4 class="font-medium text-gray-900" x-text="result.title"></h4>
                                <p class="text-sm text-gray-600 mt-1" x-text="result.excerpt"></p>
                                <div class="flex items-center mt-2 space-x-2">
                                    <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded" x-text="result.category"></span>
                                    <span class="text-xs text-gray-500" x-text="result.date"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <div class="flex items-center space-x-3">
                <button @click="showMobileSearch = !showMobileSearch" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-search text-gray-600"></i>
                </button>
                <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-bars text-gray-600"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Search -->
        <div x-show="showMobileSearch"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden mt-4">
            <div class="relative">
                <input
                    type="text"
                    x-model="searchQuery"
                    @input="searchPosts"
                    placeholder="Cari artikel..."
                    class="w-full px-4 py-3 pl-12 pr-4 bg-gray-100 rounded-full border-0 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all duration-200"
                >
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<div class="container mx-auto px-4 py-8 flex gap-8">
    <!-- Sidebar -->
    <aside class="hidden lg:block w-80 flex-shrink-0">
        <div class="sticky top-24 space-y-6">
            <!-- Kategori -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift transition-all duration-300">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-folder-open text-indigo-500 mr-2"></i>
                    Kategori
                </h3>
                <div class="space-y-2">
                    <template x-for="category in categories" :key="category.name">
                        <button @click="filterByCategory(category.name)"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-indigo-50 transition-colors duration-200"
                                :class="selectedCategory === category.name ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700'">
                            <span x-text="category.name"></span>
                            <span class="text-sm bg-gray-100 text-gray-600 px-2 py-1 rounded-full" x-text="category.count"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Tags Popular -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift transition-all duration-300">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-tags text-indigo-500 mr-2"></i>
                    Tag Popular
                </h3>
                <div class="tag-cloud">
                    <template x-for="tag in popularTags" :key="tag">
                        <span @click="filterByTag(tag)" class="tag-item cursor-pointer" x-text="tag"></span>
                    </template>
                </div>
            </div>

            <!-- Artikel Terbaru -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift transition-all duration-300">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-clock text-indigo-500 mr-2"></i>
                    Artikel Terbaru
                </h3>
                <div class="space-y-3">
                    <template x-for="post in recentPosts" :key="post.id">
                        <div @click="selectPost(post)" class="cursor-pointer group">
                            <h4 class="font-medium text-gray-900 group-hover:text-indigo-600 transition-colors duration-200 text-sm leading-tight" x-text="post.title"></h4>
                            <p class="text-xs text-gray-500 mt-1" x-text="post.date"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </aside>

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"></div>

    <!-- Mobile Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 w-80 bg-white shadow-xl transform lg:hidden sidebar-animation"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           x-show="sidebarOpen"
           x-transition:enter="transition ease-in-out duration-300"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0">
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
                <!-- Kategori Mobile -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Kategori</h3>
                    <div class="space-y-2">
                        <template x-for="category in categories" :key="category.name">
                            <button @click="filterByCategory(category.name); sidebarOpen = false"
                                    class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-indigo-50 transition-colors duration-200"
                                    :class="selectedCategory === category.name ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700'">
                                <span x-text="category.name"></span>
                                <span class="text-sm bg-gray-100 text-gray-600 px-2 py-1 rounded-full" x-text="category.count"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Tags Mobile -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tag Popular</h3>
                    <div class="tag-cloud">
                        <template x-for="tag in popularTags" :key="tag">
                            <span @click="filterByTag(tag); sidebarOpen = false" class="tag-item cursor-pointer" x-text="tag"></span>
                        </template>
                    </div>
                </div>

                <!-- Artikel Terbaru Mobile -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Artikel Terbaru</h3>
                    <div class="space-y-3">
                        <template x-for="post in recentPosts" :key="post.id">
                            <div @click="selectPost(post); sidebarOpen = false" class="cursor-pointer group">
                                <h4 class="font-medium text-gray-900 group-hover:text-indigo-600 transition-colors duration-200 text-sm leading-tight" x-text="post.title"></h4>
                                <p class="text-xs text-gray-500 mt-1" x-text="post.date"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main ArticleDetail Content -->
    <main class="flex-1 min-w-0">
        <!-- ArticleDetail Header -->
        <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="h-64 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 relative">
                <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                <div class="absolute bottom-6 left-6 right-6 text-white">
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm backdrop-blur-sm" x-text="currentPost.category"></span>
                        <span class="text-sm opacity-90" x-text="currentPost.date"></span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold leading-tight" x-text="currentPost.title"></h1>
                    <p class="text-lg opacity-90 mt-2" x-text="currentPost.excerpt"></p>
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
                            <div>
                                <p class="font-medium text-gray-900" x-text="currentPost.author"></p>
                                <p class="text-sm text-gray-500">Content Writer</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span class="flex items-center">
                                <i class="fas fa-clock mr-1"></i>
                                <span x-text="currentPost.readTime"></span>
                            </span>
                        <span class="flex items-center">
                                <i class="fas fa-eye mr-1"></i>
                                <span x-text="currentPost.views"></span>
                            </span>
                    </div>
                </div>
            </div>

            <!-- ArticleDetail Content -->
            <div class="p-6 md:p-8">
                <div class="prose prose-lg max-w-none article-content" x-html="currentPost.content"></div>

                <!-- Tags -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h4 class="font-medium text-gray-900 mb-3">Tags:</h4>
                    <div class="tag-cloud">
                        <template x-for="tag in currentPost.tags" :key="tag">
                            <span @click="filterByTag(tag)" class="tag-item cursor-pointer" x-text="tag"></span>
                        </template>
                    </div>
                </div>
            </div>
        </article>

        <!-- Related Articles -->
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Artikel Terkait</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="post in relatedPosts" :key="post.id">
                    <div @click="selectPost(post)" class="cursor-pointer group bg-gray-50 rounded-lg overflow-hidden hover:shadow-md transition-all duration-300">
                        <div class="h-32 bg-gradient-to-r from-indigo-400 to-purple-400"></div>
                        <div class="p-4">
                            <h4 class="font-medium text-gray-900 group-hover:text-indigo-600 transition-colors duration-200 line-clamp-2" x-text="post.title"></h4>
                            <p class="text-sm text-gray-600 mt-2 line-clamp-2" x-text="post.excerpt"></p>
                            <div class="flex items-center justify-between mt-3">
                                <span class="text-xs text-indigo-600 font-medium" x-text="post.category"></span>
                                <span class="text-xs text-gray-500" x-text="post.date"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </main>

</div>

<script>
    function blogApp() {
        return {
            sidebarOpen: false,
            showMobileSearch: false,
            searchQuery: '',
            searchResults: [],
            selectedCategory: 'Semua',
            scrollProgress: 0,

            currentPost: {
                id: 1,
                title: 'Panduan Lengkap Membangun UI/UX yang Responsif dengan Tailwind CSS',
                excerpt: 'Pelajari cara membuat interface yang indah dan responsif menggunakan Tailwind CSS dengan teknik-teknik modern.',
                content: `
                        <h2>Mengapa Tailwind CSS?</h2>
                        <p>Tailwind CSS adalah framework CSS utility-first yang memungkinkan Anda membangun desain custom dengan cepat. Berbeda dengan framework CSS tradisional yang menyediakan komponen siap pakai, Tailwind memberikan building block berupa utility classes yang dapat dikombinasikan untuk membuat design yang unik.</p>

                        <h2>Keunggulan Tailwind CSS</h2>
                        <p>Dengan menggunakan Tailwind CSS, Anda dapat mengontrol setiap aspek desain tanpa harus menulis CSS custom. Framework ini juga sangat responsif dan mobile-first, sehingga website Anda akan terlihat sempurna di semua perangkat.</p>

                        <h2>Implementasi Responsif</h2>
                        <p>Salah satu kekuatan Tailwind adalah sistem responsive yang intuitif. Anda dapat dengan mudah mengatur tampilan yang berbeda untuk mobile, tablet, dan desktop menggunakan prefix seperti 'md:', 'lg:', dan 'xl:'.</p>

                        <h2>Best Practices</h2>
                        <p>Untuk mendapatkan hasil maksimal dari Tailwind CSS, penting untuk memahami konsep utility-first dan bagaimana mengkombinasikan classes dengan efektif. Selalu pertimbangkan performa dan maintainability kode Anda.</p>
                    `,
                author: 'Ahmad Rizki',
                date: '15 Maret 2024',
                category: 'Web Development',
                tags: ['Tailwind CSS', 'UI/UX', 'Web Design', 'Frontend'],
                readTime: '8 menit',
                views: '1.2k'
            },

            categories: [
                { name: 'Semua', count: 45 },
                { name: 'Web Development', count: 15 },
                { name: 'UI/UX Design', count: 12 },
                { name: 'Mobile Development', count: 8 },
                { name: 'Data Science', count: 10 }
            ],

            popularTags: [
                'React', 'Vue.js', 'Tailwind CSS', 'JavaScript', 'Python',
                'UI/UX', 'Machine Learning', 'API', 'Database', 'Mobile'
            ],

            recentPosts: [
                {
                    id: 2,
                    title: 'Mengenal Alpine.js untuk Interaktivitas Web',
                    date: '12 Maret 2024',
                    excerpt: 'Framework JavaScript ringan untuk membuat web interaktif.'
                },
                {
                    id: 3,
                    title: 'Tips Optimasi Performa Website',
                    date: '10 Maret 2024',
                    excerpt: 'Panduan lengkap untuk meningkatkan kecepatan website Anda.'
                },
                {
                    id: 4,
                    title: 'Desain Mobile-First yang Efektif',
                    date: '8 Maret 2024',
                    excerpt: 'Strategi desain yang mengutamakan pengalaman mobile.'
                }
            ],

            relatedPosts: [
                {
                    id: 5,
                    title: 'Komponen UI yang Reusable dengan Tailwind',
                    excerpt: 'Buat komponen yang dapat digunakan kembali dengan mudah.',
                    category: 'Web Development',
                    date: '5 Maret 2024'
                },
                {
                    id: 6,
                    title: 'Animation dan Transition dengan CSS',
                    excerpt: 'Menambahkan efek animasi yang menarik pada website.',
                    category: 'UI/UX Design',
                    date: '3 Maret 2024'
                },
                {
                    id: 7,
                    title: 'Grid System Modern dengan CSS Grid',
                    excerpt: 'Membangun layout yang fleksibel dengan CSS Grid.',
                    category: 'Web Development',
                    date: '1 Maret 2024'
                }
            ],

            allPosts: [
                {
                    id: 1,
                    title: 'Panduan Lengkap Membangun UI/UX yang Responsif dengan Tailwind CSS',
                    excerpt: 'Pelajari cara membuat interface yang indah dan responsif menggunakan Tailwind CSS dengan teknik-teknik modern.',
                    category: 'Web Development',
                    date: '15 Maret 2024',
                    tags: ['Tailwind CSS', 'UI/UX', 'Web Design', 'Frontend']
                },
                {
                    id: 2,
                    title: 'Mengenal Alpine.js untuk Interaktivitas Web',
                    excerpt: 'Framework JavaScript ringan untuk membuat web interaktif.',
                    category: 'Web Development',
                    date: '12 Maret 2024',
                    tags: ['Alpine.js', 'JavaScript', 'Frontend']
                },
                {
                    id: 3,
                    title: 'Tips Optimasi Performa Website',
                    excerpt: 'Panduan lengkap untuk meningkatkan kecepatan website Anda.',
                    category: 'Web Development',
                    date: '10 Maret 2024',
                    tags: ['Performance', 'Optimization', 'Web Development']
                },
                {
                    id: 4,
                    title: 'Desain Mobile-First yang Efektif',
                    excerpt: 'Strategi desain yang mengutamakan pengalaman mobile.',
                    category: 'UI/UX Design',
                    date: '8 Maret 2024',
                    tags: ['Mobile Design', 'UI/UX', 'Responsive']
                }
            ],

            init() {
                this.updateScrollProgress();
                window.addEventListener('scroll', () => this.updateScrollProgress());
            },

            updateScrollProgress() {
                const scrollTop = window.pageYOffset;
                const docHeight = document.body.offsetHeight - window.innerHeight;
                this.scrollProgress = (scrollTop / docHeight) * 100;
            },

            searchPosts() {
                if (this.searchQuery.length < 2) {
                    this.searchResults = [];
                    return;
                }

                const query = this.searchQuery.toLowerCase();
                this.searchResults = this.allPosts.filter(post =>
                    post.title.toLowerCase().includes(query) ||
                    post.excerpt.toLowerCase().includes(query) ||
                    post.category.toLowerCase().includes(query) ||
                    post.tags.some(tag => tag.toLowerCase().includes(query))
                ).slice(0, 5);
            },

            selectPost(post) {
                this.currentPost = post;
                this.searchQuery = '';
                this.searchResults = [];
                this.showMobileSearch = false;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            filterByCategory(category) {
                this.selectedCategory = category;
                // Implementasi filter berdasarkan kategori
                console.log('Filter by category:', category);
            },

            filterByTag(tag) {
                this.searchQuery = tag;
                this.searchPosts();
                console.log('Filter by tag:', tag);
            }
        }
    }
</script>
</body>
</html>
