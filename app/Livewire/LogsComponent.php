<?php

namespace App\Livewire;

use App\Models\Log;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class LogsComponent extends Component
{
    use WithPagination;

    public $type = '';
    public $user = '';
    public $date = '';
    public $filters = [
        'type' => '',
        'user' => '',
        'date' => '',
    ];

    public function applyFilters()
    {
        $this->type = $this->filters['type'];
        $this->user = $this->filters['user'];
        $this->date = $this->filters['date'];

        $this->resetPage(); // reset pagination to first page
    }

    public function resetFilters()
    {
        $this->filters = [
            'type' => '',
            'user' => '',
            'date' => '',
        ];

        $this->type = '';
        $this->user = '';
        $this->date = '';

        $this->resetPage();
    }

    public function clearAllLogs()
    {
        // Eerst backup via mail versturen
        try {
            Artisan::call('email:send-db');
        } catch (\Exception $e) {
            session()->flash('message', 'Fout bij versturen van backup: ' . $e->getMessage());
            return;
        }

        $user = Auth::user();
        // Optional: wrap in a transaction for safety
        DB::transaction(function () use ($user) {
            Log::truncate(); // This deletes all rows efficiently

            // Recreate a log stating that logs were cleared
            Log::create([
                'user_id' => $user->id,
                'type' => 'Admin',
                'log' => 'All logs were cleared by ' . ($user->name ?? 'unknown user'),
            ]);
        });

        session()->flash('message', 'All logs have been cleared.');

        $this->resetPage(); // Reset pagination after deletion
    }

    public function render()
    {
        $logs = Log::query()
            ->when($this->type, fn($q) => $q->where('type', $this->type))
            ->when($this->user, fn($q) => $q->where('user_id', $this->user))
            ->when($this->date, fn($q) => $q->whereDate('created_at', $this->date))
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('livewire.logs-component', [
            'logs' => $logs,
            'types' => Log::select('type')->distinct()->pluck('type'),
            'users' => User::select('id', 'name')->get(),
        ]);
    }
}
