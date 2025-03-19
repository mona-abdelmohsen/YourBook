<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('deleted_at'); // Remove deleted_at column
            $table->boolean('enable')->default(true); // Add enable column with default true
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable(); // Re-add deleted_at if rollback
            $table->dropColumn('enable'); // Remove enable column if rollback
        });
    }
};