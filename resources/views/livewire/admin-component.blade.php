<div class="container mx-auto p-6 bg-gray-100 dark:bg-gray-800 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-4 text-gray-700 dark:text-white">Gebruikersbeheer</h2>

    @if (session()->has('message'))
        <div class="bg-green-500 text-white p-4 mb-4 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-500 text-white p-4 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Zoekbox met verbeterde opmaak -->
    <div class="mb-4 flex items-center space-x-4">
        <!-- Zoekveld -->
        <input
            type="text"
            wire:model.debounce.500ms="searchEmail"
            placeholder="Zoeken op e-mail"
            class="border p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600"
        />

        <!-- Wis knop -->
        @if($searchEmail)
            <button
                wire:click="$set('searchEmail', '')"
                class="cursor-pointer text-gray-500 hover:text-gray-800 focus:outline-none"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endif

        <!-- Zoekknop -->
        <button
            wire:click="updateUserList"
            class="cursor-pointer bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600 focus:outline-none"
        >
        <span class="flex items-center space-x-2">
            <span>Zoeken</span>
        </span>
        </button>
    </div>

    <div class="flex justify-center mb-4">
        <button
            wire:click="confirmDeleteAll"
            class="cursor-pointer bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-lg hover:bg-red-700 transition duration-300 ease-in-out">
            Verwijder alle niet-beheerders
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-300 hidden sm:table dark:bg-gray-700 dark:border-gray-600">
            <thead>
            <tr class="bg-gray-200 dark:bg-gray-800">
                <th class="border p-2 text-center text-gray-700 dark:text-white">Naam</th>
                <th class="border p-2 text-center text-gray-700 dark:text-white">E-mail</th>
                <th class="border p-2 text-center text-gray-700 dark:text-white">Beheerder</th>
                <th class="border p-2 text-center text-gray-700 dark:text-white">Super Beheerder</th>
                <th class="border p-2 text-center text-gray-700 dark:text-white">Acties</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr class="border dark:border-gray-600">
                    <td class="p-2 text-center text-gray-700 dark:text-white">{{ $user->name }}</td>
                    <td class="p-2 text-center text-gray-700 dark:text-white">{{ $user->email }}</td>
                    <td class="p-2 text-center text-gray-700 dark:text-white">
                        @if($user->is_super_admin)
                            Ja
                        @else
                            <input type="checkbox"
                                   wire:change="updateAdminStatus({{ $user->id }}, $event.target.checked)"
                                   @checked($user->is_admin)
                                   class="form-checkbox h-5 w-5 text-green-500 cursor-pointer">
                        @endif
                    </td>
                    <td class="p-2 text-center text-gray-700 dark:text-white">
                        @if($user->is_super_admin)
                            Ja
                        @else
                            <button wire:click="confirmSuperAdminStatus({{ $user->id }}, '{{ $user->name }}')" class="cursor-pointer">
                                <flux:icon icon="x-mark" class="text-red-500"></flux:icon>
                            </button>
                        @endif
                    </td>
                    <td class="p-2 text-center">
                        @if($user->is_super_admin)
                            Geen
                        @else
                            <button wire:click="deleteUser({{ $user->id }})"
                                    class="cursor-pointer bg-red-500 text-white p-2 rounded hover:bg-red-600">
                                <flux:icon icon="trash" class="text-white"></flux:icon>
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Mobile View -->
        <div class="sm:hidden">
            @foreach ($users as $user)
                <div class="border p-4 mb-2 bg-white dark:bg-gray-800 rounded shadow">
                    <p><strong>Naam:</strong> {{ $user->name }}</p>
                    <p><strong>E-mail:</strong> {{ $user->email }}</p>
                    <p class="flex items-center gap-2"><strong>Beheerder:</strong>
                        @if($user->is_super_admin)
                            Ja
                        @else
                            <input type="checkbox"
                                   wire:change="updateAdminStatus({{ $user->id }}, $event.target.checked)"
                                   @checked($user->is_admin)
                                   class="form-checkbox h-5 w-5 text-green-500 cursor-pointer">
                        @endif
                    </p>
                    <p class="flex items-center"><strong>Super Beheerder:</strong>
                        @if($user->is_super_admin)
                            Ja
                        @else
                            <button wire:click="confirmSuperAdminStatus({{ $user->id }}, '{{ $user->name }}')" class="cursor-pointer">
                                <flux:icon icon="x-mark" class="text-red-500"></flux:icon>
                            </button>
                        @endif
                    </p>
                    <p class="flex items-center gap-2"><strong>Acties:</strong>
                        @if($user->is_super_admin)
                            Geen
                        @else
                            <button wire:click="confirmDeleteSelectedUser({{ $user->id }}, '{{ $user->name }}')"
                                    class="cursor-pointer bg-red-500 text-white p-1 rounded hover:bg-red-600">
                                <flux:icon icon="trash" class="text-white"></flux:icon>
                            </button>
                        @endif
                    </p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Confirmation Modals -->
    @if($confirmSuperAdmin)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-xl w-96 max-w-sm space-y-4">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Weet je zeker dat je gebruiker: "{{ $selectedUserName }}" een superbeheerder wilt maken?
                </h3>
                <div class="flex justify-between">
                    <button
                        wire:click="makeUserSuperAdmin()"
                        class="cursor-pointer bg-red-600 text-white py-2 px-4 rounded-lg shadow-md hover:bg-red-700 transition duration-300">
                        Ja
                    </button>
                    <button
                        wire:click="toggleConfirmingSuperAdminStatus()"
                        class="cursor-pointer bg-gray-500 text-white py-2 px-4 rounded-lg shadow-md hover:bg-gray-600 transition duration-300">
                        Nee
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation -->
    @if($confirmDelete)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-xl w-96 max-w-sm space-y-4">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Weet je zeker dat je alle niet-beheerder gebruikers wilt verwijderen?</h3>
                <div class="flex justify-between">
                    <button
                        wire:click="deleteAllNonAdminUsers()"
                        class="cursor-pointer bg-red-600 text-white py-2 px-4 rounded-lg shadow-md hover:bg-red-700 transition duration-300">
                        Ja
                    </button>
                    <button
                        wire:click="toggleConfirmDelete()"
                        class="cursor-pointer bg-gray-500 text-white py-2 px-4 rounded-lg shadow-md hover:bg-gray-600 transition duration-300">
                        Nee
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Confirm Delete User -->
    @if($confirmDeleteUser)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-xl w-96 max-w-sm space-y-4">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Weet je zeker dat je gebruiker: "{{ $selectedUserName }}" wilt verwijderen?
                </h3>
                <div class="flex justify-between">
                    <button
                        wire:click="deleteUser()"
                        class="cursor-pointer bg-red-600 text-white py-2 px-4 rounded-lg shadow-md hover:bg-red-700 transition duration-300">
                        Ja
                    </button>
                    <button
                        wire:click="toggleConfirmDelete()"
                        class="cursor-pointer bg-gray-500 text-white py-2 px-4 rounded-lg shadow-md hover:bg-gray-600 transition duration-300">
                        Nee
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
