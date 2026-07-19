<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $quizzes = Quiz::all();

        if ($quizzes->isEmpty()) {
            $this->call(QuizSeeder::class);
            $quizzes = Quiz::all();
        }

        $types = ['multiple_choice', 'true_false', 'fill_in_the_blank', 'essay'];

        foreach ($quizzes as $quiz) {
            if ($quiz->questions()->exists()) {
                continue;
            }

            foreach (range(1, 6) as $order) {
                $type = $types[($order - 1) % count($types)];

                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'question' => fake()->sentence(10) . '?',
                    'type' => $type,
                    'points' => fake()->randomElement([1, 2, 5]),
                    'order' => $order,
                    'explanation' => fake()->optional()->sentence(),
                ]);

                match ($type) {
                    'multiple_choice' => $this->makeMultipleChoiceOptions($question),
                    'true_false' => $this->makeTrueFalseOptions($question),
                    default => null,
                };
            }
        }
    }

    private function makeMultipleChoiceOptions(Question $question): void
    {
        $correctIndex = fake()->numberBetween(0, 3);

        foreach (range(0, 3) as $i) {
            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => fake()->words(3, true),
                'is_correct' => $i === $correctIndex,
                'order' => $i,
            ]);
        }
    }

    private function makeTrueFalseOptions(Question $question): void
    {
        $correct = fake()->boolean();

        QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'True',
            'is_correct' => $correct,
            'order' => 0,
        ]);

        QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'False',
            'is_correct' => ! $correct,
            'order' => 1,
        ]);
    }
}
