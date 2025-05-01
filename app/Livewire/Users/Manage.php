<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class Manage extends Component
{
    use WithPagination;

    public $userId = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $roles = [];
    public $allRoles = [];
    public $search = '';

    public function mount()
    {
        $this->allRoles = Role::pluck('name')->toArray();
    }

    public function render()
    {
        $users = User::with('roles')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(5);

        return view('livewire.users.manage', [
            'users' => $users,
        ]);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'password' => $this->userId ? 'nullable|min:6' : 'required|min:6',
            'roles' => 'array',
        ]);

        $user = User::updateOrCreate(
            ['id' => $this->userId],
            [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password ? Hash::make($this->password) : User::find($this->userId)->password,
            ]
        );

        $user->syncRoles($this->roles);

        session()->flash('success', 'User saved.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->roles = $user->roles->pluck('name')->toArray();
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        session()->flash('success', 'User deleted.');
    }

    public function cancel()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['userId', 'name', 'email', 'password', 'roles']);
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
