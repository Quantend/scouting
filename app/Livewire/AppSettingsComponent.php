<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Hours;
use App\Models\Log;
use App\Models\Member;
use App\Models\Task;
use App\Models\User;

class AppSettingsComponent extends Component
{
    public $resetAppConfirm = false;
    public $resetAppConfirm2 = false;
    public $deleteLogs = false;
    public $deleteHours = false;
    public $deleteMembers = false;
    public $deleteTasks = false;
    public $deleteUsers = false;


    public function confirmResetApp()
    {
        $this->resetAppConfirm = true;
    }

    public function confirmResetApp2()
    {
        $this->resetAppConfirm = false;
        $this->resetAppConfirm2 = true;
    }

    public function resetInputFields()
    {
        $this->reset([
            'resetAppConfirm',
            'resetAppConfirm2',
            'deleteLogs',
            'deleteHours',
            'deleteMembers',
            'deleteTasks',
            'deleteUsers',
        ]);
    }


    public function sendDbEmail()
    {
        Artisan::call('email:send-db', [
            'context' => 'manual',
        ]);
        session()->flash('message', 'email:send-db is uitgevoerd.');
    }

    public function resetApp()
    {
        $user = Auth::user();
        // Eerst backup via mail versturen
        try {
            Log::create([
                'user_id' => $user->id,
                'type' => 'Reset',
                'log' => 'Start resetting app',
            ]);

            Artisan::call('email:send-db', [
                'context' => 'reset',
            ]);
        } catch (\Exception $e) {
            session()->flash('message', 'Fout bij versturen van backup: ' . $e->getMessage());
            return;
        }


        if ($this->deleteLogs) {
            Log::truncate();
            $logs = 'Logs,';
        } else {
            $logs = '';
        }

        if ($this->deleteHours) {
            Hours::truncate();
            $hours = 'Hours,';
        } else {
            $hours = '';
        }

        if ($this->deleteMembers) {
            Member::truncate();
            $members = 'Members,';
        } else {
            $members = '';
        }

        if ($this->deleteTasks) {
            Task::truncate();
            $tasks = 'Tasks,';
        } else {
            $tasks = '';
        }

        if ($this->deleteUsers){
            User::where('is_super_admin', false)->delete();
            $users = 'Users';
        } else {
            $users = '';
        }

        Log::create([
            'user_id' => $user->id,
            'type' => 'Admin',
            'log' => 'Reset app clearing:' . $logs . ' ' . $hours . ' ' . $members . ' ' . $tasks . ' ' . $users,
        ]);

        session()->flash('message', 'Geselecteerde data is verwijderd. Backup is verstuurd.');
        $this->resetInputFields();
    }

    public function render()
    {
        return view('livewire.app-settings-component');
    }
}
