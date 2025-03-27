<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class AdminComponent extends Component
{
    public $users;
    public $searchEmail;
    public $confirmDelete;
    public $confirmDeleteUser;
    public $confirmSuperAdmin;
    public $selectedUserId;
    public $selectedUserName;

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

        // If there's a search term, filter by email
        if ($this->searchEmail) {
            $query->where('email', 'like', '%' . $this->searchEmail . '%');
        }

        // Fetch all users except the one with id 1
        $this->users = $query->get();
    }

    // This method will handle the toggling of the 'is_admin' status
    public function updateAdminStatus($userId, $isAdmin)
    {
        $user = User::find($userId);

        if ($user) {
            $user->is_admin = $isAdmin; // Set the 'is_admin' value to the checkbox status
            $user->save();

            // Refresh user list after update
            $this->users = User::all();
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
            $this->users = User::all();
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

    public function deleteUser()
    {
        $this->selectedUserId = User::find($this->selectedUserId);
        $this->selectedUserId->delete();

            // Refresh user list after deletion
            $this->users = User::all();

            session()->flash('message', 'User deleted successfully.');

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
        $this->users = User::all();

        session()->flash('message', 'Deleted all non admin users.');
    }

    public function render()
    {
        return view('livewire.admin-component', [
            'users' => $this->users,
        ]);
    }
}
