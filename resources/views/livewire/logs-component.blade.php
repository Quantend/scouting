<div>
    <div class="p-4">
        {{-- Filters --}}
        <div class="flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0 mb-4">
            <div class="flex flex-col">
                <label for="type">Type:</label>
                <select wire:model.defer="filters.type" id="type" class="border p-2 rounded">
                    <option value="">All</option>
                    @foreach($types as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col">
                <label for="user">User:</label>
                <select wire:model.defer="filters.user" id="user" class="border p-2 rounded">
                    <option value="">All</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col">
                <label for="date">Date:</label>
                <input type="date" wire:model.defer="filters.date" id="date" class="border p-2 rounded">
            </div>

            <div class="flex md:items-end gap-2">
                <button wire:click="applyFilters"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 w-full md:w-auto">
                    Apply
                </button>
                <button wire:click="resetFilters"
                        class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400 w-full md:w-auto">
                    Reset
                </button>
            </div>
        </div>

        {{-- Session Message --}}
        @if (session()->has('message'))
            <div class="mb-4 text-green-600 font-semibold">
                {{ session('message') }}
            </div>
        @endif

        {{-- Clear Logs Button --}}
        <div class="flex justify-center mb-4">
            <button wire:click="toggleClearLogs"
                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 w-full md:w-auto">
                Clear All Logs
            </button>
        </div>

        {{-- Logs Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse border border-gray-300 ">
                <thead class="bg-gray-100 dark:bg-gray-600 dark:border-gray-500">
                <tr>
                    <th class="border px-4 py-2 text-left">User</th>
                    <th class="border px-4 py-2 text-left">Type</th>
                    <th class="border px-4 py-2 text-left">Log</th>
                    <th class="border px-4 py-2 text-left">Date</th>
                </tr>
                </thead>
                <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="border px-4 py-2">{{ $log->user->name ?? 'Unknown' }}</td>
                        <td class="border px-4 py-2">{{ $log->type }}</td>
                        <td class="border px-4 py-2">{{ $log->log }}</td>
                        <td class="border px-4 py-2">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">No logs found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $logs->links() }}
        </div>

        @if ($confirmClearLogs)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded shadow-lg w-96 dark:bg-gray-600 dark:border-gray-500">
                    <h3 class="text-lg font-semibold mb-4">Weet je zeker dat je alle logs wilt verwijderen?</h3>
                    <p class="mb-4">Deze actie kan niet ongedaan worden gemaakt.</p>
                    <div wire:loading wire:target="clearAllLogs" class="font-semibold text-blue-500 text-center mb-2">
                        Sending database and deleting logs...
                    </div>
                    <div class="flex justify-between">
                        <button wire:click="clearAllLogs"
                                class="cursor-pointer bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                            Verwijderen
                        </button>
                        <button wire:click="toggleClearLogs"
                                class="cursor-pointer bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Annuleren
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
