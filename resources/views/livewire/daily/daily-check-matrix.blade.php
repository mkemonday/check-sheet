<section class="w-full">
    @include('partials.setup-heading', [
        'title' => __('Daily Inspection Matrix'),
        'description' => __('Manage your daily inspection matrix here. Select the month and year to view the matrix for that period.'),
    ])

    <div>
        <!-- Tabs -->
        <div class="flex flex-column gap-2 mb-4">
            @if ($isCurrentMonth)
                <flux:button 
                    wire:click="$set('viewMode', 'day')" 
                    :variant="$viewMode === 'day' ? 'primary' : 'outline'">
                    Daily
                </flux:button>

                <flux:button 
                    wire:click="$set('viewMode', 'week')" 
                    :variant="$viewMode === 'week' ? 'primary' : 'outline'">
                    Weekly
                </flux:button>
            @endif

            <flux:button wire:click="$set('viewMode', 'month')" :variant="$viewMode === 'month' ? 'primary' : 'outline'">Monthly</flux:button>
            <flux:select  wire:model.lazy="month" id="month">
                @foreach (range(1, 12) as $m)
                    <x-flux::select.option value="{{ $m }}" :selected="$m == $month">
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </x-flux::select.option>
                @endforeach
            </flux:select>

            <flux:select  wire:model.lazy="year" id="year">
                @foreach (range(now()->year, now()->year + 5) as $y)
                    <x-flux::select.option value="{{ $y }}" :selected="$y == $year">{{ $y }}</x-flux::select.option>
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
                            <th class="border p-2 {{ $day == now()->day ? 'bg-green-900' : '' }}">
                                {{ $day }}
                            </th>
                        @endforeach
                        <th class="border p-2">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($checkItems->groupBy('area') as $area => $items)
                        <tr>
                            <td class="border p-2" rowspan="{{ $items->count() }}">
                                <a href="{{ route('daily.daily-check', ['id' => $items->first()->area->id]) }}" class="font-bold hover:underline">
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
                                                'no_problem' => '<span style="color: #16a34a;">🟢</span>',
                                                'minor_problem' => '<span style="color: #facc15;">▲</span>',
                                                'major_problem' => '<span style="color: #dc2626;">❌</span>',
                                                default => '<span style="color: #9ca3af;">⬜</span>',
                                            };
                                            $color = match($status) {
                                                'no_problem' => '#16a34a',
                                                'minor_problem' => '#facc15',
                                                'major_problem' => '#dc2626',
                                                default => '#9ca3af',
                                            };
                                        @endphp
                                        <td class="border text-center">
                                            <button wire:click="toggleCheck({{ $checkItem->id }}, {{ $day }})"
                                                class="text-sm focus:outline-none" style="color: {{ $color }}">
                                                {!! $icon !!}
                                            </button>
                                        </td>
                                    @endforeach

                                    <td class="border p-2 text-center">
                                        <flux:button icon="layout-grid" wire:click="openRemarkModal({{ $checkItem->id }})" size="xs" />
                                    </td>
                                </tr>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal for remarks -->
        <flux:modal wire:model.defer="showRemarkModal" class="md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Edit Remark</flux:heading>
                    <flux:text class="mt-2">Make changes to your inspection details.</flux:text>
                </div>
                <flux:textarea label="Remarks" placeholder="Enter your remark..." wire:model.defer="selectedRemark" />
                <flux:input label="Remark date" type="date" wire:model.defer="remarkDate" />
                <div class="flex gap-2 justify-end">
                    <flux:spacer />
                    <flux:button wire:click="saveRemark" color="primary">Save</flux:button>
                    <flux:button wire:click="$set('showRemarkModal', false)" color="secondary">Cancel</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</section>
