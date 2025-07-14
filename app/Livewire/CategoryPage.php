<?php

namespace App\Livewire;

use App\Models\Category;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class CategoryPage extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public $search = '';
    public $sortBy;
    public $pageTitle;
    public $sortDirection = 'asc';
    public array $sortOptions = [];

    public ?Category $category = null;

    public function mount($slug = null): void
    {
        if ($slug) {
            $this->category = Category::where('slug', $slug)->first();
            $this->pageTitle = $this->category->name;
            SEOTools::setTitle($this->category->name);
            SEOTools::setDescription($this->category->description ?? 'Kumpulan artikel dalam kategori ' . $this->category->name);
            SEOTools::opengraph()->setUrl(url()->current());
            $this->sortOptions = [
                'title' => 'Judul',
                'created_at' => 'Tanggal Dibuat', // Contoh menambahkan opsi baru
            ];
            $this->sortBy = 'created_at';
        }
        else {
            $this->pageTitle = 'Kategori';
            SEOTools::setTitle('Categories ' . config('app.name'));
            SEOTools::setDescription('Category dari blog ini.');
            SEOTools::opengraph()->setUrl(url()->current());
            $this->sortOptions = [
                'name' => 'Nama',
                'created_at' => 'Tanggal Dibuat', // Contoh menambahkan opsi baru
            ];
            $this->sortBy = 'name';
        }

    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['search', 'sortBy', 'sortDirection'])) {
            $this->resetPage();
        }
    }

    public function sortby($field): void
    {
        if (!array_key_exists($field, $this->sortOptions)) {
            return;
        }
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            $this->resetPage();
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render(): View
    {
        $categories = null;

        if ($this->category) {
            $categories = $this->category->posts()
                ->published()
                ->latest()
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate(12);
        } else {
            $categories = Category::query()
                ->withCount(['posts' => function ($query) {
                    $query->published();
                }])
                ->when($this->search, function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                })
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate(12);
        }

        $totals = "Menampilkan " . $categories->firstItem() . " - " . $categories->lastItem() . " dari " . $categories->total() . " kategori";

        return view(
            'livewire.categories.category-page',
            compact('categories', 'totals')
        );
    }
}
