<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('status')
                    ->label(' ')
                    ->icon(function (Post $post) {
                        return match ($post->status) {
                            "published" => "heroicon-o-check-circle",
                            "draft" => "heroicon-o-clock",
                            "denied" => "heroicon-o-x-circle",
                        };
                    })
                    ->color(function (Post $post) {
                        return match ($post->status) {
                            "published" => "success",
                            "draft" => "warning",
                            "denied" => "danger",
                        };
                    })
                    ->tooltip(fn(Post $record): string => ucfirst($record->status)),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(35)
                    ->tooltip(fn($record) => $record->title),

                TextColumn::make('slug')
                    ->searchable()
                    ->visibleFrom('lg')
                    ->sortable(),

                ViewColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->view('filaments.tables.columns.thumbnail')
                    ->visibleFrom('lg'),

                TextColumn::make('published_at')
                    ->label('Published Date')
                    ->date(),

                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->visible(fn() => auth()->user()->can('view-any', Post::class)),

                TextColumn::make('category.name')
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('lg'),
            ])
            ->modifyQueryUsing(function (Builder $query){
                if (!auth()->user()->hasRole('super_admin')) {
                    return $query->where('user_id', auth()->user()->id);
                }
                return $query;
            })
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                \Filament\Tables\Actions\ViewAction::make()
                    ->color('success'),
                ActionGroup::make([
                    Action::make("Publish")
                        ->requiresConfirmation()
                        ->icon("heroicon-o-check-circle")
                        ->color('success')
                        ->action(function (Post $record) {
                            $record->update([
                                'status' => 'published',
                                'published_at' => now()
                            ]);
                        })->visible(fn(Post $record): bool => $record->status !== 'published'),
                    Action::make("Denied")
                        ->color("danger")
                        ->requiresConfirmation()
                        ->icon("heroicon-o-x-circle")
                        ->action(function (Post $record) {
                            $record->where('id', $record->id)
                                ->update(['status' => 'denied']);
                        })->visible(fn(Post $record): bool => $record->status !== 'denied')
                ])
                    ->visible(fn() => auth()->user()->hasRole('super_admin')),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
