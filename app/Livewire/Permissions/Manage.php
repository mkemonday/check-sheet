<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class Manage extends Component
{
    use WithPagination;

    public $permissionId;
    public $name;
    public $search = '';

    public function render()
    {
        $permissions = Permission::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.permissions.manage', [
            'permissions' => $permissions,
        ]);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|unique:permissions,name,' . $this->permissionId,
        ]);

        Permission::updateOrCreate(['id' => $this->permissionId], [
            'name' => $this->name,
        ]);

        $this->resetForm();
        session()->flash('success', 'Permission saved successfully!');
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        $this->permissionId = $permission->id;
        $this->name = $permission->name;
    }

    public function delete($id)
    {
        Permission::findOrFail($id)->delete();
        session()->flash('success', 'Permission deleted!');
    }

    public function cancel()
    {
        $this->resetForm();
    }

    protected function resetForm()
    {
        $this->reset(['permissionId', 'name']);
        $this->resetPage();
    }
}

