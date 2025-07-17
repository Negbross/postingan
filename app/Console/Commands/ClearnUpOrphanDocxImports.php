<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearnUpOrphanDocxImports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clearn-up-orphan-docx-imports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes temporary docx import files older than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info("Deleting temporary docx import files");
        $directory = "tmp-docx";
        $files = Storage::disk('public')->allFiles($directory);
        foreach ($files as $file) {
            if (Storage::disk('public')->lastModified($file) < now()->subHours(24)->getTimestamp()) {
                Storage::disk('public')->delete($file);
            };
        }
        $this->info("Deleting temporary docx import files done");
    }
}
