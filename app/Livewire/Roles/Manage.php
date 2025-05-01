<?php

namespace App\Livewire\Roles;

use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Manage extends Component
{
    use WithPagination;

    public $search = '';

    public $roleId = null;
    public $name = '';
    public $permissions = [];
    public $allPermissions = [];

    public function mount()
    {
        $this->allPermissions = Permission::all();
    }

    public function render()
    {
        $roles = Role::with('permissions')
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->paginate(5);

        return view('livewire.roles.manage', [
            'roles' => $roles,
        ]);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|unique:roles,name,' . $this->roleId,
        ]);

        $role = Role::updateOrCreate(['id' => $this->roleId], ['name' => $this->name]);
        $role->syncPermissions($this->permissions);

        $this->resetForm();
        session()->flash('success', 'Role saved successfully!');
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->permissions = $role->permissions()->pluck('name')->toArray();
    }

    public function delete($id)
    {
        Role::findOrFail($id)->delete();
        session()->flash('success', 'Role deleted!');
    }

    public function cancel()
    {
        $this->resetForm();
    }

    protected function resetForm()
    {
        $this->reset(['roleId', 'name', 'permissions']);
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}

