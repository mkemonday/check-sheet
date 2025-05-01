<section class="w-full">
    @include('partials.setup-heading', [
        'title' => __('Users'),
        'description' => __('Manage users and assign roles'),
    ])

    <form wire:submit.prevent="save" class="space-y-4">
        <flux:input wire:model="name" placeholder="Full name" label="Name" />
        <flux:input wire:model="email" type="email" placeholder="Email address" label="Email" />
        <flux:input wire:model="password" type="password" placeholder="Set password" label="Password" />

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Roles</label>
            <div class="flex flex-wrap gap-3">
                @foreach ($allRoles as $role)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox"
                               wire:model="roles"
                               value="{{ $role }}"
                               class="form-checkbox rounded text-indigo-600">
                        <span>{{ $role }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex space-x-2">
            <flux:button type="submit" variant="primary" color="indigo">Save</flux:button>
            <flux:button type="button" variant="danger" color="gray" wire:click="cancel">Cancel</flux:button>
        </div>
    </form>

    <div class="overflow-x-auto mt-6">
        <div class="mb-4 px-1">
            <flux:input wire:model.live.debounce.1000ms="search" placeholder="Search users..." label="Search" icon="magnifying-glass" />
        </div>

        <table class="min-w-full border-collapse border border-gray-200">
            <thead>
                <tr>
                    <th class="border px-4 py-3 text-left">Name</th>
                    <th class="border px-4 py-3 text-left">Email</th>
                    <th class="border px-4 py-3 text-left">Roles</th>
                    <th class="border px-4 py-3 text-center w-1/12">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td class="border px-4 py-2">{{ $user->name }}</td>
                        <td class="border px-4 py-2">{{ $user->email }}</td>
                        <td class="border px-4 py-2">
                            @foreach ($user->roles as $role)
                                <flux:badge size="sm">{{ $role->name }}</flux:badge>
                            @endforeach
                        </td>
                        <td class="border px-4 py-2 text-center">
                            <div class="space-x-2">
                                <flux:button variant="primary" wire:click="edit({{ $user->id }})" icon="pencil-square" color="indigo" class="text-sm" />
                                <flux:button variant="danger" wire:click="delete({{ $user->id }})" icon="trash" color="red" class="text-sm" />
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</section>
