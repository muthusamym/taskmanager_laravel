<?php

namespace App\Http\Controllers;
use App\Jobs\SendTaskNotification;
use Illuminate\Support\Facades\Cache;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Cache::remember('tasks', 60, function () {
        return Task::with('user')->get();
    });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
  public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
    ]);

    $task = Task::create([
        'title' => $validated['title'],
        'description' => $validated['description'],
        'user_id' => auth()->id() // ✅ Ensure user_id is set correctly
    ]);

    return response()->json($task, 201);
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $request->validate(['status' => 'required|in:pending,in_progress,completed']);
    $task->update(['status' => $request->status]);
      // Dispatch Job
    dispatch(new SendTaskNotification($task->user, $task));

    return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task->delete();
    return response()->json(['message' => 'Task deleted']);
    }
}
