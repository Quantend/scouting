<div class="container p-6 bg-gray-100 dark:bg-gray-800 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold text-gray-700 dark:text-white">Bekijk Geregistreerde Uren</h2>

    @if (session()->has('message'))
        <div class="bg-green-500 text-white p-2 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="my-4">
        <label for="memberSelect" class="block font-medium text-gray-700 dark:text-gray-300">Selecteer Lid:</label>
        <select wire:model="selectedMember" id="memberSelect"
                class="border rounded p-2 w-full dark:bg-gray-700 dark:text-white dark:border-gray-600">
            <option value="">Alle Leden</option>
            @foreach ($members as $member)
                <option value="{{ $member->id }}">{{ $member->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="taskSelect" class="block font-medium text-gray-700 dark:text-gray-300">Selecteer Klus:</label>
        <select wire:model="selectedTask" id="taskSelect"
                class="border rounded p-2 w-full dark:bg-gray-700 dark:text-white dark:border-gray-600">
            <option value="">Alle Taken</option>
            @foreach ($tasks as $task)
                <option value="{{ $task->id }}">{{ $task->title }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="dateSelect" class="block font-medium text-gray-700 dark:text-gray-300">Selecteer Datum:</label>
        <input type="date" wire:model="selectedDate" id="dateSelect"
                class="border rounded p-2 w-full dark:bg-gray-700 dark:text-white dark:border-gray-600">
    </div>

    <div class="mb-4 space-x-2">
        <button wire:click="filterHours"
                class="cursor-pointer bg-blue-500 text-white p-2 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Filter
        </button>
        <button wire:click="resetFilters"
                class="cursor-pointer bg-blue-500 text-white p-2 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Reset Filter
        </button>
    </div>

    @if($hours->isEmpty())
        <p class="text-center mt-4 text-gray-600 dark:text-gray-300">Geen uren gevonden voor de geselecteerde
            filters.</p>
    @else
        <div class="">
            {{ $hours->links() }}
        </div>
        <div class="w-full overflow-x-scroll">
            <table class="w-full mt-4 border dark:bg-gray-700 dark:border-gray-600">
                <thead>
                <tr class="bg-gray-200 dark:bg-gray-800">
                    <th class="p-2 text-gray-700 dark:text-white">Datum</th>
                    <th class="p-2 text-gray-700 dark:text-white">Lid</th>
                    <th class="p-2 text-gray-700 dark:text-white">Klus</th>
                    <th class="p-2 text-gray-700 dark:text-white">Uren</th>
                    <th class="p-2 text-gray-700 dark:text-white">Acties</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($hours as $hour)
                    <tr class="border-t dark:border-gray-600">
                        <td class="p-2 text-center text-gray-700 dark:text-white">
                            {{ \Carbon\Carbon::parse($hour->date)->format('d-m-Y') }}
                        </td>
                        <td class="p-2 text-center text-gray-700 dark:text-white">{{ optional($hour->member)->name ?? 'Geen Lid' }}</td>
                        <td class="p-2 text-center text-gray-700 dark:text-white">{{ optional($hour->task)->title ?? 'Geen Klus' }}</td>
                        <td class="p-2 text-center text-gray-700 dark:text-white">{{ number_format($hour->hours, 2) }}</td>
                        <td class="p-2 text-center">
                            <button wire:click="editHour({{ $hour->id }})"
                                    class="cursor-pointer bg-yellow-500 text-white p-1 rounded hover:bg-yellow-600">
                                <flux:icon.pencil-square></flux:icon.pencil-square>
                            </button>
                            <button wire:click="confirmDelete({{ $hour->id }})"
                                    class="cursor-pointer bg-red-500 text-white p-1 rounded hover:bg-red-600">
                                <flux:icon.trash></flux:icon.trash>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $hours->links() }}
        </div>
    @endif

    <!-- Bewerken Formulier Modaal -->
    @if($editHourId)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow-lg w-96 dark:bg-gray-700 dark:text-white">
                <h3 class="text-lg font-semibold mb-4">Bewerk Uur</h3>

                <form wire:submit.prevent="updateHour">
                    <div class="mb-4">
                        <label for="editMemberSelect"
                               class="block font-medium text-gray-700 dark:text-gray-300">Lid</label>
                        <select wire:model="editMemberId" id="editMemberSelect"
                                class="border rounded p-2 w-full dark:bg-gray-600 dark:text-white dark:border-gray-500">
                            <option value="">Selecteer Lid</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="editTaskSelect"
                               class="block font-medium text-gray-700 dark:text-gray-300">Klus</label>
                        <select wire:model="editTaskId" id="editTaskSelect"
                                class="border rounded p-2 w-full dark:bg-gray-600 dark:text-white dark:border-gray-500">
                            <option value="">Selecteer Klus</option>
                            @foreach ($tasks as $task)
                                <option value="{{ $task->id }}">{{ $task->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="editHours" class="block font-medium text-gray-700 dark:text-gray-300">Uren
                            (hh:mm)</label>
                        <input type="time" wire:model="hoursInput"
                               class="border rounded p-2 w-full dark:bg-gray-600 dark:text-white dark:border-gray-500"
                               required>
                    </div>
                    <div class="mb-4">
                        <label for="editDate" class="block font-medium text-gray-700 dark:text-gray-300">Datum</label>
                        <input type="date" wire:model="editDate" id="editDate"
                               class="border rounded p-2 w-full dark:bg-gray-600 dark:text-white dark:border-gray-500"
                               required>
                    </div>

                    <div class="flex justify-between">
                        <button type="submit"
                                class="cursor-pointer bg-blue-500 text-white p-2 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Bijwerken
                        </button>
                        <button type="button" wire:click="resetEditFields"
                                class="cursor-pointer bg-gray-500 text-white p-2 rounded hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Annuleren
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Deletion Confirmation Modal -->
    @if ($hourToDelete)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow-lg w-96">
                <h3 class="text-lg font-semibold mb-4">Weet je zeker dat je dit wilt verwijderen?</h3>
                <p class="mb-4">Deze actie kan niet ongedaan worden gemaakt.</p>
                <div class="flex justify-between">
                    <button wire:click="deleteConfirmed" class="cursor-pointer bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        Verwijderen
                    </button>
                    <button wire:click="$set('hourToDelete', null)" class="cursor-pointer bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Annuleren
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
