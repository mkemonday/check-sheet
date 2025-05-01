<section class="w-full">
    @include('partials.setup-heading', [
        'title' => __('Check Item'),
        'description' => __('Manage your check items'),
    ])
    <div>
        @canany(['create-check-item', 'edit-check-item', 'view-check-item'])
        <form wire:submit.prevent="save" class="space-y-4">
            <flux:input wire:model="name" placeholder="Item name" label="Check Item Name" />

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <flux:select wire:model="area_id" label="Area">
                        <option value="">Choose area...</option> <!-- Default unselected option -->
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:select wire:model="method_id" label="Method">
                        <option value="">Choose method...</option> <!-- Default unselected option -->
                        @foreach ($methods as $method)
                            <flux:select.option value="{{ $method->id }}">{{ $method->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            <div x-data="{ previewUrl: '', previewing: false }">
                <flux:input key="upload-{{ $uploadIteration }}" type="file" wire:model="newPhotos" label="Upload Pictures" multiple />
            
                @if ($photos)
                    <div class="mt-2 flex flex-rows gap-4">
                        @foreach ($photos as $index => $photo)
                            <div class="relative flex items-center">
                                <img 
                                    src="{{ is_string($photo) ? asset('storage/' . $photo) : $photo->temporaryUrl() }}" 
                                    alt="Preview" 
                                    class="w-32 h-32 object-cover rounded-md cursor-pointer"
                                    @click="previewUrl = '{{ is_string($photo) ? asset('storage/' . $photo) : $photo->temporaryUrl() }}'; previewing = true"
                                >
                                <button type="button" wire:click="removePhoto({{ $index }})" class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">
                                    ✕
                                </button>
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
            

            <div class="flex space-x-2">
                <flux:button type="submit" variant="primary" color="indigo">Save</flux:button>
                <flux:button type="button" variant="danger" color="gray" wire:click="cancel">Cancel</flux:button>
            </div>
        </form>
        @endcanany

        <div class="overflow-x-auto mt-6">
            <div class="mb-4 px-1">
                <flux:input wire:model.live.debounce.1000ms="search" placeholder="Search check items..." label="Search"
                    icon="magnifying-glass" />
            </div>
            <table class="min-w-full border-collapse border border-gray-200">
                <thead>
                    <tr>
                        <th class="border px-4 py-3 text-left">Name</th>
                        <th class="border px-4 py-3 text-left">Area</th>
                        <th class="border px-4 py-3 text-left">Method</th>
                        <th class="border px-4 py-3 text-center w-1/12">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td class="border px-4 py-1">{{ $item->name }}</td>
                            <td class="border px-4 py-1">{{ $item->area->name }}</td>
                            <td class="border px-4 py-1">{{ $item->method->name }}</td>
                            <td class="border px-4 py-1 text-center">
                                @canany(['edit-check-item', 'delete-check-item'])
                                    <div class="space-x-2">
                                        @can('edit-check-item')
                                            <flux:button variant="primary" wire:click="edit({{ $item->id }})"
                                                color="indigo" icon="pencil-square" class="text-sm">
                                            </flux:button>
                                        @endcan
                                        @can('delete-check-item')
                                            <flux:button variant="danger" wire:click="delete({{ $item->id }})"
                                                color="red" icon="trash" class="text-sm">
                                            </flux:button>
                                        @endcan
                                    </div>
                                @endcanany
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ $items->links() }}</div>
        </div>
    </div>
</section>
