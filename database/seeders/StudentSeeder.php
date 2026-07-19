<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $sections = ['Grade 10 - Diamond', 'Grade 10 - Emerald', 'Grade 11 - STEM A', 'Grade 11 - STEM B'];

        for ($i = 1; $i <= 30; $i++) {
            Student::firstOrCreate(
                ['student_id' => 'S-' . str_pad((string) $i, 5, '0', STR_PAD_LEFT)],
                [
                    'name' => fake()->name(),
                    'section' => fake()->randomElement($sections),
                ]
            );
        }
    }
}
