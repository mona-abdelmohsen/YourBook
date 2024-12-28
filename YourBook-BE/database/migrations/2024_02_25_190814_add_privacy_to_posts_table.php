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
            if (!Schema::hasColumn('posts', 'privacy')) {
                $table->string('privacy')->default('public')->after('share_link')->comment('public/private/friends');
            }
            if (!Schema::hasColumn('posts', 'show_in_feed')) {
                $table->tinyInteger('show_in_feed')->default(1)->after('privacy');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('privacy');
            $table->dropColumn('show_in_feed');
        });
    }
};
