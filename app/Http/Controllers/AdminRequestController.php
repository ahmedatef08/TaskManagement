<?php

namespace App\Http\Controllers;

use App\Models\AdminRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class AdminRequestController extends Controller
{
    public function requestDeleteTask(Request $request)
    {
        $data = $request->validate([
            'task_id' => ['required', 'integer', 'exists:tasks,id'],
            'reason'  => ['nullable', 'string', 'max:500'],
        ]);

        $task = Task::findOrFail($data['task_id']);

        if ($task->user_id !== $request->user()->id) {
            abort(403, 'You can only request deletion of your own task.');
        }

        $alreadyPending = AdminRequest::query()
            ->where('type', 'delete_task')
            ->where('status', 'pending')
            ->where('requestable_type', Task::class)
            ->where('requestable_id', $task->id)
            ->exists();

        if ($alreadyPending) {
            return response()->json([
                'message' => 'A delete request for this task is already pending approval.',
            ], 409);
        }

        $adminRequest = AdminRequest::create([
            'user_id'          => $request->user()->id,
            'type'             => 'delete_task',
            'status'           => 'pending',
            'requestable_type' => Task::class,
            'requestable_id'   => $task->id,
        ]);

        return response()->json([
            'message' => 'Delete request submitted and pending admin approval.',
            'data'    => $adminRequest,
        ], 202);
    }
}
