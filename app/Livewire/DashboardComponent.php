<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\Hours;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;

class DashboardComponent extends Component
{
    public $totalHours;
    public $totalMoney;
    public $mvp;
    public $lvp;
    public $totalHoursMvp;
    public $totalHoursLvp;
    public $isAdmin;

    public function mount()
    {
        // Check if the user is authenticated and if they are an admin
        $user = Auth::user(); // Get the currently authenticated user
        $this->isAdmin = $user && $user->is_admin; // Set isAdmin to true if the user is an admin

        // Logs out deleted users
        if ($user && $user->is_deleted) {
            Auth::logout();

            // Optional: Invalidate the session and regenerate token
            session()->invalidate();
            session()->regenerateToken();

            return redirect()->route('/register');
        }

        $this->totalHours = Hours::sum('hours');
        $this->totalMoney = Task::sum('money');

        // Get the total hours grouped by member for MVP (Most Valuable Player)
        $memberHours = Hours::selectRaw('member_id, SUM(hours) as total_hours')
            ->groupBy('member_id')
            ->orderByDesc('total_hours')
            ->first(); // Get the member with the most hours

        // If we have a member, set the MVP details
        if ($memberHours) {
            $this->mvp = Member::find($memberHours->member_id); // Get the member with the most hours
            $this->totalHoursMvp = $memberHours->total_hours; // Set the total hours for the MVP
        } else {
            $this->mvp = null; // If no member found, set MVP to null
            $this->totalHoursMvp = 0; // Set MVP total hours to 0
        }

        // Get the total hours grouped by member for LVP (Least Valuable Player)
        $memberHoursLvp = Hours::selectRaw('member_id, SUM(hours) as total_hours')
            ->groupBy('member_id')
            ->orderBy('total_hours') // Order by ascending to get the member with the least hours
            ->first(); // Get the member with the least hours

        // If we have a member, set the LVP details
        if ($memberHoursLvp) {
            $this->lvp = Member::find($memberHoursLvp->member_id); // Get the member with the least hours
            $this->totalHoursLvp = $memberHoursLvp->total_hours; // Set the total hours for the LVP
        } else {
            $this->lvp = null; // If no member found, set LVP to null
            $this->totalHoursLvp = 0; // Set LVP total hours to 0
        }
    }

    public function render()
    {
        return view('livewire.dashboard-component');
    }
}
