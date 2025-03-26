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
                // Calculate basis points
                $member->basis_points = $this->totalHoursAll > 0
                    ? ($member->total_hours_member / $this->totalHoursAll)
                    : 0;

                return $member;
            });

        // Calculate the total of all inverse basis points (to scale them properly)
        $totalInverseBasisPoints = $this->totalHoursMember->sum(fn($data) => (1 - $data->basis_points));

        // Now, calculate how much each member should pay based on the inverse of their basis points
        foreach ($this->totalHoursMember as $data) {
            $data->moneyToPay = ($totalInverseBasisPoints > 0)
                ? ((1 - $data->basis_points) / $totalInverseBasisPoints) * $this->requiredMoney
                : 0;
        }

        $this->viewChart = true;
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
