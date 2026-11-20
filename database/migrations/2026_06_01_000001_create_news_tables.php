<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('parent_id')->nullable()->constrained('regions')->nullOnDelete();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->integer('sort_order')->default(0);
            $t->boolean('show_in_menu')->default(true);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->integer('sort_order')->default(0);
            $t->boolean('show_in_menu')->default(true);
            $t->boolean('show_on_home')->default(false);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('ads', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('image');
            $t->string('link_url')->nullable();
            $t->string('format')->default('banner');
            $t->boolean('is_active')->default(true);
            $t->timestamp('starts_at')->nullable();
            $t->timestamp('ends_at')->nullable();
            $t->unsignedBigInteger('clicks')->default(0);
            $t->timestamps();
        });

        Schema::create('posts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $t->string('title');
            $t->string('slug')->unique();
            $t->text('excerpt')->nullable();
            $t->longText('body')->nullable();
            $t->string('image')->nullable();
            $t->string('image_caption')->nullable();
            $t->string('author_name')->default('Bali Globalnews');
            $t->string('status')->default('draft');
            $t->boolean('is_headline')->default(false);
            $t->boolean('is_editor_choice')->default(false);
            $t->boolean('is_breaking')->default(false);
            $t->unsignedBigInteger('views')->default(0);
            $t->timestamp('published_at')->nullable();
            $t->timestamps();
            $t->index(['status', 'published_at']);
        });

        Schema::create('ad_post', function (Blueprint $t) {
            $t->id();
            $t->foreignId('post_id')->constrained()->cascadeOnDelete();
            $t->foreignId('ad_id')->constrained()->cascadeOnDelete();
            $t->string('placement');
        });

        Schema::create('landing_ads', function (Blueprint $t) {
            $t->id();
            $t->string('slot');
            $t->foreignId('ad_id')->constrained()->cascadeOnDelete();
            $t->integer('sort_order')->default(0);
        });

        Schema::create('pages', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug')->unique();
            $t->longText('body')->nullable();
            $t->boolean('show_in_footer')->default(true);
            $t->integer('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('settings', function (Blueprint $t) {
            $t->string('key')->primary();
            $t->text('value')->nullable();
        });
    }

    public function down(): void
    {
        foreach (['settings', 'pages', 'landing_ads', 'ad_post', 'posts', 'ads', 'categories', 'regions'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
