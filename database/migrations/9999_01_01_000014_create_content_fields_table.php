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
            $table->integer('order_no')->unsigned()->default(1);
            $table->boolean('mandatory')->default(false);
            $table->boolean('readonly')->default(false);
            $table->string('hint', 255)->nullable();
            $table->json('configs')->nullable();

            $table->json('visibility_rules')->nullable();
            $table->json('mandatory_rules')->nullable();

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
