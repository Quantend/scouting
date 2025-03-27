<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Hours;
use App\Models\Member;

class ChartComponent extends Component
{
    public $totalMembers;
    public $totalHoursMember;
    public $totalHoursAll;
    public $basisPoints;
    public $requiredMoney;
    public $totalRequiredMoney;
    public $viewChart = false;
    public $showInfo = false;
    public $scalingFactor = 1; // Default: 1 (Higher = Stronger penalty for low hours)
    public $basePayment = 50; // Minimum amount everyone pays

    public function mount()
    {
        $this->totalMembers = Member::count();
        $this->totalHoursAll = Hours::sum('hours');
        $this->requiredMoney = 1000;
        $this->totalRequiredMoney = $this->requiredMoney * $this->totalMembers;
        $this->calculateChart();
    }

    public function calculateChart()
    {
        // Ensure requiredMoney is set before calculation
        if (!$this->requiredMoney || $this->requiredMoney <= 0) {
            return;
        }

        // Get total hours per member and calculate basis points
        $this->totalHoursMember = Hours::join('members', 'hours.member_id', '=', 'members.id')
            ->selectRaw('members.name, SUM(hours.hours) as total_hours_member')
            ->groupBy('members.name')
            ->get()
            ->map(function ($member) {
                $member->basis_points = $this->totalHoursAll > 0
                    ? ($member->total_hours_member / $this->totalHoursAll)
                    : 0;
                return $member;
            });

        // Apply scaling factor to inverse basis points
        $this->totalHoursMember = $this->totalHoursMember->map(function ($member) {
            $member->adjusted_inverse = pow((1 - $member->basis_points), $this->scalingFactor);
            return $member;
        });

        // Calculate total adjusted inverse basis points
        $totalAdjustedInverse = $this->totalHoursMember->sum(fn($data) => $data->adjusted_inverse);

        // Step 1: Initial Payment Calculation
        $this->totalHoursMember = $this->totalHoursMember->map(function ($member) use ($totalAdjustedInverse) {
            $inverseRatio = $totalAdjustedInverse > 0 ? ($member->adjusted_inverse / $totalAdjustedInverse) : 0;
            $member->moneyToPay = $inverseRatio * $this->requiredMoney;
            return $member;
        });

        // Step 2: Enforce Minimum Payment and Adjust
        $totalShortage = 0; // Track extra money needed due to minimum payments
        $countAboveBase = 0; // Track how many members are still paying above the base

        $this->totalHoursMember = $this->totalHoursMember->map(function ($member) use (&$totalShortage, &$countAboveBase) {
            if ($member->moneyToPay < $this->basePayment) {
                $totalShortage += ($this->basePayment - $member->moneyToPay);
                $member->moneyToPay = $this->basePayment;
            } else {
                $countAboveBase++; // This person will contribute to covering the shortage
            }
            return $member;
        });

        // Step 3: Redistribute the Extra Cost Among Those Above the Base
        if ($totalShortage > 0 && $countAboveBase > 0) {
            $totalAboveBase = $this->totalHoursMember->sum(fn($m) => ($m->moneyToPay > $this->basePayment) ? $m->moneyToPay : 0);

            $this->totalHoursMember = $this->totalHoursMember->map(function ($member) use ($totalShortage, $totalAboveBase) {
                if ($member->moneyToPay > $this->basePayment) {
                    $adjustmentFactor = $totalAboveBase > 0 ? ($member->moneyToPay / $totalAboveBase) : 0;
                    $member->moneyToPay -= $adjustmentFactor * $totalShortage;
                }
                return $member;
            });
        }

        // Sort members by who pays the least to the most
        $this->totalHoursMember = $this->totalHoursMember->sortBy('moneyToPay')->values();

        $this->viewChart = true;
    }


    public function toggleInfo()
    {
        $this->showInfo = !$this->showInfo;
        $this->calculateChart();
    }

    public function render()
    {
        return view('livewire.chart-component', [
            'totalHoursMember' => $this->totalHoursMember,
            'totalHoursAll' => $this->totalHoursAll,
            'viewChart' => $this->viewChart
        ]);
    }
}
