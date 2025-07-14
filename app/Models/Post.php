<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Str;

class Post extends Model
{
    use SoftDeletes, HasUuids, HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'thumbnail',
        'published_at',
        'user_id',
        'category_id',
        'status',
        'references'
    ];

    /**
     * Filter search title, inside of content, and author
     **/
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'ilike', '%' . $search . '%')
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'ilike', '%' . $search . '%')
                        ->orWhere('username', 'ilike', '%' . $search . '%');
                });
        });
    }

    /**
     * Filter is publish or not
    **/
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'like', '%published%')
            ->where('published_at', '<=', now());
    }

    /**
     * Filter by category
    **/
    public function scopeCategory(Builder $query, $category): Builder
    {
        return $query->whereHas('category', function (Builder $query) use ($category) {
            $query->where('name', $category);
        });
    }

    /**
     * Check or generate excerpt
    **/
    public function getExcerptAttribute($value)
    {
        if ($value) return $value;

        return Str::limit(strip_tags($this->content), 80);
    }

    /**
     * Get read time attribute
    **/
    public function getReadTimeAttribute($value)
    {
        if ($value) return $value;

        // Calculate reading time based on content (average 200 words per minute)
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / 200));
    }

    /**
     * Get the post's full URL.
     */
    public function getUrlAttribute(): string
    {
        return route('blog.detail', $this->slug);
    }

    /**
     * Get the post's featured image URL or return a placeholder.
     */
    public function getFeaturedImageUrlAttribute(): string
    {
//        if ($this->thumbnail && Storage::disk('public')->exists($this->thumbnail))
//            return Storage::publicUrl($this->thumbnail);
        $title = $this->title;

        // Ambil dua huruf pertama dari judul sebagai inisial
        $words = explode(' ', $title);
        $initials = strtoupper(
            ($words[0][0] ?? '') . ($words[1][0] ?? '')
        );

        // Buat warna latar belakang unik berdasarkan hash dari judul
        $bgColor = '#' . substr(md5($title), 0, 6);
        $textColor = '#FFFFFF';

        // Template SVG
        $svg = <<<SVG
    <svg width="800" height="600" xmlns="http://www.w3.org/2000/svg">
      <rect width="100%" height="100%" fill="{$bgColor}" />
      <text x="50%" y="50%" font-family="Inter, sans-serif" font-size="120" font-weight="bold" fill="{$textColor}" text-anchor="middle" dy=".3em">{$initials}</text>
    </svg>
    SVG;

        // Kembalikan sebagai Data URI yang siap dipakai di <img>
        return $this->thumbnail
            ? storage_path($this->thumbnail)
            : 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id');
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'timestamp',
            'references' => 'array'
        ];
    }
}
