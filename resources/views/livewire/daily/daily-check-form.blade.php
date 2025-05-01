<section class="w-full">
    @include('partials.setup-heading', [
        'title' => __('Daily Check'),
        'description' => __('Log daily inspection results'),
    ])

    <form wire:submit.prevent="confirmPassword" class="space-y-4">
        <flux:input wire:model="area_name" label="Area" readonly />

        <flux:select wire:model="check_item_id" label="Check Item" wire:change="onCheckItemChange">
            <flux:select.option value="">Select check item ...</flux:select.option>
            @foreach ($checkItems as $item)
            <flux:select.option value="{{ $item->id }}">
                {{ $item->name }} : {{ $item->method->name }}
            </flux:select.option>
            @endforeach
        </flux:select>

        @if ($selectedCheckItemPhotos)
        <div x-data="{ previewUrl: '', previewing: false }">
            @if ($selectedCheckItemPhotos)
                <div class="mt-2 flex flex-rows gap-4">
                    @foreach ($selectedCheckItemPhotos as $index => $photo)
                        <div class="relative flex items-center">
                            <img 
                                src="{{ is_string($photo) ? asset('storage/' . $photo) : $photo->temporaryUrl() }}" 
                                alt="Preview" 
                                class="w-32 h-32 object-cover rounded-md cursor-pointer"
                                @click="previewUrl = '{{ is_string($photo) ? asset('storage/' . $photo) : $photo->temporaryUrl() }}'; previewing = true"
                            >
                        </div>
                    @endforeach
                </div>
            @endif
        
            <!-- Modal for preview -->
            <div 
                x-show="previewing" 
                x-transition 
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 z-50"
                @click.away="previewing = false"
            >
                <div class="max-w-3xl p-4">
                    <img :src="previewUrl" class="w-full h-auto rounded-lg shadow-lg">
                    <button 
                        type="button" 
                        class="absolute top-4 right-4 text-white text-3xl"
                        @click="previewing = false"
                    >
                        ✕
                    </button>
                </div>
            </div>
        </div>
        @endif

        <flux:input type="date" wire:model="check_date" label="Check Date" readonly />

        <flux:select wire:model="status" label="Status">
            <flux:select.option value="">Select status ...</flux:select.option>
            <flux:select.option value="ok">OK</flux:select.option>
            <flux:select.option value="not_ok">Not OK</flux:select.option>
            <flux:select.option value="na">N/A</flux:select.option>
        </flux:select>

        <flux:textarea wire:model="remarks" label="Remarks" placeholder="Add remarks here..." />

        <div class="mt-4">
            <flux:button type="button" wire:click="openCameraModal" variant="primary">
                Open Camera
            </flux:button>
        </div>

        @if ($capturedPhotos)
            <div class="mt-4 grid grid-cols-3 gap-4">
                @foreach ($capturedPhotos as $photo)
                    <img src="{{ Storage::url($photo) }}"
                         class="rounded border w-full aspect-square object-cover" />
                @endforeach
            </div>
        @endif

        <div class="flex space-x-2 mt-4">
            <flux:button type="submit" variant="primary">Save</flux:button>
            <flux:button type="button" variant="danger" wire:click="cancel">Cancel</flux:button>
        </div>
    </form>

    <!-- Camera Modal -->
    <flux:modal wire:model="showCameraModal" title="Capture Photo" size="xl">
        <div class="overflow-hidden mx-auto border-4 border-gray-300">
            <video id="video-face" autoplay playsinline class="w-full h-full object-cover"></video>
        </div>

        <canvas id="canvas-camera-faces" class="hidden"></canvas>

        <div class="mt-4 flex justify-end gap-2">
            <flux:button type="button" variant="danger" wire:click="$set('showCameraModal', false)">
                    Cancel
                </flux:button>
            <flux:button wire:click="captureFace" variant="primary">
                Snap
            </flux:button>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-4" id="preview-container"></div>
    </flux:modal>

    <!-- Password Modal -->
    <flux:modal wire:model="showPasswordModal" title="Confirm Save" size="md">
        <div>
            <flux:input type="password" wire:model.defer="password" label="Password" />
            @error('password') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

            <div class="mt-4 flex justify-end gap-2">
                <flux:button type="button" variant="danger" wire:click="$set('showPasswordModal', false)">
                    Cancel
                </flux:button>
                <flux:button type="button" wire:click="save" variant="primary">
                    Confirm
                </flux:button>
            </div>
        </div>
    </flux:modal>
</section>

@push('scripts')
<script>
    let stream = null;
    let video = document.getElementById('video-face');
    let canvas = document.getElementById('canvas-camera-faces');

    Livewire.on('startCamera', () => {
        navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment' },
            audio: false
        }).then((s) => {
            stream = s;
            video.srcObject = stream;
            video.play();
        }).catch((err) => {
            alert("Cannot access camera: " + err.message);
        });
    });

    Livewire.on('stopCamera', () => {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    });

    Livewire.on('captureImage', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = canvas.toDataURL('image/jpeg');
        Livewire.dispatch('photoCaptured', { image: imageData });
    });
</script>
@endpush
