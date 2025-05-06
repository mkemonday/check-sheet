<?php

namespace App\Livewire\Dashboard;

use App\Models\Area;
use App\Models\DailyCheck;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Log;
use Livewire\Component;

class MachineInspectionReport extends Component
{
    public $data        = [];
    public $month;
    public $year;
    public $viewMode       = 'month';
    public $isCurrentMonth = false;

    public function mount()
    {
        $this->month = now()->month;
        $this->year  = now()->year;
        $this->viewMode = 'month';
        $this->refreshData();
    }

    public function updatedMonth()
    {
        $this->viewMode = 'month';
        $this->refreshData();
    }

    public function updatedYear()
    {
        $this->viewMode = 'month';
        $this->refreshData();
    }

    public function updatedViewMode()
    {
        $this->refreshData();
    }


    public function refreshData()
    {
        $this->isCurrentMonth = $this->month == now()->month && $this->year == now()->year;

        // dump($this->viewMode);
        // dump(now()->day);

        \Log::info('Current Month: ' . now()->month);

        // Define the base date from selected month/year
        $baseDate = Carbon::createFromDate($this->year, $this->month, now()->day);

        $areas = Area::orderBy('id')->get();

        $this->data = $areas->map(function ($area) use ($baseDate) {
            $checkItems = $area->checkItems()->pluck('id');

            $query = DailyCheck::whereIn('check_item_id', $checkItems);

            if ($this->viewMode === 'day') {
                // Use the first day of the selected month for "day" view
                $query->whereDate('check_date', $baseDate->copy()->startOfDay());
            } elseif ($this->viewMode === 'week') {
                // Use the week of the selected month/year
                $startOfWeek = $baseDate->copy()->startOfWeek();
                $endOfWeek = $baseDate->copy()->endOfWeek();
                $query->whereBetween('check_date', [$startOfWeek, $endOfWeek]);
            } else {
                // Default to monthly view
                $query->whereMonth('check_date', $this->month)
                    ->whereYear('check_date', $this->year);
            }

            return [
                'name'  => $area->name,
                'ok'    => (clone $query)->where('status', 'no_problem')->count(),
                'minor' => (clone $query)->where('status', 'minor_problem')->count(),
                'major' => (clone $query)->where('status', 'major_problem')->count(),
            ];
        })->toArray();

         $this->dispatch('refresh-chart', data: $this->data);
    }


    public function render()
    {
        return view('livewire.dashboard.machine-inspection-report');
    }
}
