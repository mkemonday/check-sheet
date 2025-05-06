<section class="w-full p-6 space-y-6">
    <flux:heading size="xl">Machine Inspection Problem Status</flux:heading>

    <!-- Filters -->
    <div class="flex flex-column gap-2 mb-4">
        @if ($isCurrentMonth)
            <flux:button wire:click="$set('viewMode', 'day')" :variant="$viewMode === 'day' ? 'primary' : 'outline'">
                Daily</flux:button>
            <flux:button wire:click="$set('viewMode', 'week')"
                :variant="$viewMode === 'week' ? 'primary' : 'outline'">Weekly</flux:button>
        @endif
        <flux:button wire:click="$set('viewMode', 'month')" :variant="$viewMode === 'month' ? 'primary' : 'outline'">
            Monthly</flux:button>

        <flux:select wire:model.lazy="month" id="month">
            @foreach (range(1, 12) as $m)
                <x-flux::select.option value="{{ $m }}">
                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                </x-flux::select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.lazy="year" id="year">
            @foreach (range(now()->year - 2, now()->year + 3) as $y)
                <x-flux::select.option value="{{ $y }}">{{ $y }}</x-flux::select.option>
            @endforeach
        </flux:select>
    </div>

    <!-- Bar Chart -->
    <div id="bar-chart" wire:ignore wire:key="chart-container"></div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 text-sm">
            <thead>
                <tr>
                    <th class="border p-2">No</th>
                    <th class="border p-2">Machine Name</th>
                    <th class="border p-2">🟢 No problem</th>
                    <th class="border p-2">🔺 Minor problem</th>
                    <th class="border p-2">❌ Major problem</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                    <tr>
                        <td class="border p-2 text-center">{{ $index + 1 }}</td>
                        <td class="border p-2">{{ $item['name'] }}</td>
                        <td class="border p-2 text-center">{{ $item['ok'] }}</td>
                        <td class="border p-2 text-center">{{ $item['minor'] }}</td>
                        <td class="border p-2 text-center">{{ $item['major'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@push('scripts')
<script>
    let chartInstance = null;

    Livewire.on('refresh-chart', (e) => {
        console.log('refresh-chart:', e.data);
        renderChart(e.data);
    });

    function renderChart(chartData) {
        const chartElement = document.querySelector("#bar-chart");

        if (!chartElement) {
            console.warn("Chart container not found.");
            return;
        }

        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }

        const categories = chartData.map(item => item.name);
        const series = [
            {
                name: 'No Problem',
                data: chartData.map(item => item.ok),
                color: '#22c55e'
            },
            {
                name: 'Minor Problem',
                data: chartData.map(item => item.minor),
                color: '#facc15'
            },
            {
                name: 'Major Problem',
                data: chartData.map(item => item.major),
                color: '#ef4444'
            }
        ];

        const options = {
            chart: {
                type: 'bar',
                height: 500
            },
            series: series,
            xaxis: {
                categories: categories
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '60%',
                }
            },
            dataLabels: {
                enabled: true
            },
            theme: {
                mode: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'dark'
            }
        };

        chartInstance = new ApexCharts(chartElement, options);

        chartInstance.render()
            .then(() => console.log('Chart rendered successfully.'))
            .catch(err => console.error('Chart render error:', err));
    }
</script>
@endpush

