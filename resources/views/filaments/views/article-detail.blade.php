<x-filament-panels::page>
    @livewire('articles.article-detail', ['slug' => $record->slug, 'isEmbedded' => true])
</x-filament-panels::page>
