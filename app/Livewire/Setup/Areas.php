<?php

namespace App\Livewire\Setup;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Area;
use Exception;
use Illuminate\Support\Facades\DB;

class Areas extends Component
{
    use WithPagination;

    public $name, $editingId, $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function save()
    {
        $this->validate(['name' => 'required|string']);

        try {
            DB::beginTransaction();

            Area::updateOrCreate(['id' => $this->editingId], ['name' => $this->name]);

            DB::commit();

            session()->flash('success', $this->editingId ? 'Area updated successfully.' : 'Area created successfully.');

            $this->reset(['name', 'editingId']);
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'An error occurred while saving the area.');
        }
    }

    public function cancel()
    {
        $this->reset(['name', 'editingId']);
    }

    public function edit($id)
    {
        $area            = Area::findOrFail($id);
        $this->editingId = $id;
        $this->name      = $area->name;
    }

    public function delete($id)
    {
        Area::destroy($id);
    }

    public function render()
    {
        return view('livewire.setup.areas', [
            'areas' => Area::where('name', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10)
        ]);
    }
}
