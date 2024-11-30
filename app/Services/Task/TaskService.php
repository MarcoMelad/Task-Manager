<?php

namespace App\Services\Task;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Collection;

class TaskService extends BaseService
{
    public function taskList(array $data): array
    {
        $validator = validator($data, [
            'status' => 'nullable|in:pending,completed,overdue',
            'due_date_from' => 'date',
            'due_date_to' => 'date|after_or_equal:due_date_from',
        ]);

        if ($validator->fails()) {
            return $this->failed(400, ['error' => $validator->errors()->first()]);
        }
        $tasks = Task::query();

        if (isset($data['status'])) {
            $tasks = $tasks->where('status', $data['status']);
        }
        if (isset($data['due_date_from']) && isset($data['due_date_to'])) {
            $tasks = $tasks->whereBetween('due_date', [
                $data['due_date_from'],
                $data['due_date_to']
            ]);
        }
        $tasks = $tasks->paginate($data['per_page'] ?? 10);
        return $this->success(200, ['tasks' => TaskResource::collection($tasks)]);
    }

    public function trashedTaskList(array $data): array
    {
        $validator = validator($data, [
            'status' => 'nullable|in:pending,completed,overdue',
            'due_date_from' => 'date',
            'due_date_to' => 'date|after_or_equal:due_date_from',
        ]);
        if ($validator->fails()) {
            return $this->failed(400, ['error' => $validator->errors()->first()]);
        }
        $tasks = Task::onlyTrashed();

        if (isset($data['status'])) {
            $tasks = $tasks->where('status', $data['status']);
        }
        if (isset($data['due_date_from']) && isset($data['due_date_to'])) {
            $tasks = $tasks->whereBetween('due_date', [
                $data['due_date_from'],
                $data['due_date_to']
            ]);
        }
        $tasks = $tasks->paginate($data['per_page'] ?? 10);
        return $this->success(200, ['tasks' => TaskResource::collection($tasks)]);
    }
    public function taskDetail($id): array
    {
        $task = Task::where('id', $id)->first();
        if (!$task) {
            return $this->failed(404, ['error' => 'task not found']);
        }

        return $this->success(200, ['task' => new TaskResource($task)]);
    }

    public function createTask(array $data): array
    {
        $task = new Task();
        $task->title = $data['title'];
        $task->description = $data['description'];
        $task->due_date = $data['due_date'];
        $task->status = 'pending';
        $task->save();

        return $this->success(200, ['task' => new TaskResource($task)]);
    }

    public function updateTask(array $data, $id): array
    {
        $task = Task::where('id', $id)->first();
        if (!$task) {
            return $this->failed(404, ['error' => 'task not found']);
        }

        $task->title = $data['title'] ?? $task->title;
        $task->description = $data['description'] ?? $task->description;
        $task->due_date = $data['due_date'] ?? $task->due_date;
        $task->save();

        return $this->success(200, ['task' => new TaskResource($task)]);
    }

    public function assignTask(array $data, $id): array
    {
        $validator = validator($data, [
            'emails' => 'required|array',
            'emails.*' => 'email|exists:users,email',
        ]);
        if ($validator->fails()) {
            return $this->failed(400, ['error' => $validator->errors()->first()]);
        }

        $task = Task::where('id', $id)->first();
        if (!$task) {
            return $this->failed(404, ['error' => 'task not found']);
        }
        $userIds = User::whereIn('email', $data['emails'])->pluck('id');
        $task->users()->syncWithoutDetaching($userIds);

        return $this->success(200, ['message' => 'Users assigned to task successfully.']);
    }

    public function userTasks($id): array
    {
        $user = User::where('id', $id)->first();
        if (!$user) {
            return $this->failed(404, ['error' => 'user not found']);
        }
        $tasks = $user->tasks;
        return $this->success(200, ['tasks' => TaskResource::collection($tasks)]);
    }
    public function deleteTask($id): array
    {
        $task = Task::where('id', $id)->first();
        if (!$task) {
            return $this->failed(404, ['error' => 'task not found']);
        }

        $task->delete();
        return $this->success(200, ['message' => 'task deleted successfully']);
    }

    public function retrieveTask($id): array
    {
        $task = Task::onlyTrashed()->where('id', $id)->first();
        if (!$task) {
            return $this->failed(404, ['error' => 'task not found']);
        }
        $task->restore();

        return $this->success(200, ['task' => new TaskResource($task)]);
    }

    public function forceDeleteTask($id): array
    {
        $task = Task::withTrashed()->where('id', $id)->first();
        if (!$task) {
            return $this->failed(404, ['error' => 'task not found']);
        }
        $task->forceDelete();
        return $this->success(200, ['message' => 'task deleted successfully']);
    }
}
