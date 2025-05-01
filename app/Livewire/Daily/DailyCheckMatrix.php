<?php

namespace App\Livewire\Daily;

use App\Models\Area;
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
    public $remarkDate = '';
    public $search = '';

    public function mount()
    {
        $this->month = now()->month;
        $this->year  = now()->year;
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

    public function refreshData()
    {
        $this->loadDays();
        $this->loadExistingChecks();
    }

    public function loadDays()
    {
        $this->daysInMonth = range(1, Carbon::create($this->year, $this->month)->daysInMonth);
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
            $day                                       = Carbon::parse($check->check_date)->day;
            $this->checks[$check->check_item_id][$day] = $check->status;
            $this->remarks[$check->check_item_id]      = $check->remarks;
        }
    }

    public function toggleCheck($checkItemId, $day)
    {
        $date  = Carbon::create($this->year, $this->month, $day)->toDateString();
        $check = DailyCheck::firstOrNew([
            'check_item_id' => $checkItemId,
            'check_date'    => $date
        ]);

        // disable this for now for test purposes
        // $check->status     = $check->status === 'checked' ? 'not_checked' : 'checked';
        // $check->checked_by = auth()->id();
        // $check->checked_at = now();
        // $check->save();

        $this->checks[$checkItemId][$day] = $check->status;
    }

    public function changeTemp()
    {
          // This method used for inject onchange function.
    }

    public function openRemarkModal($checkItemId)
    {
        $this->selectedCheckItemId = $checkItemId;
        $this->selectedRemark      = '';
        $this->remarkDate          = '';
        $this->showRemarkModal     = true;
    }

    public function showItemDetails($checkItemId)
    {
        $checkItem = $this->checkItems->firstWhere('id', $checkItemId);

        if ($checkItem) {
            session()->flash('itemDetails', [
                'name'        => $checkItem->name,
                'description' => $checkItem->description,
                'area'        => $checkItem->area->name ?? 'N/A',
                'method'      => $checkItem->method->name ?? 'N/A',
            ]);
        }
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

        $day = Carbon::parse($this->remarkDate)->day;
        $this->remarks[$this->selectedCheckItemId][$day] = $this->selectedRemark;
        $this->showRemarkModal = false;

        session()->flash('success', 'Remark saved successfully.');
    }


    public function render()
    {
        return view('livewire.daily.daily-check-matrix');
    }

    public function updatingSearch()
    {
       
        $this->refreshData();
    }

}
