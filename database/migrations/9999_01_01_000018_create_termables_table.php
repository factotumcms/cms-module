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
        Schema::create('termables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('term_id')->constrained()->onDelete('cascade');
            $table->morphs('termable');

            $table->timestamps();

            $table->unique(['term_id', 'termable_id', 'termable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('termables');
    }
};
