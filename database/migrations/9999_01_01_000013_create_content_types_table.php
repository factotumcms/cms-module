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
        Schema::create('content_types', function (Blueprint $table) {
            $table->id();
            $table->string('label', 64)->nullable()->default(null);
            $table->string('type', 32)->unique();
            $table->boolean('editable')->default(true);
            $table->integer('order_no')->unsigned()->nullable();
            $table->string('icon', 64)->nullable();
            $table->boolean('sitemap')->default(true);
            $table->boolean('visible')->default(true)->nullable();
            $table->boolean('hierarchical')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_types');
    }
};
