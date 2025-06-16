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
    public $deleteUsers = false;

    public function confirmResetApp()
    {
        $this->resetAppConfirm = true;
    }

    public function confirmResetWithUser()
    {
        $this->resetAppConfirm2 = true;
        $this->deleteUsers = true;
    }

    public function confirmResetApp2()
    {
        $this->resetAppConfirm = false;
        $this->resetAppConfirm2 = true;
    }

    public function resetInputFields()
    {
        $this->resetAppConfirm = false;
        $this->resetAppConfirm2 = false;
        $this->deleteUsers = false;
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


        // Dan pas alles verwijderen in juiste volgorde
        // Volgorde is belangrijk ivm foreign keys
        Log::truncate();
        Hours::truncate();
        Member::truncate();
        Task::truncate();

        if ($this->deleteUsers){
            User::where('is_super_admin', false)->delete();
        }

        session()->flash('message', 'Alle data is succesvol verwijderd en backup van database is verstuurd.');
        $this->resetInputFields();
    }

    public function render()
    {
        return view('livewire.app-settings-component');
    }
}
