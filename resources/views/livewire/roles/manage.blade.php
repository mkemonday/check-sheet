<section class="w-full">
    @include('partials.setup-heading', [
        'title' => __('Roles'),
        'description' => __('Manage user roles and assign permissions.'),
    ])

    <div>
        <form wire:submit.prevent="save" class="space-y-4">
            <flux:input wire:model="name" placeholder="Role name" label="Role Name" />

            <div>
                <label class="text-sm font-medium text-gray-700">Permissions</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mt-2">
                    @foreach($allPermissions as $perm)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" wire:model="permissions" value="{{ $perm->name }}" class="form-checkbox">
                            <span>{{ $perm->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex space-x-2">
                <flux:button type="submit" variant="primary" color="indigo">
                    Save
                </flux:button>
                <flux:button type="button" variant="danger" color="gray" wire:click="cancel">
                    Cancel
                </flux:button>
            </div>
        </form>

        <div class="overflow-x-auto mt-6">
            <div class="mb-4 px-1">
                <flux:input wire:model.live.debounce.1000ms="search" placeholder="Search roles..." label="Search" icon="magnifying-glass" />
            </div>
            <table class="min-w-full border-collapse border border-gray-200">
                <thead>
                    <tr>
                        <th class="border px-4 py-3 text-left">Role Name</th>
                        <th class="border px-4 py-3 text-left">Permissions</th>
                        <th class="border px-4 py-3 text-center w-1/12">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td class="border px-4 py-1 whitespace-nowrap">{{ $role->name }}</td>
                            <td class="border px-4 py-1 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($role->permissions as $perm)
                                        <flux:badge size="sm">{{ $perm->name }}</flux:badge>
                                    @endforeach
                                </div>
                            </td>
                            <td class="border px-4 py-1 whitespace-nowrap text-center">
                                <div class="space-x-2">
                                    <flux:button variant="primary" wire:click="edit({{ $role->id }})" color="indigo" icon="pencil-square" class="text-sm" />
                                    <flux:button variant="danger" wire:click="delete({{ $role->id }})" color="red" icon="trash" class="text-sm" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
</section>
