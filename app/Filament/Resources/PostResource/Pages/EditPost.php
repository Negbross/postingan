<?php

namespace App\Filament\Resources\PostResource\Pages;

use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Filament\Resources\PostResource;
use App\Models\Post;
use App\Models\Tag;
use Exception;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\Element\Image;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Writer\HTML;

class EditPost extends EditRecord
{
    use WithFileUploads;

    protected static string $resource = PostResource::class;
    protected static bool $canCreateAnother = false;
    public ?TemporaryUploadedFile $docxFile = null;
    public array $newlyCreatedImagePaths = [];

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

    private function extractImagesRecursively(AbstractElement $element, array &$imageReplacements): void
    {
        // Cek jika elemen ini adalah gambar
        if ($element instanceof Image) {
            $base64Data = $element->getImageStringData(true);
            $imageData = base64_decode($base64Data);
            if ($imageData) {
                $imageName = 'docx-import-' . Str::random(10) . '.' . $element->getImageExtension();
                $directory = 'tmp-docx/user-' . auth()->id();
                $storagePath = $directory . '/' . $imageName;
                Storage::disk('public')->put($storagePath, $imageData);

                $this->newlyCreatedImagePaths[] = $storagePath;
                // Simpan path lama dan URL baru untuk diganti nanti
                $imageReplacements[$element->getImageStringData(true)] = Storage::disk('public')->url($storagePath);
            }

        }

        // Jika elemen ini punya "anak" (bisa berisi elemen lain), periksa juga semua anaknya
        if (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $childElement) {
                $this->extractImagesRecursively($childElement, $imageReplacements);
            }
        }
    }

    public function updatedDocxFile(): void
    {
        $this->validateOnly('docxFile');

        try {
            $path = $this->docxFile->getRealPath();
            $phpWord = IOFactory::load($path);
            $imageReplacements = [];
            $sections = $phpWord->getSections();
            foreach ($sections as $section) {
                $this->extractImagesRecursively($section, $imageReplacements);
            }

            $htmlWriter = new HTML($phpWord);
            $htmlContent = $htmlWriter->getContent();

            foreach ($imageReplacements as $oldSrc => $newUrl) {
                $htmlContent = str_replace('src="data:image/png;base64,' . $oldSrc . '"', 'src="' . $newUrl . '"', $htmlContent);
            }

            $cleanHtml = clean($htmlContent);
            $this->data['content'] = $cleanHtml;
        } catch (Exception $exception) {
            logger()->error($exception->getMessage());
            $this->addError('docxFile', 'Gagal memproses file. Pastikan file tidak rusak.');
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [...$data, 'user_id' => auth()->user()->id];
    }
}
