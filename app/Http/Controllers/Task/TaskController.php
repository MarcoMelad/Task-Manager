<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Services\Task\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class TaskController extends Controller
{
    public function __construct(protected TaskService $taskService)
    {
    }
    public function index(Request $request): JsonResponse
    {
        $result = $this->taskService->taskList($request->all());
        return response()->json($result, $result['status_code']);
    }

    public function trashedTasks(Request $request): JsonResponse
    {
        $result = $this->taskService->trashedTaskList($request->all());
        return response()->json($result, $result['status_code']);
    }
    public function store(TaskStoreRequest $request): JsonResponse
    {
        $result = $this->taskService->createTask($request->validated());
        return response()->json($result, $result['status_code']);
    }
    public function show($id): JsonResponse
    {
        $result = $this->taskService->taskDetail($id);
        return response()->json($result, $result['status_code']);
    }
    public function update(TaskStoreRequest $request, $id): JsonResponse
    {
        $result = $this->taskService->updateTask($request->validated(), $id);
        return response()->json($result, $result['status_code']);
    }

    public function assignTask(Request $request, $id): JsonResponse
    {
        $result = $this->taskService->assignTask($request->all(), $id);
        return response()->json($result, $result['status_code']);
    }

    public function getUserTasks($id): JsonResponse
    {
        $result = $this->taskService->userTasks($id);
        return response()->json($result, $result['status_code']);
    }
    public function destroy(string $id): JsonResponse
    {
        $result = $this->taskService->deleteTask($id);
        return response()->json($result, $result['status_code']);
    }

    public function retrieveTasks($id): JsonResponse
    {
        $result = $this->taskService->retrieveTask($id);
        return response()->json($result, $result['status_code']);
    }

    public function forceDelete($id): JsonResponse
    {
        $result = $this->taskService->forceDeleteTask($id);
        return response()->json($result, $result['status_code']);
    }
}
