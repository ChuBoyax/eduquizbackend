<?php

namespace Database\Seeders;

use App\Models\EssayAnswer;
use App\Models\Room;
use Illuminate\Database\Seeder;

class EssayAnswerSeeder extends Seeder
{
    public function run(): void
    {
        if (Room::query()->doesntExist()) {
            $this->call(RoomSeeder::class);
        }

        $rooms = Room::with(['participants', 'quiz.questions'])->get();

        foreach ($rooms as $room) {
            $essayQuestions = $room->quiz->questions->where('type', 'essay');

            if ($essayQuestions->isEmpty()) {
                continue;
            }

            foreach ($room->participants as $participant) {
                foreach ($essayQuestions as $question) {
                    $isGraded = $participant->is_finished && fake()->boolean(70);

                    EssayAnswer::firstOrCreate(
                        [
                            'room_participant_id' => $participant->id,
                            'question_id' => $question->id,
                        ],
                        [
                            'answer' => fake()->paragraph(3),
                            'points_earned' => $isGraded ? fake()->numberBetween(0, $question->points) : 0,
                            'teacher_feedback' => $isGraded ? fake()->sentence() : null,
                            'is_graded' => $isGraded,
                        ]
                    );
                }
            }
        }
    }
}
