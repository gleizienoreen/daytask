<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $work = Category::where('name', 'Work')->first();
        $school = Category::where('name', 'School')->first();
        $personal = Category::where('name', 'Personal')->first();

        if (! $user || ! $work || ! $school || ! $personal) {
            return;
        }

        $tasks = [
            [
                'user_id' => $user->id,
                'category_id' => $work->id,
                'title' => 'Finish project report',
                'description' => 'Complete the final draft of the quarterly report.',
                'status' => 'pending',
                'due_date' => now()->addDays(3)->toDateString(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $school->id,
                'title' => 'Submit research paper',
                'description' => 'Upload the bibliography and paper in PDF format.',
                'status' => 'done',
                'due_date' => now()->addDays(5)->toDateString(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $personal->id,
                'title' => 'Buy groceries',
                'description' => 'Get fresh fruits, vegetables, and milk for the week.',
                'status' => 'pending',
                'due_date' => now()->addDay()->toDateString(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $work->id,
                'title' => 'Team sync meeting',
                'description' => 'Review sprint backlog and discuss blockers.',
                'status' => 'done',
                'due_date' => now()->subDay()->toDateString(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $personal->id,
                'title' => 'Schedule dental appointment',
                'description' => 'Call the dental clinic to set up routine cleaning.',
                'status' => 'pending',
                'due_date' => now()->addWeek()->toDateString(),
            ],
        ];

        foreach ($tasks as $task) {
            Task::firstOrCreate(
                [
                    'user_id' => $task['user_id'],
                    'title' => $task['title'],
                ],
                $task
            );
        }
    }
}
