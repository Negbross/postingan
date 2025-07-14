<?php

namespace App\Livewire\Articles;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;

class ArticleDetail extends Component
{
    public Post $post;
    public Collection $relatedPosts;
    public bool $isEmbedded = false;

    public function mount($slug, $isEmbedded = false): void
    {
        $this->isEmbedded = $isEmbedded;
        $this->post = Post::with(['category', 'tags', 'user'])->where('slug', $slug)->firstOrFail();
        SEOTools::setTitle($this->post->title);
        SEOTools::setDescription($this->post->excerpt);
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'article');
        if ($this->post->thumbnail) SEOTools::opengraph()->addImage($this->post->thumbnail);

        $this->relatedPosts = Post::where('category_id', $this->post->category_id)
            ->where('id', '!=', $this->post->id)
            ->limit(3)
            ->get();
    }

    public function render(): View
    {
        $categories = Category::withCount('posts')->get();
        $popularTags = Tag::withCount('posts')
            ->orderBy('posts_count', 'desc')->limit(10)->get();
        $recentPosts = Post::whereNotNull('published_at')
        ->where('id', '!=', $this->post->id)
        ->latest('published_at')
        ->limit(3)
        ->get();
        $data = [
            'categories' => $categories,
            'popularTags' => $popularTags,
            'recentPosts' => $recentPosts,
            'post' => $this->post,
            'relatedPosts' => $this->relatedPosts,
        ];
        $view = view('livewire.articles.article-detail');
        if ($this->isEmbedded) return $view->layout('components.layouts.blank')
            ->with($data);
        return $view->layout('components.layouts.app')
            ->with($data);
    }
}
