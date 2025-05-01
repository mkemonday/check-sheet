<section class="w-full">
    @include('partials.setup-heading', [
        'title' => __('User Roles'),
        'description' => __('Assign roles to users'),
    ])

    <div class="mt-4">
        <table class="min-w-full border-collapse border border-gray-200">
            <thead>
                <tr>
                    <th class="border px-4 py-3 text-left">User</th>
                    <th class="border px-4 py-3 text-left">Roles</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td class="border px-4 py-2">
                            {{ $user->name }}<br>
                            <small class="text-gray-500">{{ $user->email }}</small>
                        </td>
                        <td class="border px-4 py-2">
                            <div class="flex flex-wrap gap-2">
                                @foreach ($roles as $role)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox"
                                               wire:model="selectedRoles.{{ $user->id }}"
                                               value="{{ $role->name }}"
                                               class="form-checkbox">
                                        <span>{{ $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
