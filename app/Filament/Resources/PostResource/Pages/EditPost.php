<?php

namespace App\Filament\Resources\PostResource\Pages;

use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Filament\Resources\PostResource;
use App\Models\Post;
use App\Models\Tag;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->readOnly()
                    ->required()
                    ->unique(Post::class, 'slug', fn($record) => $record),

                TinyEditor::make('content')
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory(fn (): string => 'user-' . auth('web')->user()->id . '/posts')
                    ->fileAttachmentsVisibility('public')
                    ->profile('default')
                    ->resize('both')
                    ->columnSpanFull()
                    ->required(),

                Repeater::make('references')
                    ->label('Daftar referensinya')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->label('Judul Referensi'),
                        TextInput::make('url')
                            ->nullable()
                            ->label('URL Referensi')
                            ->url(),
                    ])->columnSpanFull()
                    ->addActionLabel('Tambah Referensi')
                    ->collapsible()
                    ->collapsed(),

                FileUpload::make('thumbnail')
                    ->disk('public')
                    ->directory('user' . auth('web')->user()->id . '/posts/thumbnails')
                    ->nullable()
                    ->image(),

                TextInput::make('user_id')
                    ->hidden()
                    ->required(),

                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->required(),

                TagsInput::make('tags')
                    ->label('Tags')
                    ->suggestions(Tag::all()->pluck('name')->all())

            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [...$data, 'user_id' => auth()->user()->id];
    }
}
