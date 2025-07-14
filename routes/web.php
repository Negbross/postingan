<?php

use App\Livewire\Articles\ArticleDetail;
use App\Livewire\Articles\Index;
use App\Livewire\CategoryPage;
use App\Livewire\TagsPage;
use Illuminate\Support\Facades\Route;

Route::get('/', Index::class)->name('blog');
Route::get('/blog/{slug}', ArticleDetail::class)->name('blog.detail');

Route::get('/categories', CategoryPage::class)->name('categories');
Route::get('/categories/{slug}', CategoryPage::class)->name('categories.show');

Route::get('/tags', TagsPage::class)->name('tags');
Route::get('/tags/{slug}', TagsPage::class)->name('tags.show');
