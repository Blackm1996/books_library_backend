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
        Schema::create('lending', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book')->constrained('book')->onDelete('cascade')->onUpdate('cascade');
            $table->string('loaner');
            $table->string('loaner_phone');
            $table->date('date_lending');
            $table->boolean('returned')->default("0");
            $table->date('date_return')->nullable();
            $table->dateTime('date_returned')->nullable();
            $table->foreignId('lended_by')->nullable()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('returned_to')->nullable()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lending');
    }
};
