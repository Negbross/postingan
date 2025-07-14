<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
            ->required()
            ->maxLength(50),
            TextInput::make('slug')
            ->unique(Category::class, 'slug', ignoreRecord: true)
            ->required()
        ]);
    }
}
