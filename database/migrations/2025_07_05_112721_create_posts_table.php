<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique()->index();
            $table->longText('content');
            $table->json('references')->nullable();
            $table->string('thumbnail')->nullable();
            $table->text('excerpt')->nullable();
            $table->unsignedBigInteger('read_time')->nullable();
            $table->enum('status', ['draft', 'published', 'denied'])->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->foreignUuid('user_id')->index()->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('category_id')->index()->constrained('categories')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
