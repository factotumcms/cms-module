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
        Schema::create('content_fields', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('content_type_id')->unsigned();
            $table->foreign('content_type_id')->references('id')->on('content_types')->onDelete('cascade');
            $table->string('name', 50);
            $table->string('label', 50);
            $table->string('type', 50);
            $table->integer('order_no')->unsigned()->nullable();
            $table->boolean('mandatory')->nullable();
            $table->boolean('readonly')->nullable();
            $table->string('hint', 255)->nullable();
            $table->text('options')->nullable();
            $table->integer('max_file_size')->unsigned()->nullable();
            $table->integer('min_width_size')->unsigned()->nullable();
            $table->integer('min_height_size')->unsigned()->nullable();
            $table->string('image_operation', 16)->nullable();
            $table->boolean('image_bw')->nullable();
            $table->string('allowed_types', 64)->nullable();
            $table->text('resizes')->nullable();
            $table->bigInteger('linked_content_type_id')->unsigned()->nullable()->default(null);
            $table->foreign('linked_content_type_id')->references('id')->on('content_types')->onDelete('cascade');
            $table->json('visibility_rules')->nullable(false)->default(DB::raw("('[]')"));
            $table->json('mandatory_rules')->nullable(false)->default(DB::raw("('[]')"));

            $table->unique(['content_type_id', 'name']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_fields');
    }
};
