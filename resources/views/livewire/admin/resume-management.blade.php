<div class="space-y-6">

    <!-- Header actions -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Resumes</h1>
            <p class="text-sm text-slate-500">All resumes created on the platform</p>
        </div>

        <div class="flex items-center gap-2">
            @php
                $filterTargets = 'resumeType,createdFrom,createdTo,sortBy,sortDir,resetFilters';
            @endphp

            <div class="relative z-[9999]">
                <!-- FILTER BUTTON -->
                <button type="button" wire:click="toggleFilters" wire:loading.attr="disabled" wire:target="toggleFilters"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm rounded-md border border-slate-200
    bg-white text-slate-700 hover:bg-slate-50 transition disabled:opacity-60 disabled:cursor-not-allowed">

                    <x-lucide-filter class="w-4 h-4" />
                    <span>Filters</span>

                    <!-- Loader ONLY when opening/closing -->
                    <svg wire:loading wire:target="toggleFilters" class="w-4 h-4 animate-spin text-slate-500"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>

                    @if ($this->hasActiveFilters)
                        <span class="ml-1 px-2 py-0.5 text-[11px] rounded-full bg-brand/10 text-brand font-semibold">
                            Active
                        </span>
                    @endif
                </button>

                <!-- FILTER DROPDOWN -->
                @if ($this->showFilters)
                    <div
                        class="absolute right-0 top-full mt-2 w-[340px]
                    bg-white border border-slate-200 shadow-lg rounded-xl p-4">

                        <!-- overlay loader -->
                        <div wire:loading wire:target="{{ $filterTargets }}"
                            class="absolute inset-0 z-50 rounded-xl bg-white/70 backdrop-blur-[1px]
                        flex items-center justify-center text-center">
                            <div class="inline-flex items-center gap-2 text-sm text-slate-600 font-medium">
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

                            <!-- Template -->
                            <div>
                                <label class="text-xs font-medium text-slate-600">Template</label>
                                <select wire:model.live="resumeType"
                                    class="mt-1 w-full rounded-lg border border-slate-200 text-sm px-3 py-2">
                                    <option value="">All Templates</option>
                                    @foreach ($resumeTypes as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Created date -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs font-medium text-slate-600">Created From</label>
                                    <input type="date" wire:model.live="createdFrom"
                                        class="mt-1 w-full rounded-lg border border-slate-200 text-sm px-3 py-2" />
                                </div>

                                <div>
                                    <label class="text-xs font-medium text-slate-600">Created To</label>
                                    <input type="date" wire:model.live="createdTo"
                                        class="mt-1 w-full rounded-lg border border-slate-200 text-sm px-3 py-2" />
                                </div>
                            </div>

                            <!-- Sort -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs font-medium text-slate-600">Sort By</label>
                                    <select wire:model.live="sortBy"
                                        class="mt-1 w-full rounded-lg border border-slate-200 text-sm px-3 py-2">
                                        <option value="created_at">Created</option>
                                        <option value="resumeTitle">Title</option>
                                        <option value="resumeType">Template</option>
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

                            <!-- Reset -->
                            <div class="flex justify-end pt-2 border-t border-slate-100">
                                <button type="button" wire:click="resetFilters"
                                    class="px-3 py-2 rounded-lg border border-slate-200 text-slate-700 text-sm hover:bg-slate-50">
                                    Reset
                                </button>
                            </div>

                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="p-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search -->
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

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Resume</th>
                    <th class="px-4 py-3 text-left">User</th>
                    <th class="px-4 py-3 text-left">Template</th>
                    <th class="px-4 py-3 text-left">Created</th>
                    <th class="px-4 py-3 text-right pr-6">Actions</th>
                </tr>
            </thead>

            <tbody class="border-t border-slate-200">
                @forelse($resumes as $resume)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900">
                                {{ blank($resume->resumeTitle) ? 'Untitled Resume' : $resume->resumeTitle }}
                            </div>
                            <div class="text-xs text-slate-500 break-all">
                                Resume ID: {{ $resume->id }}
                            </div>
                        </td>

                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-900">
                                {{ $resume->user->fullName ?? 'Unknown' }}
                            </div>
                            <div class="text-xs text-slate-500 break-all">
                                {{ $resume->user->email ?? '-' }}
                            </div>
                            <div class="text-xs text-slate-400 break-all">
                                User ID: {{ $resume->user->id ?? '-' }}
                            </div>
                        </td>

                        <td class="px-4 py-3 text-slate-600">
                            {{ $resume->resumeType ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-slate-600">
                            {{ $resume->created_at?->diffForHumans() ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-right pr-6">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.resumes.show', $resume->id) }}"
                                    class="p-2 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                                    title="Show Resume Data">
                                    <x-lucide-eye class="w-4 h-4" />
                                </a>

                                <a href="{{ route('admin.resumes.preview', $resume->id) }}"
                                    class="p-2 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                                    title="Preview Resume">
                                    <x-lucide-file-text class="w-4 h-4" />
                                </a>

                                <!-- Delete -->
                                <button wire:click="deleteResume('{{ $resume->id }}')"
                                    wire:confirm="Delete this resume?"
                                    class="p-2 rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50 transition"
                                    title="Delete resume">
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                            No resumes found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $resumes->links() }}
        </div>
    </div>

</div>
