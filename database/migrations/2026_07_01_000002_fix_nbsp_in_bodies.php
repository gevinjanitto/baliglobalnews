<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['posts', 'pages'] as $table) {
            DB::table($table)->select('id', 'body')->orderBy('id')->chunkById(200, function ($rows) use ($table) {
                foreach ($rows as $r) {
                    $clean = clean_html((string) $r->body);
                    if ($clean !== (string) $r->body) {
                        DB::table($table)->where('id', $r->id)->update(['body' => $clean]);
                    }
                }
            });
        }
    }

    public function down(): void {}
};
