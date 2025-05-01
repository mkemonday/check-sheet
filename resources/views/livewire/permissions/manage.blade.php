<section class="w-full">
    @include('partials.setup-heading', [
        'title' => __('Permissions'),
        'description' => __('Manage available permissions'),
    ])

    <form wire:submit.prevent="save" class="space-y-4">
        <flux:input wire:model="name" placeholder="Permission name" label="Permission Name" />

        <div class="flex space-x-2">
            <flux:button type="submit" variant="primary" color="indigo">Save</flux:button>
            <flux:button type="button" variant="danger" color="gray" wire:click="cancel">Cancel</flux:button>
        </div>
    </form>

    <div class="overflow-x-auto mt-6">
        <div class="mb-4 px-1">
            <flux:input wire:model.live.debounce.1000ms="search" placeholder="Search permissions..." label="Search" icon="magnifying-glass" />
        </div>

        <table class="min-w-full border-collapse border border-gray-200">
            <thead>
                <tr>
                    <th class="border px-4 py-3 text-left">Permission Name</th>
                    <th class="border px-4 py-3 text-center w-1/12">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($permissions as $perm)
                    <tr>
                        <td class="border px-4 py-1">{{ $perm->name }}</td>
                        <td class="border px-4 py-1 text-center">
                            <div class="space-x-2">
                                <flux:button variant="primary" wire:click="edit({{ $perm->id }})" icon="pencil-square" color="indigo" class="text-sm" />
                                <flux:button variant="danger" wire:click="delete({{ $perm->id }})" icon="trash" color="red" class="text-sm" />
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $permissions->links() }}
        </div>
    </div>
</section>
