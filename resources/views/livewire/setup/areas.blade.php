<section class="w-full">
    @include('partials.setup-heading', [
        'title' => __('Area'),
        'description' => __('Manage your areas'),
    ])

    <div>
        @canany(['create-area', 'edit-area', 'view-area'])
            <form wire:submit.prevent="save" class="space-y-4">
                <flux:input wire:model="name" placeholder="Area name" label="Area Name" />
                <div class="flex space-x-2">
                    <flux:button type="submit" variant="primary" color="indigo">
                        Save
                    </flux:button>
                    <flux:button type="button" variant="danger" color="gray" wire:click="cancel">
                        Cancel
                    </flux:button>
                </div>
            </form>
        @endcanany

        <div class="overflow-x-auto mt-6">
            <div class="mb-4 px-1">
                <flux:input wire:model.live.debounce.1000ms="search" placeholder="Search area..." label="Search"
                    icon="magnifying-glass" />
            </div>
            <table class="min-w-full border-collapse border border-gray-200">
                <thead>
                    <tr>
                        <th scope="col" class="border px-4 py-3 text-left">
                            Area Name
                        </th>
                        <th scope="col" class="border px-4 py-3 text-center w-1/12">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($areas as $area)
                        <tr>
                            <td class="border px-4 py-1 whitespace-nowrap">
                                {{ $area->name }}
                            </td>
                            <td class="border px-4 py-1 whitespace-nowrap text-center">
                                <div class="space-x-2">
                                    @can('edit-area')
                                        <flux:button variant="primary" wire:click="edit({{ $area->id }})"
                                            color="indigo" icon="pencil-square" class="text-sm">
                                        </flux:button>
                                    @endcan
                                    @can('delete-area')
                                        <flux:button variant="danger" wire:click="delete({{ $area->id }})"
                                            color="red" icon="trash" class="text-sm">
                                        </flux:button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $areas->links() }}
            </div>
        </div>
    </div>
</section>
