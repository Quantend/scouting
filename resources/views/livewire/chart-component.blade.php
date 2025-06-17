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
        <div class="flex items-center space-x-4">
            <label class="block font-medium text-gray-700 dark:text-gray-300 text-lg">
                Bereken Betaling Totaal
            </label>
            <flux:navlist.item class="max-w-10">
            <span class="text-2xl text-blue-500 cursor-pointer" wire:click="toggleInfo()">
                <flux:icon name="information-circle"/>
            </span>
            </flux:navlist.item>
        </div>

        <div class="flex space-x-2 mt-2">
            <!-- Vereist bedrag -->
            <div class="w-1/3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Totaal €</label>
                <input
                    type="number"
                    maxlength="6"
                    class="w-full p-2 border rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    wire:model="requiredMoney">
            </div>

            <!-- Schaalfactor -->
            <div class="w-1/4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Schaal</label>
                <input
                    type="number" step="0.1"
                    class="w-full p-2 border rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    wire:model="scalingFactor">
            </div>

            <!-- Basisbetaling -->
            <div class="w-1/4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Min. €</label>
                <input
                    type="number"
                    maxlength="6"
                    class="w-full p-2 border rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    wire:model="basePayment">
            </div>
        </div>

        <button wire:click="calculateChart()"
                class="cursor-pointer mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Bereken Grafiek
        </button>
    </div>


    @if($viewChart)
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-white mt-6">Betalingsgrafiek</h2>

        <!-- Bar Chart -->
        <div class="mt-4 space-y-4">
            @php
                // Zoek de persoon die het meeste moet betalen
                $maxPayment = $totalHoursMember->max('moneyToPay');
            @endphp
            @foreach($totalHoursMember as $data)
                <div class="flex items-center justify-between">
                    <span class="font-medium text-gray-700 dark:text-white min-w-20">{{ $data->name }}</span>
                    <div class="w-full bg-gray-200 dark:bg-gray-600 h-4 rounded-lg relative">
                        <div class="absolute top-0 left-0 h-2 bg-green-500 rounded-lg"
                             style="width: {{ $data->moneyToPay / $requiredMoney * 100 }}%"></div>
                        <div class="absolute top-2 left-0 h-2 bg-red-500 rounded-lg"
                             style="width: {{ ($data->moneyToPay / $maxPayment) * 100 }}%"></div>
                    </div>
                    <span class="w-20 text-gray-600 dark:text-gray-300 text-right min-w-20">{{ number_format($data->moneyToPay, 2) }}€</span>
                </div>
            @endforeach
        </div>
    @endif

    @if($showInfo)
        <div wire:click="toggleInfo()"
             class="fixed top-1 right-2 ml-2 bg-white shadow-lg p-4 rounded-lg border border-gray-300 dark:bg-gray-800 dark:border-gray-700 z-50">
            <p class="text-gray-700 dark:text-gray-300">
                De grafiek berekent hoeveel elk lid moet betalen op basis van het aantal gewerkte uren per lid en het
                totaal vereiste bedrag.
                <br><br>
                <strong>Totaal €:</strong> <br>
                Het totaalbedrag dat nodig is.<br>
                Dit is het bedrag dat over de leden wordt verdeeld op basis van hun gewerkte uren.
                <br><br>
                <strong>Schaal:</strong> <br>
                De schaalfactor bepaalt hoeveel invloed het aantal gewerkte uren heeft op het bedrag dat een lid moet
                betalen.<br>
                Hoe meer uren een lid heeft gewerkt, hoe minder dat lid hoeft te betalen.<br>
                Dit zorgt ervoor dat leden die meer bijdragen door werk, minder betalen.
                <br><br>
                <strong>Min. €:</strong> <br>
                Het minimaal te betalen bedrag voor elk lid. <br>
            </p>
            <button class="mt-2 text-blue-500 cursor-pointer" wire:click="toggleInfo()">Sluiten</button>
        </div>
    @endif
</div>
