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
        $baseSchema = PostResource::form($form);

        return $baseSchema->schema(array_merge([
            $baseSchema->getComponents(),
            TextInput::make('slug')
                ->required()
                ->unique(Post::class, 'slug', fn($record) => $record),
        ]));
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
