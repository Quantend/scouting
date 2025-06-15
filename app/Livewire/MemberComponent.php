<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\Hours;
use Illuminate\Support\Facades\DB;

class MemberComponent extends Component
{
    public $name;
    public $nameTasks;
    public $member_id;
    public $isEdit = false;
    public $viewTasks = false; // Flag to toggle task view
    public $tasksWithHours = []; // Stores the tasks and associated hours for the selected member
    public $memberToDelete = null;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    public function render()
    {
        return view('livewire.member-component', [
            'members' => Member::all(),
            'tasksWithHours' => $this->tasksWithHours,
        ]);
    }

    public function resetFields()
    {
        $this->name = '';
        $this->member_id = null;
        $this->isEdit = false;
        $this->viewTasks = false; // Reset task view when canceling
        $this->tasksWithHours = [];
    }

    public function store()
    {
        $this->validate();

        Member::create([
            'name' => $this->name,
        ]);

        Log::create([
            'user_id' => Auth::id(),
            'type' => 'Created member',
            'log' => "Created member: $this->name",
        ]);

        session()->flash('message', 'Lid succesvol aangemaakt.');
        $this->resetFields();
    }

    public function edit($id)
    {
        $member = Member::findOrFail($id);
        $this->member_id = $member->id;
        $this->name = $member->name;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        $member = Member::findOrFail($this->member_id);
        $oldName = $member->name;

        $member->update([
            'name' => $this->name,
        ]);

        if($oldName != $member->name){
            Log::create([
                'user_id' => Auth::id(),
                'type' => 'Updated member',
                'log' => "Updated member: $oldName → $this->name",
            ]);
            session()->flash('message', 'Lid succesvol bijgewerkt.');
        } else {
            session()->flash('message', 'Geen wijzigen gedetecteerd.');
        }

        session()->flash('message', 'Lid succesvol bijgewerkt.');
        $this->resetFields();
    }

    public function confirmDelete($id)
    {
        if (Hours::where('member_id', $id)->exists()) {
            session()->flash('messageError', 'Lid kan niet worden verwijderd omdat het al geregistreerde uren heeft.');
            return redirect()->back();
        }

        $this->memberToDelete = $id;
        return redirect()->back();
    }

    public function deleteConfirmed()
    {
        // Proceed with deletion
        $this->delete($this->memberToDelete);
        // Reset taskToDelete after deletion
        $this->memberToDelete = null;
    }

    public function delete($id)
    {
        $member = Member::findOrFail($id);
        $memberName = $member->name;
        $member->delete();

        Log::create([
            'user_id' => Auth::id(),
            'type' => 'Deleted member',
            'log' => "Deleted member ID: $id, name: $memberName",
        ]);
        session()->flash('message', 'Lid succesvol verwijderd.');
    }

    // Updated function to view tasks and total hours for a member
    public function viewTasksFunc($memberId)
    {
        $member = Member::findOrFail($memberId);
        $this->nameTasks = $member->name;

        // Query to get the tasks and total hours for a specific member, grouped by task_id
        $this->tasksWithHours = Hours::where('member_id', $memberId)
            ->join('tasks', 'tasks.id', '=', 'hours.task_id')  // Join the tasks table to get task titles
            ->select('tasks.title', 'hours.task_id', DB::raw('SUM(hours.hours) as total_hours'))
            ->groupBy('hours.task_id', 'tasks.title')  // Group by task_id and title
            ->get();

        $this->viewTasks = true; // Toggle the task view
    }

    public function hideTasks()
    {
        $this->viewTasks = false;
        $this->tasksWithHours = []; // Clear tasks and hours when hiding
    }
}
