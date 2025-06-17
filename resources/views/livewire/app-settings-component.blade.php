<div class="p-4 bg-white dark:bg-gray-800 rounded shadow text-center text-sm">
    <div>
        <button
            wire:click="confirmResetApp"
            wire:loading.attr="disabled"
            class="bg-red-600 text-white px-3 py-1.5 rounded hover:bg-red-700 transition"
        >
            Reset App
        </button>
        <button
            wire:click="sendDbEmail"
            wire:loading.attr="disabled"
            class="bg-green-500 text-white px-2 py-1.5 rounded hover:bg-green-600 transition ml-4"
        >
            Verstuur database
        </button>

        <div wire:loading class="text-sm text-gray-600 dark:text-gray-300 mt-2">
            Versturen bezig...
        </div>

        @if (session()->has('message'))
            <div class="mt-2 text-green-600 dark:text-green-400">
                {{ session('message') }}
            </div>
        @endif
    </div>

    @if ($resetAppConfirm)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4">
            <div class="bg-white p-4 rounded shadow w-full max-w-xs text-center">
                <div class="text-base font-semibold">Wat wil je verwijderen?</div>
                <div class="text-left space-y-2">
                    <div class="text-sm text-gray-600">
                        Voor meer opties selecteer eerst Logs of Uren
                    </div>
                    <label><input type="checkbox" wire:model.live.debounce="deleteLogs"> Logs</label><br>
                    <label><input type="checkbox" wire:model.live.debounce="deleteHours"> Uren</label><br>
                    @if($deleteHours)
                        <label><input type="checkbox" wire:model.live.debounce="deleteMembers"> Leden</label><br>
                        <label><input type="checkbox" wire:model.live.debounce="deleteTasks"> Klussen</label><br>
                    @endif
                    @if($deleteLogs)
                        <label><input type="checkbox" wire:model.live.debounce="deleteUsers"> Gebruikers (geen super
                            admins)</label>
                    @endif
                </div>
                <div class="mt-3 flex justify-center gap-2">
                    <button wire:click="confirmResetApp2" class="bg-red-600 text-white px-3 py-1 rounded text-sm">Ja
                    </button>
                    <button wire:click="resetInputFields" class="bg-gray-300 px-3 py-1 rounded text-sm">Annuleer
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($resetAppConfirm2)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4">
            <div class="bg-white p-4 rounded shadow w-full max-w-sm text-center space-y-4">
                <div class="mt-3 flex justify-center gap-2">
                    <button wire:click="resetApp" class="bg-red-700 text-white px-3 py-1 rounded text-sm">Ja,
                        verwijder
                    </button>
                    <button wire:click="resetInputFields" class="bg-gray-300 px-3 py-1 rounded text-sm">Nee</button>
                </div>
                <div class="text-base font-semibold">Weet je het zeker?</div>
                <div wire:loading class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                    Verwijderen bezig...
                </div>
            </div>
        </div>
    @endif
</div>
