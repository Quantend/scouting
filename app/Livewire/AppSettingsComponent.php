<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Artisan;
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
        Artisan::call('email:send-db');
        session()->flash('message', 'email:send-db is uitgevoerd.');
    }

    public function resetApp()
    {
        // Eerst backup via mail versturen
        try {
            Artisan::call('email:send-db');
        } catch (\Exception $e) {
            session()->flash('message', 'Fout bij versturen van backup: ' . $e->getMessage());
            return;
        }


        if ($this->deleteLogs) {
            Log::truncate();
        }

        if ($this->deleteHours) {
            Hours::truncate();
        }

        if ($this->deleteMembers) {
            Member::truncate();
        }

        if ($this->deleteTasks) {
            Task::truncate();
        }

        if ($this->deleteUsers){
            User::where('is_super_admin', false)->delete();
        }

        session()->flash('message', 'Geselecteerde data is verwijderd. Backup is verstuurd.');
        $this->resetInputFields();
    }

    public function render()
    {
        return view('livewire.app-settings-component');
    }
}
