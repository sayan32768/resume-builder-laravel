<div>
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex items-center gap-2 justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">User Management</h1>
                <p class="text-sm text-slate-500">
                    View, filter, and manage all registered users on the platform.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button
                    class="w-38 inline-flex items-center gap-2 px-4 py-2.5 text-sm rounded-md
           bg-black text-white hover:bg-gray-800 transition">
                    <x-lucide-user-plus class="w-4 h-4" />
                    <span>Add New User</span>
                </button>

            </div>
        </div>


        <div class="relative">
            <!-- search icon -->
            <div class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                <x-lucide-search class="w-4 h-4" />
            </div>

            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name, email..."
                class="w-full rounded-lg border-2 border-slate-300 bg-white py-3 pl-10 pr-6 text-sm
           focus:outline-none focus:border-brand focus:ring-2 focus:border-0 focus:ring-brand/30" />

            <!-- searching loader -->
            <div wire:loading wire:target="search"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-500 whitespace-nowrap">

                <span class="inline-flex items-center gap-2">
                    <svg class="inline-block w-4 h-4 animate-spin align-middle" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>

                    <span class="inline-block align-middle">Searching...</span>
                </span>

            </div>


        </div>


        @if (session('success'))
            <div class="p-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        <!-- User Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Name & Email</th>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-left">Joined</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="">

                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-9 w-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-semibold">
                                        {{ strtoupper(substr($user->fullName ?? $user->email, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-900">{{ $user->fullName ?? 'Unnamed' }}</div>
                                        <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700">
                                    {{ ucfirst($user->role ?? 'Free') }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-slate-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>

                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-1 text-xs rounded-full
                            {{ $user->is_blocked ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $user->is_blocked ? 'Suspended' : 'Active' }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <!-- View -->
                                    <button
                                        class="p-2 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                                        title="View user">
                                        <x-lucide-eye class="w-4 h-4" />
                                    </button>

                                    <!-- Delete -->
                                    <button wire:click="confirmDelete('{{ $user->id }}')"
                                        wire:loading.attr="disabled" wire:target="confirmDelete('{{ $user->id }}')"
                                        class="p-2 rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50 transition
           disabled:opacity-50 disabled:cursor-not-allowed"
                                        title="Delete user">

                                        <!-- icon -->
                                        <x-lucide-trash-2 wire:loading.remove
                                            wire:target="confirmDelete('{{ $user->id }}')" class="w-4 h-4" />

                                        <!-- spinner -->
                                        <svg wire:loading wire:target="confirmDelete('{{ $user->id }}')"
                                            class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                        </svg>
                                    </button>


                                    {{-- <div class="text-red-500 font-bold">
                                        LIVEWIRE TEST: {{ now() }}
                                    </div> --}}

                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                <div class="flex flex-col items-center gap-2">
                                    <x-lucide-users class="w-10 h-10 text-slate-300" />
                                    <div class="text-sm font-medium text-slate-700">No users found</div>
                                    <div class="text-xs text-slate-500">
                                        Try searching with a different name or email.
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $users->links() }}
            </div>
        </div>

    </div>

    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">

            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/40" wire:click="cancelDelete"></div>

            <!-- Modal -->
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-lg border border-slate-200 p-6">
                <div class="flex items-start gap-4">

                    <!-- Icon -->
                    <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                        <x-lucide-triangle-alert class="w-5 h-5 text-red-600" />
                    </div>

                    <div class="flex-1">
                        <div class="text-lg font-bold text-slate-900">Delete User</div>
                        <div class="text-sm text-slate-600 mt-1">
                            Are you sure you want to delete
                            <span class="font-semibold text-slate-900">{{ $deleteUserName }}</span>?
                            <br />
                            This action cannot be undone.
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button wire:click="cancelDelete" wire:loading.attr="disabled" wire:target="deleteConfirmed"
                        class="px-4 py-2 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm
           disabled:opacity-60 disabled:cursor-not-allowed">
                        Cancel
                    </button>


                    <button wire:click="deleteConfirmed" wire:loading.attr="disabled" wire:target="deleteConfirmed"
                        class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm
           inline-flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">

                        <!-- Spinner (only while loading) -->
                        <svg wire:loading wire:target="deleteConfirmed" class="h-4 w-4 animate-spin"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                            </path>
                        </svg>

                        <span wire:loading.remove wire:target="deleteConfirmed">Delete</span>
                        <span wire:loading wire:target="deleteConfirmed">Deleting...</span>
                    </button>

                </div>
            </div>
        </div>
    @endif


</div>
