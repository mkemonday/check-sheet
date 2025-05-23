<?php

namespace App\Livewire\Daily;

use App\Models\CheckItem;
use App\Models\DailyCheck;
use Livewire\Component;
use Illuminate\Support\Carbon;

class DailyCheckMatrix extends Component
{
    public $month;
    public $year;
    public $daysInMonth = [];
    public $checkItems;
    public $checks          = [];
    public $remarks         = [];
    public $showRemarkModal = false;
    public $selectedCheckItemId;
    public $selectedRemark = '';
    public $remarkDate     = '';
    public $isCurrentMonth = false;
    public $viewMode       = 'month';  // 'day', 'week', 'month'


    public function mount()
    {
        $this->month    = now()->month;
        $this->year     = now()->year;
        $this->viewMode = 'month';
        $this->updateIsCurrentMonth();

        $this->loadDays();
        $this->loadCheckItems();
        $this->loadExistingChecks();
    }

    public function updatedMonth()
    {
        $this->refreshData();
    }

    public function updatedYear()
    {
        $this->refreshData();
    }
    
    public function updatedViewMode()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->updateIsCurrentMonth();
        $this->loadDays();
        $this->loadExistingChecks();
    }

    public function updateIsCurrentMonth()
    {
        $now                  = now();
        $this->isCurrentMonth = ($this->month == $now->month && $this->year == $now->year);
    }

    public function loadDays()
    {
        if ($this->viewMode === 'day') {
            $this->daysInMonth = [now()->day];
        } elseif ($this->viewMode === 'week') {
            $start = Carbon::now()->startOfWeek();
            $end   = Carbon::now()->endOfWeek();
            $days  = [];

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                if ($date->month == $this->month && $date->year == $this->year) {
                    $days[] = $date->day;
                }
            }

            $this->daysInMonth = $days;
        } else {
            $this->daysInMonth = range(1, Carbon::create($this->year, $this->month)->daysInMonth);
        }
    }

    public function loadCheckItems()
    {
        $this->checkItems = CheckItem::with(['area', 'method'])->get();
    }

    public function loadExistingChecks()
    {
        $existing = DailyCheck::whereMonth('check_date', $this->month)
            ->whereYear('check_date', $this->year)
            ->get();

        $this->checks  = [];
        $this->remarks = [];

        foreach ($existing as $check) {
            $day                                        = Carbon::parse($check->check_date)->day;
            $this->checks[$check->check_item_id][$day]  = $check->status;
            $this->remarks[$check->check_item_id][$day] = $check->remarks;
        }
    }

    public function toggleCheck($checkItemId, $day)
    {
        $date = Carbon::create($this->year, $this->month, $day)->toDateString();

        $check = DailyCheck::firstOrNew([
            'check_item_id' => $checkItemId,
            'check_date'    => $date,
        ]);

          // Cycle status for demo (can be customized)
        $current    = $check->status;
        $nextStatus = match ($current) {
            null            => 'no_problem',
            'no_problem'    => 'minor_problem',
            'minor_problem' => 'major_problem',
            default         => null,
        };

        $check->status = $nextStatus;
        $check->save();

        $this->checks[$checkItemId][$day] = $check->status;
    }

    public function openRemarkModal($checkItemId)
    {
        $this->selectedCheckItemId = $checkItemId;
        $this->selectedRemark      = '';
        $this->remarkDate          = now()->toDateString();
        $this->showRemarkModal     = true;
    }

    public function saveRemark()
    {
        $this->validate([
            'selectedCheckItemId' => 'required|integer',
            'remarkDate'          => 'required|date',
            'selectedRemark'      => 'required|string|max:255',
        ]);

        $check = DailyCheck::firstOrNew([
            'check_item_id' => $this->selectedCheckItemId,
            'check_date'    => $this->remarkDate,
        ]);

        $check->remarks = $this->selectedRemark;
        $check->save();

        $day                                             = Carbon::parse($this->remarkDate)->day;
        $this->remarks[$this->selectedCheckItemId][$day] = $this->selectedRemark;

        $this->showRemarkModal = false;
        session()->flash('success', 'Remark saved successfully.');
    }

    public function render()
    {
        return view('livewire.daily.daily-check-matrix');
    }
}
