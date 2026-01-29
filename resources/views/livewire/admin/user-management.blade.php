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


                @php
                    // only the filter inputs
                    $filterTargets = 'role,status,joinedFrom,joinedTo,sortBy,sortDir,resetFilters';
                @endphp

                <div class="relative z-[9999]">
                    <!-- FILTER BUTTON -->
                    <button type="button" wire:click="toggleFilters"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm rounded-md border border-slate-200
        bg-white text-slate-700 hover:bg-slate-50 transition">

                        <x-lucide-filter class="w-4 h-4" />
                        <span>Filters</span>

                        <!-- loader ONLY when dropdown toggles -->
                        <svg wire:loading wire:click="toggleFilters" class="w-4 h-4 animate-spin text-slate-500"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>

                        @if ($this->hasActiveFilters)
                            <span
                                class="ml-1 px-2 py-0.5 text-[11px] rounded-full bg-brand/10 text-brand font-semibold">
                                Active
                            </span>
                        @endif
                    </button>

                    <!-- DROPDOWN -->
                    @if ($showFilters)
                        <div
                            class="absolute right-0 top-full mt-2 w-[340px]
            bg-white border border-slate-200 shadow-lg rounded-xl p-4">

                            <!-- overlay loader INSIDE dropdown -->
                            <div wire:loading wire:target="{{ $filterTargets }}"
                                class="absolute inset-0 z-50 rounded-xl bg-white/70 backdrop-blur-[1px]
                flex items-center justify-center text-center h-full">
                                <div
                                    class="inline-flex items-center justify-center gap-2 text-sm text-slate-600 font-medium">
                                    <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                    Applying...
                                </div>
                            </div>

                            <div class="flex items-center justify-between mb-3">
                                <div class="text-sm font-semibold text-slate-900">Filters</div>

                                <button type="button" wire:click="$set('showFilters', false)"
                                    class="text-slate-400 hover:text-slate-700">
                                    <x-lucide-x class="w-4 h-4" />
                                </button>
                            </div>

                            <div class="space-y-4">

                                <!-- Role -->
                                <div>
                                    <label class="text-xs font-medium text-slate-600">Role</label>
                                    <select wire:model.live="role"
                                        class="mt-1 w-full rounded-lg border border-slate-200 text-sm px-3 py-2">
                                        <option value="">All Roles</option>
                                        <option value="user">USER</option>
                                        <option value="admin">ADMIN</option>
                                    </select>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="text-xs font-medium text-slate-600">Status</label>
                                    <select wire:model.live="status"
                                        class="mt-1 w-full rounded-lg border border-slate-200 text-sm px-3 py-2">
                                        <option value="">All</option>
                                        <option value="active">Active</option>
                                        <option value="blocked">Blocked</option>
                                    </select>
                                </div>

                                <!-- Joined Date -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-xs font-medium text-slate-600">Joined From</label>
                                        <input type="date" wire:model.live="joinedFrom"
                                            class="mt-1 w-full rounded-lg border border-slate-200 text-sm px-3 py-2" />
                                    </div>

                                    <div>
                                        <label class="text-xs font-medium text-slate-600">Joined To</label>
                                        <input type="date" wire:model.live="joinedTo"
                                            class="mt-1 w-full rounded-lg border border-slate-200 text-sm px-3 py-2" />
                                    </div>
                                </div>

                                <!-- Sort -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-xs font-medium text-slate-600">Sort By</label>
                                        <select wire:model.live="sortBy"
                                            class="mt-1 w-full rounded-lg border border-slate-200 text-sm px-3 py-2">
                                            <option value="created_at">Date Joined</option>
                                            <option value="fullName">Name</option>
                                            <option value="role">Role</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-xs font-medium text-slate-600">Order</label>
                                        <select wire:model.live="sortDir"
                                            class="mt-1 w-full rounded-lg border border-slate-200 text-sm px-3 py-2">
                                            <option value="desc">Desc</option>
                                            <option value="asc">Asc</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">

                                    <!-- Reset (no spinner now) -->
                                    <button type="button" wire:click="resetFilters"
                                        class="px-3 py-2 rounded-lg border border-slate-200 text-slate-700 text-sm hover:bg-slate-50">
                                        Reset
                                    </button>

                                </div>

                            </div>
                        </div>
                    @endif
                </div>




                <!-- ADD NEW USER BUTTON -->
                {{-- <button
                    class="w-38 inline-flex items-center gap-2 px-4 py-2.5 text-sm rounded-md
           bg-black text-white hover:bg-gray-800 transition">
                    <x-lucide-user-plus class="w-4 h-4" />
                    <span>Add New User</span>
                </button> --}}

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
                                        <div class="font-medium text-slate-900">{{ $user->fullName ?? 'Unnamed' }}
                                        </div>
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
                                    {{ $user->is_blocked ? 'Blocked' : 'Active' }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">

                                    <!-- Block/Unblock -->
                                    <button wire:click="toggleBlock('{{ $user->id }}')"
                                        wire:loading.attr="disabled" wire:target="toggleBlock('{{ $user->id }}')"
                                        class="p-2 rounded-md transition
        {{ $user->is_blocked
            ? 'text-green-600 hover:text-green-700 hover:bg-green-50'
            : 'text-amber-600 hover:text-amber-700 hover:bg-amber-50' }}"
                                        title="{{ $user->is_blocked ? 'Unblock user' : 'Block user' }}">

                                        @if ($user->is_blocked)
                                            <x-lucide-check wire:loading.remove
                                                wire:target="toggleBlock('{{ $user->id }}')" class="w-4 h-4" />
                                        @else
                                            <x-lucide-ban wire:loading.remove
                                                wire:target="toggleBlock('{{ $user->id }}')" class="w-4 h-4" />
                                        @endif

                                        <!-- loader -->
                                        <svg wire:loading wire:target="toggleBlock('{{ $user->id }}')"
                                            class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                        </svg>
                                    </button>

                                    {{-- <!-- Show all resumes button -->
                                    <a href="{{ route('admin.users.index', $user->id) }}"
                                        class="p-2 rounded-md text-slate-400 hover:text-slate-700
        hover:bg-slate-100 transition"
                                        title="View user's resumes">
                                        <x-lucide-file-text class="w-4 h-4" />
                                    </a> --}}


                                    <a href="{{ route('admin.users.show', $user->id) }}"
                                        class="p-2 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                                        title="View user">
                                        <x-lucide-eye class="w-4 h-4" />
                                    </a>


                                    <!-- Delete -->
                                    <button wire:click="confirmDelete('{{ $user->id }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="confirmDelete('{{ $user->id }}')"
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
