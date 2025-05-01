<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class AssignRoles extends Component
{
    public $users;
    public $roles;
    public $selectedRoles = [];

    public function mount()
    {
        $this->users = User::with('roles')->get();
        $this->roles = Role::all();

        // Pre-fill selectedRoles for each user
        foreach ($this->users as $user) {
            $this->selectedRoles[$user->id] = $user->roles->pluck('name')->toArray();
        }
    }

    public function updatedSelectedRoles()
    {
        foreach ($this->selectedRoles as $userId => $roles) {
            $user = \App\Models\User::find($userId);
            if ($user) {
                $user->syncRoles($roles ?? []);
            }
        }

        session()->flash('success', 'Roles updated successfully!');
    }

    public function render()
    {
        return view('livewire.users.assign-roles');
    }
}

