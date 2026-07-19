<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        collect(['Maria Santos', 'Juan Dela Cruz', 'Ana Reyes'])->each(function (string $name, int $i) {
            $user = User::firstOrCreate(
                ['email' => 'teacher' . ($i + 1) . '@eduquiz.test'],
                [
                    'name' => $name,
                    'password' => bcrypt('password'),
                    'role' => 'teacher',
                ]
            );

            Teacher::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'school' => fake()->randomElement(['Manila High School', 'Quezon City Science HS', 'Cebu National HS']),
                    'subject' => fake()->randomElement(['Math', 'Science', 'English', 'History']),
                    'employee_id' => 'EMP-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                ]
            );
        });
    }
}
