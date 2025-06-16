<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class AdminComponent extends Component
{
    public $users;
    public $search;
    public $confirmDelete;
    public $confirmDeleteUser;
    public $confirmSuperAdmin;
    public $selectedUserId;
    public $selectedUserName;
    public $showDeleted = false;


    public function mount()
    {
        $user = User::where('email', 'dylanbroens2003@gmail.com')->first();
        if ($user) {
            if ($user->is_admin = false) {
                $user->is_admin = true;
                $user->save();
            }
        }

        $this->updateUserList(); // Call method to load the user list on mount
    }

    public function updateUserList()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('email', 'like', '%' . $this->search . '%')
                    ->orWhere('name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->showDeleted) {
            $query->where('is_deleted', true);
        } else {
            $query->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', false);
            });
        }

        $this->users = $query->get();
    }


    // This method will handle the toggling of the 'is_admin' status
    public function updateAdminStatus($userId, $isAdmin)
    {
        $user = User::find($userId);

        if ($user) {
            // Alleen toestaan als admin status wordt ingeschakeld
            if (!$user->is_admin && $isAdmin) {
                $user->is_admin = true;
                $user->save();
            }

            // Super_admin mag nooit worden aangepast via deze functie
            if ($user->is_super_admin) {
                $user->is_admin = true;
            }

            // Refresh user list
            $this->updateUserList();
        }
    }

    public function confirmSuperAdminStatus($userId, $userName)
    {
        $this->confirmSuperAdmin = true;
        $this->selectedUserId = $userId;
        $this->selectedUserName = $userName;
    }

    public function makeUserSuperAdmin()
    {
        $user = User::find($this->selectedUserId);

        if ($user) {
            $user->is_admin = true;
            $user->is_super_admin = true;
            $user->save();

            // Refresh user list after update
            $this->updateUserList();
        }

        $this->confirmSuperAdmin = false;
        $this->selectedUserId = '';
        $this->selectedUserName = '';
    }

    public function toggleConfirmingSuperAdminStatus()
    {
        $this->confirmSuperAdmin = false;
        $this->selectedUserId = '';
        $this->selectedUserName = '';
    }

    public function confirmDeleteSelectedUser($userId, $userName)
    {
        $this->selectedUserId = $userId;
        $this->selectedUserName = $userName;
        $this->confirmDeleteUser = true;
    }

    public function deleteUser($userID)
    {
        $user = User::find($userID);

        if ($user) {
            if ($user->is_admin) {
                // Mark admin users as "deleted"
                $user->is_deleted = true;
                $user->save();

                session()->flash('message', 'Admin user marked as deleted.');
            } else {
                // Delete non-admin users normally
                $user->delete();

                session()->flash('message', 'User deleted successfully.');
            }

            // Refresh user list after update/deletion
            $this->updateUserList();
        } else {
            session()->flash('error', 'User not found.');
        }

        $this->selectedUserId = '';
        $this->selectedUserName = '';
        $this->confirmDeleteUser = false;
    }


    public function confirmDeleteAll()
    {
        if (User::where('is_admin', false)->count() >= 1){
            $this->confirmDelete = true;
        } else {
            session()->flash('error', 'No users to delete.');
        }
    }

    public function toggleConfirmDelete()
    {
        $this->selectedUserId = '';
        $this->selectedUserName = '';
        $this->confirmDelete = false;
        $this->confirmDeleteUser = false;
    }

    public function deleteAllNonAdminUsers()
    {
        User::where('is_admin', false)->delete();

        $this->confirmDelete = false;

        // Refresh user list after deletion
        $this->updateUserList();

        session()->flash('message', 'Deleted all non admin users.');
    }

    public function toggleShowDeleted()
    {
        $this->showDeleted = !$this->showDeleted;
        $this->updateUserList();
    }

    public function restoreUser($userId)
    {
        $user = User::find($userId);

        if ($user && $user->is_deleted) {
            $user->is_deleted = false;
            $user->save();

            session()->flash('message', 'Gebruiker succesvol hersteld.');
            $this->updateUserList();
        } else {
            session()->flash('error', 'Kan gebruiker niet herstellen.');
        }
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->updateUserList();
    }

    public function render()
    {
        return view('livewire.admin-component', [
            'users' => $this->users,
        ]);
    }
}
