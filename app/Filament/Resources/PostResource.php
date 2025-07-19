<?php

namespace App\Filament\Resources;

use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Models\Tag;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PostResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Post::class;

    protected static ?string $slug = 'posts';

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->maxLength(100)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->readOnly()
                    ->required()
                    ->unique(Post::class, 'slug', fn($record) => $record),

                TextInput::make('excerpt')
                    ->nullable(),

                TinyEditor::make('content')
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory(
                        fn (): string => 'user-' . auth('web')->user()->id . '/post-content-images'
                    )
                    ->fileAttachmentsVisibility('public')
                    ->resize('both')
                    ->disableGrammarly()
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
                    ->acceptedFileTypes(['jpg', 'jpeg', 'png', 'gif'])
                    ->disk('public')
                    ->directory('user-' . auth('web')->user()->id . '/post-content-images/thumbnail')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
            'view' => Pages\ViewPost::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'category']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug', 'user.name', 'category.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->user) {
            $details['User'] = $record->user->name;
        }

        if ($record->category) {
            $details['Category'] = $record->category->name;
        }

        return $details;
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            "view-any",
            "delete-any",
            "view",
            "create",
            "edit",
            "delete",
            "trash",
            "restore",
            "forceDelete",
            "publish",
        ];
    }
}
