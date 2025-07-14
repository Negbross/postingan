<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->readOnly()
                    ->required()
                    ->unique(Category::class, 'slug', ignoreRecord: true),

//                Placeholder::make('created_at')
//                    ->label('Created Date')
//                    ->content(fn(?Category $record): string => $record?->created_at?->diffForHumans() ?? '-'),
//
//                Placeholder::make('updated_at')
//                    ->label('Last Modified Date')
//                    ->content(fn(?Category $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
