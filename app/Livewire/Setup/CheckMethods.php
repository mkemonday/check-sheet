<?php

namespace App\Livewire\Setup;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CheckMethod;
use Illuminate\Support\Facades\DB;

class CheckMethods extends Component
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
            CheckMethod::updateOrCreate(['id' => $this->editingId], ['name' => $this->name]);
            DB::commit();

            session()->flash('success', $this->editingId ? 'Check method updated successfully.' : 'Check method created successfully.');
            $this->reset(['name', 'editingId']);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'An error occurred while saving the check method.');
        }
    }

    public function edit($id)
    {
        $method          = CheckMethod::findOrFail($id);
        $this->editingId = $id;
        $this->name      = $method->name;
    }

    public function delete($id)
    {
        CheckMethod::destroy($id);
    }

    public function cancel()
    {
        $this->reset(['name', 'editingId']);
    }

    public function render()
    {
        return view('livewire.setup.check-methods', [
            'methods' => CheckMethod::where('name', 'like', "%{$this->search}%")
                ->latest()
                ->paginate(10),
        ]);
    }
}
