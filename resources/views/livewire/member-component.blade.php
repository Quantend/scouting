<div class="container p-6 bg-gray-100 dark:bg-gray-800 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold text-gray-700 dark:text-white">Ledenbeheer</h2>

    @if (session()->has('message'))
        <div class="bg-green-500 text-white p-2 rounded mt-4">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('messageError'))
        <div class="bg-red-500 text-white p-2 rounded mt-4">
            {{ session('messageError') }}
        </div>
    @endif

    <!-- Formulier voor het toevoegen van een nieuw lid -->
    <form wire:submit.prevent="store" class="mt-4">
        <div class="mb-4">
            <label class="font-medium text-gray-700 dark:text-gray-300">Naam:</label>
            <input type="text" wire:model="name"
                   class="border rounded p-2 w-full dark:bg-gray-700 dark:text-white dark:border-gray-600"
                   placeholder="Voer naam in">
            @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        <button type="submit"
                class="cursor-pointer bg-blue-500 text-white p-2 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Maak Nieuw Lid
        </button>
    </form>

    @if(!$viewTasks)
        <table class="w-full mt-4 border dark:bg-gray-700 dark:border-gray-600">
            <thead>
            <tr class="bg-gray-200 dark:bg-gray-800">
                <th class="p-2 text-gray-700 dark:text-white">Naam</th>
                <th class="p-2 text-gray-700 dark:text-white">Acties</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($members as $member)
                <tr class="border-t dark:border-gray-600">
                    <td class="p-2 text-center text-gray-700 dark:text-white">{{ $member->name }}</td>
                    <td class="p-2 flex justify-center space-x-2">
                        <button wire:click="edit({{ $member->id }})"
                                class="cursor-pointer bg-yellow-500 text-white p-1 rounded hover:bg-yellow-600">
                            <flux:icon.pencil-square></flux:icon.pencil-square>
                        </button>
                        <button wire:click="confirmDelete({{ $member->id }})"
                                class="cursor-pointer bg-red-500 text-white p-1 rounded hover:bg-red-600">
                            <flux:icon.trash></flux:icon.trash>
                        </button>
                        <button wire:click="viewTasksFunc({{ $member->id }})"
                                class="cursor-pointer bg-blue-500 text-white p-1 rounded hover:bg-blue-600">
                            <flux:icon.eye></flux:icon.eye>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    <!-- Deletion Confirmation Modal -->
    @if ($memberToDelete)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow-lg w-96">
                <h3 class="text-lg font-semibold mb-4">Weet je zeker dat je dit lid wilt verwijderen?</h3>
                <p class="mb-4">Deze actie kan niet ongedaan worden gemaakt.</p>
                <div class="flex justify-between">
                    <button wire:click="deleteConfirmed"
                            class="cursor-pointer bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        Verwijderen
                    </button>
                    <button wire:click="$set('memberToDelete', null)"
                            class="cursor-pointer bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Annuleren
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modale Bewerkingspopup -->
    @if ($isEdit)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow-lg w-96">
                <h3 class="text-lg font-semibold mb-4">Bewerk Lid</h3>

                <form wire:submit.prevent="update">
                    <div class="mb-4">
                        <label class="font-medium text-gray-700 dark:text-gray-300">Naam:</label>
                        <input type="text" wire:model="name"
                               class="border rounded p-2 w-full dark:bg-gray-700 dark:text-white dark:border-gray-600"
                               placeholder="Voer naam in">
                        @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-between">
                        <button type="submit"
                                class="cursor-pointer bg-blue-500 text-white p-2 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Bewerk Lid
                        </button>
                        <button type="button" wire:click="resetFields"
                                class="cursor-pointer bg-gray-500 text-white p-2 rounded hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Annuleren
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Weergave van kulssen en totaal aantal uren -->
    @if ($viewTasks)
        <div class="mt-4">
            <h3 class="mb-4 text-xl font-semibold text-gray-700 dark:text-white">Klussen en Totaal Uren
                voor {{$nameTasks}}</h3>
            <button wire:click="hideTasks()"
                    class="cursor-pointer bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Verberg Klussen
            </button>
            <table class="w-full mt-2 border dark:bg-gray-700 dark:border-gray-600">
                <thead>
                <tr class="bg-gray-200 dark:bg-gray-800">
                    <th class="p-2 text-gray-700 dark:text-white">Klus Titel</th>
                    <th class="p-2 text-gray-700 dark:text-white">Totaal Uren</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($tasksWithHours as $task)
                    <tr class="border-t dark:border-gray-600">
                        <td class="p-2 text-center text-gray-700 dark:text-white">{{ $task->title }}</td>
                        <td class="p-2 text-center text-gray-700 dark:text-white">{{ number_format($task->total_hours ,2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="p-2 text-center text-gray-700 dark:text-white">Geen klussen gevonden voor
                            dit lid.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
