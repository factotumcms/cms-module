<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('content_type_taxonomy', function (Blueprint $table) {
            $table->id();

            $table->foreignId('content_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('taxonomy_id')->constrained()->onDelete('cascade');
            $table->boolean('is_required')->default(false);
            $table->boolean('allow_multiple')->default(true);

            $table->timestamps();

            $table->unique(['content_type_id', 'taxonomy_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_type_taxonomy');
    }
};
