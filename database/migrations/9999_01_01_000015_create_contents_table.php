<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Wave8\Factotum\Cms\Enums\ContentEditorType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('content_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('contents')->onDelete('cascade');

            $table->string('status', 25);
            $table->string('title', 255);
            $table->string('editor_type', 32)->default(ContentEditorType::BUILDER);
            $table->longText('content');
            $table->string('url', 191);
            $table->string('abs_url', 191)->unique();
            $table->string('lang', 5);
            $table->boolean('show_in_menu')->default(false);
            $table->boolean('is_home')->default(false);
            $table->integer('order_no')->default(0);

            $table->string('seo_title', 70)->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_canonical_url', 255)->nullable();
            $table->string('seo_robots_indexing', 10)->default('index')->nullable();
            $table->string('seo_robots_following', 10)->default('follow')->nullable();
            $table->string('seo_focus_key', 255)->nullable();

            $table->string('fb_title', 255)->nullable();
            $table->string('fb_description', 255)->nullable();
            $table->integer('fb_image')->nullable();

            $table->timestamps();
            $table->softDeletes();
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
