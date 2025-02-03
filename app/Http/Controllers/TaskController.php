<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\TaskRequest;

class TaskController extends Controller
{

    public function users()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function index()
    {
        $tasks = Task::all();
        return response()->json($tasks);
    }

    public function fetchByStatus($status)
    {
        $validStatuses = ['to-do', 'in-progress', 'done'];

        if (!in_array($status, $validStatuses)) {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        $tasks = Task::where('status', $status)->get();
        return response()->json($tasks);
    }

    // public function fetchByStatus($status)
    // {
    //     $validStatuses = ['to-do', 'in-progress', 'done'];

    //     if (!in_array($status, $validStatuses)) {
    //         return response()->json(['error' => 'Invalid status'], 400);
    //     }

    //     $tasks = Task::with('users')->where('status', $status)->get();

    //     $tasksWithUser = $tasks->map(function ($task) {
    //         return [
    //             'id' => $task->id,
    //             'title' => $task->title,
    //             'description' => $task->description,
    //             'status' => $task->status,
    //             'user_assigned' => $task->users->pluck('name'), 
    //         ];
    //     });

    //     return response()->json($tasksWithUser);
    // }

    public function store(TaskRequest $request)
    {
        $data = $request->validated();

        $task = Task::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'user_assigned' => json_encode($data['user_assigned'] ?? []),
        ]);

        return response()->json($task, 201);
    }

    public function update(TaskRequest $request, $id)
    {
        $task = Task::findOrFail($id);

        $data = $request->validated();

        $userAssigned = is_array($data['user_assigned']) ? $data['user_assigned'] : json_decode($data['user_assigned'], true);

        $task->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'user_assigned' => json_encode($userAssigned),
            'status' => $data['status'],
        ]);

        return response()->json($task);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully.']);
    }
}
