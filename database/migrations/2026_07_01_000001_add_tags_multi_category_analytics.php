<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_post', function (Blueprint $t) {
            $t->id();
            $t->foreignId('post_id')->constrained()->cascadeOnDelete();
            $t->foreignId('category_id')->constrained()->cascadeOnDelete();
            $t->unique(['post_id', 'category_id']);
        });

        Schema::create('tags', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->timestamps();
        });

        Schema::create('post_tag', function (Blueprint $t) {
            $t->id();
            $t->foreignId('post_id')->constrained()->cascadeOnDelete();
            $t->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $t->unique(['post_id', 'tag_id']);
        });

        Schema::table('posts', function (Blueprint $t) {
            $t->unsignedBigInteger('views_manual')->nullable()->after('views');
        });

        Schema::create('visits', function (Blueprint $t) {
            $t->id();
            $t->string('visitor_id', 40)->index();
            $t->string('ip', 45)->index();
            $t->string('country')->nullable();
            $t->string('country_code', 4)->nullable();
            $t->string('region')->nullable();
            $t->string('city')->nullable();
            $t->boolean('geo_done')->default(false)->index();
            $t->string('device', 20);
            $t->string('browser', 40);
            $t->string('os', 40);
            $t->string('path', 500);
            $t->foreignId('post_id')->nullable()->constrained()->nullOnDelete();
            $t->string('referrer', 255)->nullable();
            $t->timestamp('created_at')->index();
        });

        $rows = DB::table('posts')->whereNotNull('category_id')->get(['id', 'category_id']);
        foreach ($rows as $r) {
            DB::table('category_post')->insert(['post_id' => $r->id, 'category_id' => $r->category_id]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
        Schema::table('posts', fn (Blueprint $t) => $t->dropColumn('views_manual'));
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('category_post');
    }
};
