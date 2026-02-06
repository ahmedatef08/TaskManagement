<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TaskController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $tasks = QueryBuilder::for(
                Task::query()->where('user_id', $request->user()->id)
            )
            ->allowedIncludes(['category'])
            ->allowedSorts(['created_at', 'due_date', 'priority', 'status'])
            ->allowedFilters([
                AllowedFilter::partial('description'),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('priority'),
                AllowedFilter::exact('category_id'),
                AllowedFilter::partial('category.name'),
            ]);

        return response()->json($tasks->get());
    }

    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $task = Task::create($data);
        $task->load('category');

        return response()->json([
            'message' => 'Task created successfully.',
            'data' => $task,
        ], 201);
    }

    public function show(Task $task)
    {
        $this->authorize('modify', $task);
        $task->load('category');
        return response()->json([
            'data' => $task,
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('modify', $task);
        $task->update($request->validated());
        $task->load('category');
        return response()->json([
            'message' => 'Task updated successfully.',
            'data' => $task,
        ]);
    }

    public function destroy(Task $task)
    {
        $this->authorize('modify', $task);
        $task->delete();
        return response()->json([
            'message' => 'Task deleted successfully.',
        ], 200);
    }
}
