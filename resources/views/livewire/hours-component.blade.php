<div class="container p-6 bg-gray-100 dark:bg-gray-800 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold text-gray-700 dark:text-white">Uren Registreren</h2>

    @if (session()->has('message'))
        <div class="bg-green-500 text-white p-2 rounded mt-4">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="store" class="mt-4">
        <div class="mb-4">
            <label class="font-medium text-gray-700 dark:text-gray-300">Lid:</label>
            <select wire:model="member_id" class="border rounded p-2 w-full dark:bg-gray-700 dark:text-white dark:border-gray-600">
                <option value="">Selecteer Lid</option>
                @foreach ($members as $member)
                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                @endforeach
            </select>
            @error('member_id') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="font-medium text-gray-700 dark:text-gray-300">Taak:</label>
            <select wire:model="task_id" class="border rounded p-2 w-full dark:bg-gray-700 dark:text-white dark:border-gray-600">
                <option value="">Selecteer Taak</option>
                @foreach ($tasks as $task)
                    <option value="{{ $task->id }}">{{ $task->title }}</option>
                @endforeach
            </select>
            @error('task_id') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="font-medium text-gray-700 dark:text-gray-300">Uren: (hh:mm)</label>
            <input type="time" wire:model="hoursInput" class="border rounded p-2 w-full dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="Voer uren in (bijv. 01:30)">
            @error('hoursInput') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="font-medium text-gray-700 dark:text-gray-300">Datum:</label>
            <input type="date" wire:model="date" class="border rounded p-2 w-full dark:bg-gray-700 dark:text-white dark:border-gray-600">
            @error('date') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="cursor-pointer bg-blue-500 text-white p-2 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Registreer Uren
        </button>
    </form>
</div>

