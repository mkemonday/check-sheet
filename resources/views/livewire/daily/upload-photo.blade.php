<div x-data="photoCapture()" x-init="init">
    <button @click="capturePhoto">Capture and Save Photo</button>

    <div id="webcam" class="mt-2"></div>

    <img id="captured-image" class="mt-2" src="" alt="Captured Image"
        onerror="this.style.display='none'; document.getElementById('canvas-placeholder').style.display='block';" />
    <canvas id="canvas-placeholder" class="mt-2" width="320" height="240"
        style="display: none; background-color: #f0f0f0; border: 1px solid #ccc;"></canvas>

    <!-- Hidden input for syncing Livewire value -->
    <input type="hidden" x-model="photo" x-ref="photoInput">

    <div class="mt-2">
        <button wire:click="save">Save Photo</button>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

<script>
    function photoCapture() {
        return {
            photo: '',
            init() {
                Webcam.set({
                    width: 320,
                    height: 240,
                    image_format: 'jpeg',
                    jpeg_quality: 90,
                });
                Webcam.attach('#webcam');
            },
            capturePhoto() {
                Webcam.snap((dataUri) => {
                    this.photo = dataUri;
                    document.getElementById('captured-image').src = dataUri;
                    this.$wire.set('photo', dataUri); // ✅ Correct Livewire sync
                });
            }
        }
    }
</script>
