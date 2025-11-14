<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class TasksPerUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:tasks-per-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lists the number of tasks per user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Users per task");
        User::query()->withCount('tasks')->chunk(100, function ($users) {
            foreach ($users as $user) {
                $this->info("User " . $user->name . " has " . $user->tasks_count . " tasks");
            }
        });
    }
}
