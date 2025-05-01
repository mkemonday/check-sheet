<section class="w-full">
    @include('partials.setup-heading', [
        'title' => __('Daily Inspection Matrix'),
        'description' => __(
            'Manage your daily inspection matrix here. Select the month and year to view the matrix for that period. You can check or uncheck items as needed.'),
    ])
    <div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <flux:select label="Month" wire:model="month" id="month" wire:change="changeTemp">
                @foreach (range(1, 12) as $m)
                    <x-flux::select.option value="{{ $m }}" :selected="$m == $month">
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </x-flux::select.option>
                @endforeach
            </flux:select>

            <flux:select label="Year" wire:model="year" id="year" wire:change="changeTemp">
                @foreach (range(now()->year, now()->year + 5) as $y)
                    <x-flux::select.option value="{{ $y }}" :selected="$y == $year">
                        {{ $y }}
                    </x-flux::select.option>
                @endforeach
            </flux:select>
        </div>

        <!-- Matrix Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-200">
                <thead>
                    <tr>
                        <th class="border p-2">Area</th>
                        <th class="border p-2">Check Item</th>
                        <th class="border p-2">Method</th>
                        @foreach ($daysInMonth as $day)
                            <th class="border p-2">{{ $day }}</th>
                        @endforeach
                        <th class="border p-2">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($checkItems->groupBy('area') as $area => $items)
                        <tr>
                            <td class="border p-2" rowspan="{{ $items->count() }}">

                                @php
                                //  $url = URL::temporarySignedRoute(
                                //     'daily.daily-check', 
                                //     now()->addMinutes(30), // Expire after 5 seconds
                                //     ['id' => $items->first()->area->id]
                                // ); 

                                $url = route('daily.daily-check', ['id' => $items->first()->area->id]);


                                @endphp
                                <br>
                                <a href="{{ $url }}" class="font-bold hover:underline">
                                    {{ $items->first()->area->name }}
                                </a>
                            </td>
                            @foreach ($items as $index => $checkItem)
                                @if ($index > 0)
                        <tr>
                    @endif
                    <td class="border p-2">{{ $checkItem->name }}</td>
                    <td class="border p-2">{{ $checkItem->method->name }}</td>
                    @foreach ($daysInMonth as $day)
                    @php
                    $status = $checks[$checkItem->id][$day] ?? null;
                
                    $icon = match($status) {
                        'checked' => '✅',
                        'not_checked' => '❌',
                        default => '⬜',
                    };
                
                    $color = match($status) {
                        'checked' => 'text-green-600',
                        'not_checked' => 'text-red-600',
                        default => 'text-gray-400',
                    };
                    @endphp
                    
                    <td class="border text-center">
                        <button
                            wire:click="toggleCheck({{ $checkItem->id }}, {{ $day }})"
                            class="text-sm focus:outline-none"
                            style="color: {{ $status === 'checked' ? '#16a34a' : ($status === 'not_checked' ? '#dc2626' : '#9ca3af') }}">
                            {{ $icon }}
                        </button>
                    </td>
                
                    @endforeach
                    <td class="border p-2 text-center">
                        <flux:button icon="layout-grid" wire:click="openRemarkModal({{ $checkItem->id }})"
                            size="xs">
                        </flux:button>
                    </td>
                    </tr>
                    @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Remarks Section -->
        {{-- <div class="mt-4">
            <h3>Remarks</h3>
            <textarea wire:model="remarks" class="w-full border p-4 rounded" rows="4" placeholder="Enter remarks here..."></textarea>
        </div> --}}

        <flux:modal wire:model.defer="showRemarkModal" class="md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Edit Remark</flux:heading>
                    <flux:text class="mt-2">Make changes to your inspection details.</flux:text>
                </div>
                <flux:textarea label="Remarks" placeholder="Enter your remark..." wire:model.defer="selectedRemark" />
                <flux:input label="Remark date" type="date" wire:model.defer="remarkDate"
                    :value="now()->toDateString()" />
                <div class="flex gap-2 justify-end">
                    <flux:spacer />
                    <flux:button wire:click="saveRemark" color="primary">
                        Save
                    </flux:button>
                    <flux:button wire:click="$set('showRemarkModal', false)" color="secondary">
                        Cancel
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</section>
