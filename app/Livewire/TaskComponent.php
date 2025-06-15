<?php

namespace App\Livewire;

use App\Models\Hours;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\Task;

class TaskComponent extends Component
{
    public $title, $money, $task_id;
    public $isEdit = false;
    public $viewMembers = false; // Flag to toggle task view
    public $membersWithHours = []; // Stores the tasks and associated hours for the selected member
    public $taskToDelete = null;


    protected $rules = [
        'title' => 'required|string|max:255',
        'money' => 'nullable|numeric',
    ];

    public function render()
    {
        return view('livewire.task-component', [
            'tasks' => Task::all(),  // Fetch all tasks without pagination
        ]);
    }

    public function resetFields()
    {
        $this->title = '';
        $this->money = '';
        $this->task_id = null;
        $this->isEdit = false;
    }

    public function store()
    {
        $this->validate();

        Task::create([
            'title' => $this->title,
            'money' => empty($this->money) ? 0 : $this->money,
        ]);

        Log::create([
            'user_id' => Auth::id(),
            'type' => 'Created task',
            'log' => "Created task: $this->title, money: $this->money",
        ]);

        session()->flash('message', 'Klus succesvol aangemaakt.');
        $this->resetFields();
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $this->task_id = $task->id;
        $this->title = $task->title;
        $this->money = $task->money;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'money' => 'nullable|numeric',
        ]);

        $task = Task::findOrFail($this->task_id);
        $oldTitle = $task->title;
        $oldMoney = $task->money;

        $task->update([
            'title' => $this->title,
            'money' => empty($this->money) ? 0 : $this->money,
        ]);

        if ($oldTitle != $task->title || $oldMoney != $task->money) {
            Log::create([
                'user_id' => Auth::id(),
                'type' => 'Updated title',
                'log' => "Updated title: $oldTitle → $this->title, money: $oldMoney → $this->money",
            ]);

            session()->flash('message', 'Klus succesvol bijgewerkt.');
        } else {
            session()->flash('message', 'Geen wijzigen gedetecteerd.');
        }

        $this->resetFields();
    }

    public function confirmDelete($taskId)
    {
        if (Hours::where('task_id', $taskId)->exists()) {
            session()->flash('messageError', 'Klus kan niet worden verwijderd omdat het al geregistreerde uren heeft.');
            return redirect()->back();
        }

        $this->taskToDelete = $taskId;
        return redirect()->back();
    }

    public function deleteConfirmed()
    {
        // Proceed with deletion
        $this->delete($this->taskToDelete);
        // Reset taskToDelete after deletion
        $this->taskToDelete = null;
    }

    public function delete($id)
    {
        $task = Task::findOrFail($id);
        $taskTitle = $task->title;
        $task->delete();

        Log::create([
            'user_id' => Auth::id(),
            'type' => 'Deleted member',
            'log' => "Deleted task ID: $id, title: $taskTitle",
        ]);

        session()->flash('message', 'Klus succesvol verwijderd.');
    }

    // Updated function to view tasks and total hours for a member
    public function viewMembersFunc($taskId)
    {
        // Query to get the tasks and total hours for a specific member, grouped by task_id
        $this->membersWithHours = Hours::where('task_id', $taskId)
            ->join('members', 'members.id', '=', 'hours.member_id')  // Join the tasks table to get task titles
            ->select('members.name', 'hours.member_id', DB::raw('SUM(hours.hours) as total_hours'))
            ->groupBy('hours.member_id', 'members.name')  // Group by task_id and title
            ->get();

        $this->viewMembers = true; // Toggle the task view
    }

    public function hideMembers()
    {
        $this->viewMembers = false;
        $this->membersWithHours = []; // Clear tasks and hours when hiding
    }
}
