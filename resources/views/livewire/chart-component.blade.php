<div class="container p-6 bg-gray-100 dark:bg-gray-800 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold text-gray-700 dark:text-white">Totaal Aantal Uren</h2>
    <p class="text-lg font-medium text-gray-600 dark:text-gray-300">{{ number_format($totalHoursAll, 2) }} uren</p>

    <h2 class="text-2xl font-semibold text-gray-700 dark:text-white mt-6">Totaal Aantal Uren Per Lid</h2>
    <ul class="list-none p-0 mt-4">
        @foreach($totalHoursMember as $data)
            <li class="flex justify-between py-2 border-b border-gray-300 dark:border-gray-600">
                <span class="font-medium text-gray-700 dark:text-white">{{ $data->name }}</span>
                <span class="text-gray-600 dark:text-gray-300">{{ number_format($data->total_hours_member, 2) }} uren ({{ number_format($data->basis_points * 100, 2) }}%)</span>
            </li>
        @endforeach
    </ul>

    <div class="mt-6">
        <label class="block font-medium text-gray-700 dark:text-gray-300">Bereken Betaling Totaal</label>
        <input class="w-full mt-2 p-3 border rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500" wire:model="requiredMoney" placeholder="Vul het vereiste bedrag in">
        <button wire:click="calculateChart()" class="cursor-pointer mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Bereken Grafiek
        </button>
    </div>

    @if($viewChart)
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-white mt-6">Betalingsgrafiek</h2>

        <!-- Bar Chart -->
        <div class="mt-4 space-y-4">
            @foreach($totalHoursMember as $data)
                <div class="flex items-center justify-between">
                    <span class="font-medium text-gray-700 dark:text-white w-20">{{ $data->name }}</span>
                    <div class="w-full bg-gray-200 dark:bg-gray-600 h-4 rounded-lg relative">
                        <div class="absolute top-0 left-0 h-4 bg-green-500 rounded-lg" style="width: {{ $data->moneyToPay / $requiredMoney * 100 }}%"></div>
                    </div>
                    <span class="w-20 text-gray-600 dark:text-gray-300 text-right">{{ number_format($data->moneyToPay, 2) }}€</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
