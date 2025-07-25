<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected static string $view = 'filaments.views.article-detail';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make('Edit Post'),
            Actions\DeleteAction::make('Delete Post'),

        ];
    }
}
