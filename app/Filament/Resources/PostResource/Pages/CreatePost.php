<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Exception;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\Element\Image;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Writer\HTML;
use Str;

class CreatePost extends CreateRecord
{
    use WithFileUploads;

    protected static string $resource = PostResource::class;
    public ?Model $record;

    protected static bool $canCreateAnother = false;
    public ?TemporaryUploadedFile $docxFile = null;
    public array $newlyCreatedImagePaths = [];

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    public function form(Form $form): Form
    {
        $baseSchema = PostResource::form($form);

        return $baseSchema->schema(array_merge([
            $baseSchema->getComponents(),
            TextInput::make('slug')
                ->readOnly()
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
                $directory = 'tmp-docx/user-' . auth()->id() . '/post-content-images';
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
        $content = $data['content'];
        $newContent = $content;

        foreach ($this->newlyCreatedImagePaths as $tmpPath) {
            $permanentPath = str_replace('tmp-docx/', '', $tmpPath);

            if (Storage::disk('public')->exists($tmpPath)) {
                Storage::disk('public')->move($tmpPath, $permanentPath);

                // Buat path URL relatif untuk PENCARIAN
                // Hasilnya akan menjadi: /storage/tmp-docx-imports/user-123/file.jpg
                $relativeTempUrl = '/storage/' . $tmpPath;

                // Buat path URL relatif untuk PENGGANTIAN
                // Hasilnya akan menjadi: /storage/user-123/post-content-images/file.jpg
                $relativePermanentUrl = '/storage/' . $permanentPath;
                $newContent = str_replace($relativeTempUrl , $relativePermanentUrl , $newContent);
            }

        }
        $data['content'] = $newContent;
        return [...$data, 'user_id' => auth()->user()->id];
    }
}
