<?php

namespace App\Livewire;

use App\Models\Tag;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TagsPage extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public $search = '';
    public $sortBy;
    public $sortDirection = 'asc';
    public array $sortOptions = [];
    public ?Tag $tag = null;
    public string $pageTitle;

    public function mount($slug = null): void
    {
        if ($slug) {
            $this->tag = Tag::where('slug', $slug)->first();
            $this->pageTitle = $this->tag->name;
            SEOTools::setTitle($this->tag->name);
            SEOTools::setDescription($this->tag->name);
            SEOTools::opengraph()->setUrl(url()->current());
            $this->sortOptions = [
                'title' => 'Judul',
                'created_at' => 'Tanggal Dibuat', // Contoh menambahkan opsi baru
            ];
            $this->sortBy = 'created_at';
        } else {
            SEOTools::setTitle('Tags ' . config('app.name'));
            SEOTools::setDescription('Tag dari blog ini.');
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
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render(): View
    {
        $tags = null;
        if ($this->tag) {
            $tags = $this->tag->posts()
                ->published()
                ->latest()
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate(12);
        } else {
            $tags = Tag::query()
                ->withCount(['posts' => function ($query) {
                    $query->published();
                }])
                ->when($this->search, function ($query) {
                    $query->where('name', 'ilike', '%' . $this->search . '%');
                })
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate(24);
        }
        $totals = "Menampilkan " . $tags->firstItem() . " - " . $tags->lastItem() . " dari " . $tags->total() . " Tags";
        return view(
            'livewire.tags-index',
            compact('tags', 'totals')
        );
    }
}
