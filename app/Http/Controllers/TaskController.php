<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $tasks = QueryBuilder::for(
                Auth::user()->tasks()
            )
            ->allowedIncludes(['category'])
            ->allowedSorts(['created_at', 'priority', 'status'])
            ->allowedFilters([
                AllowedFilter::partial('description'),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('priority'),
                AllowedFilter::exact('category_id'),
                AllowedFilter::partial('category.name'),
            ]);
            return TaskResource::collection($tasks->get());
    }

    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $task = Task::create($data);
        $task->load('category');

        return response()->json([
            'message' => 'Task created successfully.',
            'data' => new TaskResource($task),
        ], 201);
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        $task->load('category');
        return response()->json([
            'data' => new TaskResource($task),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        $task->update($request->validated());
        $task->load('category');
        return response()->json([
            'message' => 'Task updated successfully.',
            'data' => new TaskResource($task),
        ]);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return response()->json([
            'message' => 'Task deleted successfully.',
        ], 200);
    }
}
