<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Hours;
use App\Models\Member;
use App\Models\Task;

class ViewHoursComponent extends Component
{
    public $members, $tasks, $hours, $hoursInput;
    public $selectedMember = null, $selectedTask = null, $selectedDate = null;
    public $hourToDelete = null;

    // Fields for editing
    public $editHourId, $editMemberId, $editTaskId, $editHours, $editDate;

    public function mount()
    {
        // Fetch all members and tasks to populate the dropdowns
        $this->members = Member::all();
        $this->tasks = Task::all();

        // Initially load all hours
        $this->hours = Hours::with(['member', 'task'])->get()->sortByDesc('date');
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

        if ($this->selectedDate) {
            $query->whereDate('date', $this->selectedDate);
        }

        // Eager load the member and task relationships
        $this->hours = $query->with(['member', 'task'])->get()->sortByDesc('date');
    }

    public function resetFilters()
    {
        $this->selectedMember = null;
        $this->selectedTask = null;
        $this->selectedDate = null;

        $this->hours = Hours::query()->get();
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
        $this->validate([
            'editMemberId' => 'required|exists:members,id',
            'editTaskId' => 'required|exists:tasks,id',
            'editHours' => 'required|numeric',
            'editDate' => 'required|date',
        ]);

        $hour = Hours::findOrFail($this->editHourId);

        // Save old values
        $oldMember = Member::find($hour->member_id)?->name ?? 'Onbekend';
        $oldTask = Task::find($hour->task_id)?->title ?? 'Onbekend';
        $oldHours = $hour->hours;
        $oldDate = $hour->date;

        // Nieuwe waardes ophalen
        $newMember = Member::find($this->editMemberId)?->name ?? 'Onbekend';
        $newTask = Task::find($this->editTaskId)?->title ?? 'Onbekend';

        // Update the record
        $hour->update([
            'member_id' => $this->editMemberId,
            'task_id' => $this->editTaskId,
            'hours' => $this->editHours,
            'date' => $this->editDate,
        ]);

        // Log only if something changed
        if (
            $oldMember !== $newMember ||
            $oldTask !== $newTask ||
            $oldHours != $this->editHours ||
            $oldDate != $this->editDate
        ) {
            Log::create([
                'user_id' => Auth::id(),
                'type' => 'Updated hour entry',
                'log' => "Updated hour ID {$hour->id}: member {$oldMember} → {$newMember}, task {$oldTask} → {$newTask}, hours {$oldHours} → {$this->editHours}, date {$oldDate} → {$this->editDate}",
            ]);

            session()->flash('message', 'Uur succesvol bijgewerkt.');
        } else {
            session()->flash('message', 'Geen wijzigingen gedetecteerd.');
        }

        $this->resetEditFields();
        $this->filterHours();
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
        $hour = Hours::find($id);

        // Fetch related data before deleting
        $memberName = Member::find($hour->member_id)?->name ?? 'Unknown';
        $taskTitle = Task::find($hour->task_id)?->title ?? 'Unknown';
        $hours = $hour->hours;
        $date = $hour->date;

        $hour->delete();

        // Log the deletion
        Log::create([
            'user_id' => Auth::id(),
            'type' => 'Deleted hour entry',
            'log' => "Deleted hour ID: $id, member: {$memberName}, task: {$taskTitle}, hours: {$hours}, date: {$date}.",
        ]);

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
