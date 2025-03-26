<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Hours;
use App\Models\Member;
use App\Models\Task;

class ViewHoursComponent extends Component
{
    public $members, $tasks, $hours, $hoursInput;
    public $selectedMember = null, $selectedTask = null;
    public $hourToDelete = null;

    // Fields for editing
    public $editHourId, $editMemberId, $editTaskId, $editHours, $editDate;

    public function mount()
    {
        // Fetch all members and tasks to populate the dropdowns
        $this->members = Member::all();
        $this->tasks = Task::all();

        // Initially load all hours
        $this->hours = Hours::with(['member', 'task'])->get();
    }

    public function updatedHoursInput($value)
    {
        $regex = '/^([0-9]{1,2}):([0-9]{2})$/';
        if (preg_match($regex, $value, $matches)) {
            $hours = (int) $matches[1];
            $minutes = (int) $matches[2];
            $this->editHours = $hours + ($minutes / 60);
        } else {
            $this->editHours = null;
        }
    }

    public function render()
    {
        return view('livewire.view-hours-component');
    }

    // Filter hours based on member and task selection
    public function filterHours()
    {
        $query = Hours::query();

        // Apply filters if selected
        if ($this->selectedMember) {
            $query->where('member_id', $this->selectedMember);
        }

        if ($this->selectedTask) {
            $query->where('task_id', $this->selectedTask);
        }

        // Eager load the member and task relationships
        $this->hours = $query->with(['member', 'task'])->get();
    }

    // Edit a specific hour entry
    public function editHour($id)
    {
        // Find the hour by its ID
        $hour = Hours::find($id);

        // Set the form fields for editing
        $this->editHourId = $hour->id;
        $this->editMemberId = $hour->member_id;
        $this->editTaskId = $hour->task_id;
        $this->editHours = $hour->hours;
        $this->editDate = $hour->date;

        // Convert decimal hours to hh:mm format
        $hours = floor($hour->hours);
        $minutes = ($hour->hours - $hours) * 60;
        $this->hoursInput = sprintf('%02d:%02d', $hours, $minutes);
    }

    // Update the hour entry
    public function updateHour()
    {
        // Validate the data
        $this->validate([
            'editMemberId' => 'required|exists:members,id',
            'editTaskId' => 'required|exists:tasks,id',
            'editHours' => 'required|numeric',
            'editDate' => 'required|date',
        ]);

        // Find and update the hour record
        $hour = Hours::find($this->editHourId);
        $hour->update([
            'member_id' => $this->editMemberId,
            'task_id' => $this->editTaskId,
            'hours' => $this->editHours,
            'date' => $this->editDate,
        ]);

        // Reset edit fields and reload data
        $this->resetEditFields();
        $this->filterHours();  // Re-filter hours based on existing filters
    }

    public function confirmDelete($hourId)
    {
        $this->hourToDelete = $hourId;
    }

    public function deleteConfirmed()
    {
        $this->delete($this->hourToDelete);
        $this->hourToDelete = null;
    }

    // Delete a specific hour entry
    public function delete($id)
    {
        // Find and delete the hour record
        Hours::find($id)->delete();

        // Optionally, you can add a session flash message
        session()->flash('message', 'Hour deleted successfully.');

        // Reload the data after deletion
        $this->filterHours();  // Re-filter hours based on existing filters
    }

    // Reset the edit fields
    public function resetEditFields()
    {
        $this->editHourId = null;
        $this->editMemberId = null;
        $this->editTaskId = null;
        $this->editHours = null;
        $this->editDate = null;
        $this->hoursInput = null;
    }
}
