<?php

namespace App\Livewire\Daily;

use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class UploadPhoto extends Component
{
    #[Validate('required|string')]
    public string $photo = '';

    public function save()
    {
        $this->validate();

        // Decode the base64 image
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->photo));
        $fileName = 'photos/' . uniqid() . '.jpg';

        Storage::disk('public')->put($fileName, $imageData);

        session()->flash('success', 'Photo saved successfully!');
    }

    public function render()
    {
        return view('livewire.daily.upload-photo');
    }
}
