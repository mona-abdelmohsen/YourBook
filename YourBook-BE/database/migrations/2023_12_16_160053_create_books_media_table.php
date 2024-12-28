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
        Schema::create('books_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBiginteger('book_id')->unsigned();
            $table->unsignedBiginteger('media_id')->unsigned();

            $table->foreign('book_id')->references('id')
                ->on('books')->onDelete('cascade');
            $table->foreign('media_id')->references('id')
                ->on('media')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books_media');
    }
};
