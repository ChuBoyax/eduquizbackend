<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Room;
use App\Models\RoomParticipant;
use App\Models\Student;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $quizzes = Quiz::all();
        $students = Student::all();

        if ($quizzes->isEmpty()) {
            $this->call(QuizSeeder::class);
            $quizzes = Quiz::all();
        }

        if ($students->isEmpty()) {
            $this->call(StudentSeeder::class);
            $students = Student::all();
        }

        foreach ($quizzes->take(3) as $quiz) {
            if ($quiz->rooms()->exists()) {
                continue;
            }

            $status = fake()->randomElement(['waiting', 'ongoing', 'finished']);
            $startedAt = in_array($status, ['ongoing', 'finished']) ? fake()->dateTimeBetween('-1 week', 'now') : null;
            $endedAt = $status === 'finished' ? fake()->dateTimeBetween($startedAt, 'now') : null;

            $room = Room::create([
                'quiz_id' => $quiz->id,
                'teacher_id' => $quiz->teacher_id,
                'status' => $status,
                'started_at' => $startedAt,
                'ended_at' => $endedAt,
            ]);

            $participants = $students->random(min(8, $students->count()));

            foreach ($participants as $student) {
                $isFinished = $status === 'finished';

                RoomParticipant::create([
                    'room_id' => $room->id,
                    'student_id' => $student->id,
                    'score' => $isFinished ? fake()->numberBetween(0, 20) : 0,
                    'total_points' => $isFinished ? 20 : 0,
                    'is_finished' => $isFinished,
                    'joined_at' => $startedAt ?? now(),
                    'finished_at' => $isFinished ? $endedAt : null,
                ]);
            }
        }
    }
}
