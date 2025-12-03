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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('content_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');


            $table->string('name', 50);
            $table->string('label', 255);
            $table->string('abs_url', 255 )->nullable();
            $table->text('description')->nullable();
            $table->string('lang', 5);
            $table->integer('order_no')->nullable();
            $table->boolean('sitemap')->default(true);

            $table->string('seo_title', 70)->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_canonical_url', 255)->nullable();
            $table->string('seo_robots_indexing', 10)->default('index')->nullable();
            $table->string('seo_robots_following', 10)->default('follow')->nullable();
            $table->string('seo_focus_key', 255)->nullable();

            $table->string('fb_title', 255)->nullable();
            $table->string('fb_description', 255)->nullable();
            $table->bigInteger('fb_image')->unsigned()->nullable();
            $table->foreign('fb_image')->references('id')->on('media');

            $table->timestamps();
        });

        Schema::create('category_content', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('content_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
