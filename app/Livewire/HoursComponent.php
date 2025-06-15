<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;
use App\Models\Member;
use App\Models\Task;
use App\Models\Hours; // Assuming you have an Hour model

class HoursComponent extends Component
{
    public $members, $tasks, $member_id, $task_id, $hours, $date, $hoursInput;
    public $isEdit = false;
    public $hour_id;

    public function mount()
    {
        // Fetch all members and tasks to populate the dropdowns
        $this->members = Member::all();
        $this->tasks = Task::all();
        // Set default date to today
        $this->date = now()->format('Y-m-d'); // Default to today's date
    }

    public function updatedHoursInput($value)
    {
        $regex = '/^([0-9]{1,2}):([0-9]{2})$/';
        if (preg_match($regex, $value, $matches)) {
            $hours = (int) $matches[1];
            $minutes = (int) $matches[2];
            $this->hours = $hours + ($minutes / 60);
        } else {
            $this->hours = null;
        }
    }

    public function render()
    {
        return view('livewire.hours-component');
    }

    public function store()
    {
        $this->validate([
            'member_id' => 'required|exists:members,id',
            'task_id' => 'required|exists:tasks,id',
            'hours' => 'required|numeric|min:0.01',
            'date' => 'required|date',
        ]);

        Hours::create([
            'member_id' => $this->member_id,
            'task_id' => $this->task_id,
            'hours' => $this->hours,
            'date' => $this->date,
        ]);

        if (Auth::check()) {
            $member = Member::find($this->member_id);
            $task = Task::find($this->task_id);

            Log::create([
                'user_id' => Auth::id(),
                'type' => "Registered hours",
                'log' => "Registered {$this->hours} hour(s) for member '{$member->name}' on task '{$task->title}' for date {$this->date}.",
            ]);
        }

        session()->flash('message', 'Uren succesvol geregistreerd.');

        // Reset the fields after storing
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->member_id = '';
        $this->task_id = '';
        $this->hours = '';
        $this->hoursInput = '';
        $this->date = now()->format('Y-m-d'); // Reset to today's date
    }
}
