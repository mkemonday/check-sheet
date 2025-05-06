<?php

namespace App\Livewire\Daily;

use App\Models\Area;
use App\Models\CheckItem;
use App\Models\DailyCheck;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\On;

class DailyCheckForm extends Component
{
    public $check_item_id = null;
    public $check_date;
    public $status;
    public $remarks;
    public $password;
    public $id                      = null;
    public $area_name               = null;
    public array $capturedPhotos    = [];
    public $selectedCheckItemPhotos = [];

    public $showCameraModal   = false;
    public $showPasswordModal = false;

    public function mount($id = null)
    {
        $this->check_date = Carbon::now()->format('Y-m-d');

        if ($id) {
            $this->id = $id;
            $area     = Area::find($id);
            if (!$area) {
                return redirect()->route('dashboard');
            }
            $this->area_name = $area->name;
        } else {
            return redirect()->route('dashboard');
        }
    }

    #[On('photoCaptured')]
    public function photoCaptured($image)
    {
        $image     = str_replace('data:image/jpeg;base64,', '', $image);
        $image     = str_replace(' ', '+', $image);
        $imageData = base64_decode($image);

        $directory = 'daily-checks/photos/';
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $filename = $directory . uniqid() . '.jpg';
        Storage::disk('public')->put($filename, $imageData);

        $this->capturedPhotos[] = $filename;
    }

    public function openCameraModal()
    {
        $this->showCameraModal = true;
        $this->dispatch('startCamera');
    }

    public function captureFace()
    {
        $this->dispatch('captureImage');
    }

    public function confirmPassword()
    {
        $this->showPasswordModal = true;
    }

    public function save()
    {

        if (!Hash::check($this->password, auth()->user()->password)) {
            $this->addError('password', 'Incorrect password.');
            return;
        }

        $this->validate([
            'check_item_id' => 'required|exists:check_items,id',
            'check_date'    => 'required|date',
            'status'        => 'required|in:no_problem,minor_problem,major_problem',
            'remarks'       => 'nullable|string',
        ]);

        $statusValue = [
            'no_problem'    => 'checked',
            'minor_problem' => 'minor_problem',
            'major_problem' => 'major_problem',
        ][$this->status];

        try {
            DB::beginTransaction();

        $dailyCheck = DailyCheck::updateOrCreate(
            [
                'check_item_id' => $this->check_item_id,
                'check_date'    => $this->check_date,
            ],
            [
                'status'     => $statusValue,
                'remarks'    => $this->remarks,
                'checked_by' => auth()->id(),
                'checked_at' => now(),
            ]
        );

        foreach ($this->capturedPhotos as $path) {
            $dailyCheck->photos()->create(['file_path' => $path]);
        }

        DB::commit();

        $this->reset(['check_item_id', 'status', 'remarks', 'capturedPhotos', 'password', 'showPasswordModal', 'selectedCheckItemPhotos']);
        session()->flash('success', 'Saved successfully.');
        return redirect()->route('daily.daily-check-matrix');
        $this->dispatch('resetPreview');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'An error occurred while saving the check.');
            return;
        }
    }

    public function cancel()
    {
        $this->cleanupPhotos();
        $this->reset(['check_item_id', 'status', 'remarks', 'capturedPhotos', 'selectedCheckItemPhotos']);
        $this->dispatch('resetPreview');
    }

    protected function cleanupPhotos()
    {
        foreach ($this->capturedPhotos as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    public function onCheckItemChange()
    {
        $this->selectedCheckItemPhotos = [];
        if ($this->check_item_id) {
            $checkItem = CheckItem::find($this->check_item_id);

            if ($checkItem->photo_paths) {
                $this->selectedCheckItemPhotos = json_decode($checkItem->photo_paths, true);
            }
        }
    }

    public function render()
    {
        return view('livewire.daily.daily-check-form', [
            'checkItems' => CheckItem::with('area', 'method')->where('area_id', $this->id)->get()
        ]);
    }
}
