<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskDueNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class NotifyDueTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:notify-due';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users about tasks due within the next 24 hours';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tasks = Task::where('due_date', '>=', Carbon::now())
            ->where('due_date', '<=', Carbon::now()->addDay())
            ->with('users')
            ->get();

        foreach ($tasks as $task) {
            // Notify all assigned users
            Notification::send($task->users, new TaskDueNotification($task));
        }

        $this->info('Notifications sent for tasks due within the next 24 hours.');
    }
}
