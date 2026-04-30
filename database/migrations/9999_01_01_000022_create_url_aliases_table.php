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
        Schema::create('url_aliases', function (Blueprint $table) {
            $table->id();

            $table->string('uri', 2048);
            $table->morphs('routable');
            $table->string('locale', 10)->nullable();

            $table->boolean('is_canonical')->default(true);
            $table->string('redirect_to', 2048)->nullable();

            $table->timestamps();

            $table->unique(['uri', 'locale']);
            $table->index(['routable_type', 'routable_id', 'locale']);
            $table->index('is_canonical');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_aliases');
    }
};
