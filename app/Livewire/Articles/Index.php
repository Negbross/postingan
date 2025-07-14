<?php

namespace App\Livewire\Articles;

use App\Models\Category;
use App\Models\Post;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $totals;

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc']
    ];

    public array $sortOptions = [];

    public function mount(): void
    {
        SEOTools::setTitle('Blog - ' . config('app.name'));
        SEOTools::setDescription('Jelajahi artikel-artikel terbaru kami tentang teknologi, pengembangan web, dan topik menarik lainnya.');
        SEOTools::opengraph()->setUrl(url('/'));
        $this->totals = Post::published()->count() . ' Artikel ditemukan';
        $this->sortOptions = [
            'title' => 'Judul',
            'created_at' => 'Tanggal Dibuat', // Contoh menambahkan opsi baru
        ];
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['search', 'category'])) {
            $this->resetPage();
        }
    }

    public function sortby(string $field): void
    {
        if (!array_key_exists($field, $this->sortOptions)) {
            return;
        }
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render(): View
    {
        $posts = Post::query()
            ->where('status', "published")
            ->when($this->search, fn(Builder $query, $search) => $query->search($search))
            ->when($this->category, fn(Builder $query, $category) => $query->category($category))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(9);

        $categories = Category::pluck('name');
        return view('livewire.articles.index')->with([
            'posts' => $posts,
            'categories' => $categories
        ]);
    }
}
