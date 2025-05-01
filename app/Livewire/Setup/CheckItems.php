<?php

namespace App\Livewire\Setup;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\CheckItem;
use App\Models\Area;
use App\Models\CheckMethod;
use Illuminate\Support\Facades\DB;

class CheckItems extends Component
{
    use WithPagination, WithFileUploads;

    public $name,            $area_id, $method_id, $editingId, $search = '', $photos = [], $newPhotos = [];
    public $uploadIteration = 0;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedNewPhotos()
    {
          // When new photos selected, merge into $photos
        foreach ($this->newPhotos as $photo) {
            $this->photos[] = $photo;
        }
        $this->newPhotos = [];  // clear after merging
    }

    public function removePhoto($index)
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);  // reindex array
    }

    public function save()
    {
        $this->validate([
            'name'      => 'required|string',
            'area_id'   => 'required|exists:areas,id',
            'method_id' => 'required|exists:check_methods,id',
            'photos.*'  => 'nullable',                           // Already validated during upload if needed
        ]);

        $photoPaths = [];

        foreach ($this->photos as $photo) {
            if (is_string($photo)) {
                  // Old photo already saved
                $photoPaths[] = $photo;
            } else {
                  // New photo needs to be stored
                $photoPaths[] = $photo->store('photos', 'public');
            }
        }

        try {
            DB::beginTransaction();

            CheckItem::updateOrCreate(
                ['id' => $this->editingId],
                [
                    'name'        => $this->name,
                    'area_id'     => $this->area_id,
                    'method_id'   => $this->method_id,
                    'photo_paths' => json_encode($photoPaths),
                ]
            );
            DB::commit();

            session()->flash('success', $this->editingId ? 'Check item updated successfully.' : 'Check item created successfully.');
            $this->reset(['name', 'area_id', 'method_id', 'editingId', 'photos', 'newPhotos']);
            $this->uploadIteration++;
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'An error occurred while saving the check item.');
        }
    }

    public function edit($id)
    {
        $item            = CheckItem::findOrFail($id);
        $this->editingId = $id;
        $this->name      = $item->name;
        $this->area_id   = $item->area_id;
        $this->method_id = $item->method_id;
        $this->photos    = [];

        if ($item->photo_paths) {
            $this->photos = json_decode($item->photo_paths, true);
        }
    }

    public function delete($id)
    {
        CheckItem::destroy($id);
    }

    public function cancel()
    {
        $this->reset(['name', 'area_id', 'method_id', 'editingId', 'photos', 'newPhotos']);
        $this->uploadIteration++;
    }

    public function render()
    {
        return view('livewire.setup.check-items', [
            'items' => CheckItem::with(['area', 'method'])
                ->where('name', 'like', "%{$this->search}%")
                ->latest()
                ->paginate(10),
            'areas'     => Area::all(),
            'methods'   => CheckMethod::all(),
            'photos'    => $this->photos,
            'newPhotos' => $this->newPhotos,
        ]);
    }
}
