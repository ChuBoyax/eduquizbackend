<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = Teacher::all();

        if ($teachers->isEmpty()) {
            $this->call(TeacherSeeder::class);
            $teachers = Teacher::all();
        }

        $quizzes = [
            ['title' => 'Algebra Basics', 'description' => 'Fundamentals of algebraic expressions and equations.'],
            ['title' => 'Philippine History', 'description' => 'Key events from pre-colonial to modern Philippines.'],
            ['title' => 'Cell Biology', 'description' => 'Structure and function of plant and animal cells.'],
            ['title' => 'English Grammar', 'description' => 'Parts of speech, tenses, and sentence structure.'],
            ['title' => 'World Geography', 'description' => 'Continents, countries, and physical features.'],
        ];

        foreach ($quizzes as $data) {
            Quiz::firstOrCreate(
                ['title' => $data['title']],
                [
                    'teacher_id' => $teachers->random()->id,
                    'description' => $data['description'],
                    'status' => fake()->randomElement(['draft', 'active', 'archived']),
                    'time_limit' => fake()->randomElement([10, 15, 20, 30]),
                    'shuffle_questions' => fake()->boolean(),
                    'shuffle_options' => fake()->boolean(),
                    'show_results_immediately' => true,
                ]
            );
        }
    }
}
