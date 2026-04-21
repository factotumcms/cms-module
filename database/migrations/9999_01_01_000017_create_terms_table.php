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
        Schema::create('terms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('taxonomy_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('terms')->onDelete('cascade');

            $table->string('name', 100);
            $table->string('slug', 255);
            $table->text('description')->nullable();
            $table->string('lang', 10);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['taxonomy_id', 'slug', 'lang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
};
