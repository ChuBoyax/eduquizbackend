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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->enum('type', [
                'multiple_choice',
                'true_false',
                'fill_in_the_blank',
                'essay'
            ])->default('multiple_choice');
            $table->integer('points')->default(1);
            $table->integer('order')->default(0);
            $table->string('image')->nullable();
            $table->text('explanation')->nullable()
                ->comment('Explanation shown after answering');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
