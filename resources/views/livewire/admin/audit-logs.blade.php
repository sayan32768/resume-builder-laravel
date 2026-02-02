<div>
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Audit Logs</h1>
                <p class="text-sm text-slate-500">
                    Track admin actions, security events, and system changes across the platform.
                </p>
            </div>

            <div class="flex items-center gap-2">

                @php
                    $filterTargets = 'action,dateFrom,dateTo,sortBy,sortDir,resetFilters,search';
                @endphp

                <!-- Filters -->
                <div class="relative z-[9999]">
                    <button type="button" wire:click="toggleFilters"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm rounded-md border border-slate-200
                               bg-white text-slate-700 hover:bg-slate-50 transition">

                        <x-lucide-filter class="w-4 h-4" />
                        <span>Filters</span>

                        <svg wire:loading wire:target="toggleFilters" class="w-4 h-4 animate-spin text-slate-500"
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

                    @if ($showFilters)
                        <div
                            class="absolute right-0 top-full mt-2 w-[360px]
                                    bg-white border border-slate-200 shadow-lg rounded-xl p-4">

                            <div wire:loading wire:target="{{ $filterTargets }}"
                                class="absolute inset-0 z-50 rounded-xl bg-white/70 backdrop-blur-[1px]
                                       flex items-center justify-center h-full">
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
                                <!-- Action -->
                                <div>
                                    <label class="text-xs font-medium text-slate-600">Action</label>
                                    <select wire:model.live="action"
                                        class="mt-1 w-full rounded-lg border border-slate-200 text-sm px-3 py-2">
                                        <option value="">All Actions</option>
                                        @foreach ($actions as $a)
                                            <option value="{{ $a }}">{{ $a }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Date -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-xs font-medium text-slate-600">From</label>
                                        <input type="date" wire:model.live="dateFrom"
                                            class="mt-1 w-full rounded-lg border border-slate-200 text-sm px-3 py-2" />
                                    </div>

                                    <div>
                                        <label class="text-xs font-medium text-slate-600">To</label>
                                        <input type="date" wire:model.live="dateTo"
                                            class="mt-1 w-full rounded-lg border border-slate-200 text-sm px-3 py-2" />
                                    </div>
                                </div>

                                <!-- Sort -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-xs font-medium text-slate-600">Sort By</label>
                                        <select wire:model.live="sortBy"
                                            class="mt-1 w-full rounded-lg border border-slate-200 text-sm px-3 py-2">
                                            <option value="created_at">Date</option>
                                            <option value="action">Action</option>
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
                                    <button type="button" wire:click="resetFilters"
                                        class="px-3 py-2 rounded-lg border border-slate-200 text-slate-700 text-sm hover:bg-slate-50">
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Export Button -->
                {{-- <button wire:click="exportLogs"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm rounded-md
                           bg-black text-white hover:bg-gray-800 transition">

                    <x-lucide-download class="w-4 h-4" />
                    <span>Export Logs</span>

                    <svg wire:loading wire:target="exportLogs" class="w-4 h-4 animate-spin text-white"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </button> --}}
                <a href="{{ route('admin.audit-logs.export') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm rounded-md
          bg-black text-white hover:bg-gray-800 transition">
                    <x-lucide-download class="w-4 h-4" />
                    <span>Export Logs</span>
                </a>

            </div>
        </div>

        <!-- Search -->
        <div class="relative">
            <div class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                <x-lucide-search class="w-4 h-4" />
            </div>

            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="Search by actor, action, target..."
                class="w-full rounded-lg border-2 border-slate-300 bg-white py-3 pl-10 pr-6 text-sm
                       focus:outline-none focus:border-brand focus:ring-2 focus:border-0 focus:ring-brand/30" />

            <div wire:loading wire:target="search"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-500 whitespace-nowrap">
                <span class="inline-flex items-center gap-2">
                    <svg class="inline-block w-4 h-4 animate-spin align-middle" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span>Searching...</span>
                </span>
            </div>
        </div>

        <!-- Alerts -->
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

        <!-- Logs Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Action</th>
                        <th class="px-4 py-3 text-left">Actor</th>
                        <th class="px-4 py-3 text-left">Target</th>
                        {{-- <th class="px-4 py-3 text-left">IP</th> --}}
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-right">Details</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50">
                            <!-- Action -->
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full bg-brand/10 text-brand font-semibold">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <!-- Actor -->
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">
                                    {{ $log->actor?->fullName ?? 'System' }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    {{ $log->actor?->email ?? '-' }}
                                </div>
                            </td>

                            <!-- Target -->
                            <td class="px-4 py-3 text-slate-600">
                                <div class="text-xs">
                                    {{ class_basename($log->target_type ?? '-') }}
                                </div>
                                <div class="text-xs text-slate-400">
                                    {{ $log->target_id ?? '-' }}
                                </div>
                            </td>

                            <!-- IP -->
                            {{-- <td class="px-4 py-3 text-slate-500">
                                {{ $log->ip ?? '-' }}
                            </td> --}}

                            <!-- Date -->
                            <td class="px-4 py-3 text-slate-500">
                                {{-- {{ optional($log->created_at)->format('M d, Y h:i A') }} --}}
                                {{ optional($log->created_at)->timezone('Asia/Kolkata')->format('M d, Y h:i A') }}
                            </td>

                            <!-- Details -->
                            <td class="px-4 py-3 text-right">
                                <button wire:click="viewLog({{ $log->id }})"
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200
                                           text-slate-700 hover:bg-slate-50 transition">
                                    <x-lucide-eye class="w-4 h-4" />
                                    <span class="text-sm">View</span>

                                    <svg wire:loading wire:target="viewLog({{ $log->id }})"
                                        class="w-4 h-4 animate-spin text-slate-500" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                <div class="flex flex-col items-center gap-2">
                                    <x-lucide-file-text class="w-10 h-10 text-slate-300" />
                                    <div class="text-sm font-medium text-slate-700">No logs found</div>
                                    <div class="text-xs text-slate-500">
                                        Try changing filters or searching another keyword.
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $logs->links() }}
            </div>
        </div>
    </div>

    <!-- VIEW MODAL -->
    @if ($showViewModal && $viewLog)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40" wire:click="closeViewModal"></div>

            <div class="relative w-full max-w-3xl bg-white rounded-2xl shadow-lg border border-slate-200 p-6">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-lg font-bold text-slate-900">Audit Log Details</div>
                        <div class="text-sm text-slate-500 mt-1">
                            {{ $viewLog->action }} • {{ optional($viewLog->created_at)->format('M d, Y h:i A') }}
                        </div>
                    </div>

                    <button wire:click="closeViewModal" class="text-slate-400 hover:text-slate-700">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-xs font-semibold text-slate-700 mb-2">Before</div>
                        <pre class="text-xs text-slate-700 whitespace-pre-wrap break-words">{{ json_encode($viewLog->before, JSON_PRETTY_PRINT) }}</pre>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-xs font-semibold text-slate-700 mb-2">After</div>
                        <pre class="text-xs text-slate-700 whitespace-pre-wrap break-words">{{ json_encode($viewLog->after, JSON_PRETTY_PRINT) }}</pre>
                    </div>

                    <div class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-xs font-semibold text-slate-700 mb-2">Meta</div>
                        <pre class="text-xs text-slate-700 whitespace-pre-wrap break-words">{{ json_encode($viewLog->meta, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button wire:click="closeViewModal"
                        class="px-4 py-2 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
