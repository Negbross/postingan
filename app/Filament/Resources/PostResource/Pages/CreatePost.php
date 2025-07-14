<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\CreateRecord;
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

    protected static bool $canCreateAnother = false;
    public ?TemporaryUploadedFile $docxFile = null;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    private function extractImagesRecursively(AbstractElement $element, array &$imageReplacements): void
    {
        // Cek jika elemen ini adalah gambar
        if ($element instanceof Image) {
            $base64Data = $element->getImageStringData(true);
            $imageData = base64_decode($base64Data);
            if ($imageData) {
                $imageName = 'docx-import-' . Str::random(10) . '.' . $element->getImageExtension();
                $directory = 'user-' . auth()->id() . '/post-content-images/docx-import';
                $storagePath = $directory . '/' . $imageName;
//            dd($imageData);
                Storage::disk('public')->put($storagePath, $imageData);

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
        $this->validate([
            'docxFile' => 'required|mimes:docx|max:5120'
        ]);

        try {
            $path = $this->docxFile->getRealPath();
            $phpWord = IOFactory::load($path);
            $imageReplacements = [];
            $sections = $phpWord->getSections();
            foreach ($sections as $section) {
                $this->extractImagesRecursively($section, $imageReplacements);
            }
//            dd($imageReplacements);
            $htmlWriter = new HTML($phpWord);
            $htmlContent = $htmlWriter->getContent();

            foreach ($imageReplacements as $oldSrc => $newUrl) {
                $htmlContent = str_replace('src="data:image/png;base64,' . $oldSrc . '"', 'src="' . $newUrl . '"', $htmlContent);
            }
//            dd($htmlContent);
            $cleanHtml = clean($htmlContent);
//            dd($cleanHtml);
            $this->data['content'] = $cleanHtml;
        } catch (\Exception $exception) {
            logger()->error($exception->getMessage());
            $this->addError('docxFile', 'Gagal memproses file. Pastikan file tidak rusak.');
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [...$data, 'user_id' => auth()->user()->id];
    }
}
