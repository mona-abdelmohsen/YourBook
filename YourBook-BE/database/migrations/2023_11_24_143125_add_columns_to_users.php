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
        Schema::table('users', function (Blueprint $table) {
            // if not exist, add the new column
            if (!Schema::hasColumn('users', 'country_id')) {
                $table->string('country_id')->nullable()->default(null)->after('remember_token');
            }

            if (!Schema::hasColumn('users', 'birth_date')) {
                $table->timestamp('birth_date')->nullable()->default(null)->after('privacy');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('country_id');
            $table->dropColumn('birth_date');
        });
    }
};
