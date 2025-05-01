<section class="w-full">
    @include('partials.setup-heading', [
        'title' => __('Method'),
        'description' => __('Manage your methods'),
    ])
    <div>
        <form wire:submit.prevent="save" class="space-y-4">
            <flux:input wire:model="name" placeholder="Method name" label="Method Name" />
            <div class="flex space-x-2">
                <flux:button type="submit" variant="primary" color="indigo">Save</flux:button>
                <flux:button type="button" variant="danger" color="gray" wire:click="cancel">Cancel</flux:button>
            </div>
        </form>

        <div class="overflow-x-auto mt-6">
            <div class="mb-4 px-1">
                <flux:input wire:model.live.debounce.1000ms="search" placeholder="Search methods..." label="Search" icon="magnifying-glass" />
            </div>
            <table class="min-w-full border-collapse border border-gray-200">
                <thead>
                    <tr>
                        <th class="border px-4 py-3 text-left">
                            Method Name</th>
                        <th class="border px-4 py-3 text-center w-1/12">
                            Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($methods as $method)
                        <tr>
                            <td class="border px-4 py-1">{{ $method->name }}</td>
                            <td class="border px-4 py-1 text-center">
                                <flux:button variant="primary" color="indigo" wire:click="edit({{ $method->id }})"
                                    icon="pencil-square" />
                                <flux:button variant="danger" color="red" wire:click="delete({{ $method->id }})"
                                    icon="trash" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ $methods->links() }}</div>
        </div>
    </div>
</section>
