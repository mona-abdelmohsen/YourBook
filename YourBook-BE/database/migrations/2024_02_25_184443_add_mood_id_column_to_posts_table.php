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
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'mood_id')) {
                $table->foreignId('mood_id')->nullable()->after('content')->constrained()->cascadeOnDelete();
            }

            if (!Schema::hasColumn('posts', 'location')) {
                $table->string('location')->nullable()->after('mood_id')->constrained()->cascadeOnDelete();
            }

            if (!Schema::hasColumn('posts', 'share_link')) {
                $table->mediumText('share_link')->nullable()->after('location')->constrained()->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('mood_id');
            $table->dropColumn('location');
            $table->dropColumn('share_link');
        });
    }
};
