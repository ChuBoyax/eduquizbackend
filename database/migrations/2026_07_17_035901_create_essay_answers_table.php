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
        Schema::create('essay_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_participant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->longText('answer');
            $table->integer('points_earned')->default(0);
            $table->text('teacher_feedback')->nullable();
            $table->boolean('is_graded')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essay_answers');
    }
};
