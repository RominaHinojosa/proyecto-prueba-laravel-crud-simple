<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with('category')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($request->input('status') === 'pending') {
            $query->where('completed', false);
        } elseif ($request->input('status') === 'completed') {
            $query->where('completed', true);
        }

        $tasks = $query->get();
        $categories = Category::orderBy('name')->get();

        return view('tasks', compact('tasks', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'priority' => 'nullable|in:baja,media,alta',
            'attachment' => 'nullable|file|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'completed' => false,
            'category_id' => $request->category_id,
            'priority' => $request->priority ?? 'media',
            'attachment' => $attachmentPath,
        ]);

        return redirect()->back();
    }

    public function update(Task $task)
    {
        $task->update([
            'completed' => !$task->completed,
        ]);

        return redirect()->back();
    }

    public function destroy(Task $task)
    {
        if ($task->attachment) {
            Storage::disk('public')->delete($task->attachment);
        }

        $task->delete();
        return redirect()->back();
    }
}
