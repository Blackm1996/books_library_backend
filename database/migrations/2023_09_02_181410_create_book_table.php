<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book', function (Blueprint $table) {
            $table->id();
            $table->string("book_code")->unique();
            $table->string('book_name');
            $table->foreignId('author')->constrained('authors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('category')->constrained('category')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('nbPages')->default(0);
            $table->string('owner');
            $table->string('owner_phone');
            $table->boolean('state')->default("1");
            $table->text('description')->nullable();
            $table->string('coverUrl')->nullable();
            $table->boolean('active')->default("1");
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book');
    }
};
