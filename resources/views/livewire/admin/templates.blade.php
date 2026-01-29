<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Templates</h1>
            <p class="text-sm text-slate-500">All available resume templates and usage stats</p>
        </div>
    </div>

    {{-- <!-- Search -->
    <div class="relative">
        <div class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
            <x-lucide-search class="w-4 h-4" />
        </div>

        <input wire:model.live.debounce.250ms="search" type="text" placeholder="Search template..."
            class="w-full rounded-lg border-2 border-slate-300 bg-white py-3 pl-10 pr-6 text-sm
            focus:outline-none focus:border-brand focus:ring-2 focus:border-0 focus:ring-brand/30" />
    </div> --}}

    <!-- Search -->
    <div class="relative">
        <!-- search icon -->
        <div class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
            <x-lucide-search class="w-4 h-4" />
        </div>

        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by template name..."
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

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($templates as $t)
            @php
                $count = $usageCounts[$t['key']] ?? 0;
            @endphp

            <div
                class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition">
                <!-- preview -->
                <div class="h-64 bg-slate-50 border-b border-slate-200 overflow-hidden">
                    @if (!empty($t['preview_component']))
                        <x-dynamic-component :component="$t['preview_component']" :seed="$t['key']" />
                    @else
                        <div class="h-full w-full flex items-center justify-center text-slate-400 text-sm">
                            No preview
                        </div>
                    @endif
                </div>

                <!-- content -->
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-base font-bold text-slate-900">{{ $t['name'] }}</div>
                            <div class="text-xs text-slate-500">Key: {{ $t['key'] }}</div>
                        </div>

                        <span
                            class="px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-700 font-medium whitespace-nowrap">
                            {{ $count }} used
                        </span>
                    </div>

                    @if (!empty($t['description']))
                        <p class="text-sm text-slate-600">
                            {{ $t['description'] }}
                        </p>
                    @endif

                    <!-- actions -->
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('admin.resumes.index', ['template' => $t['key']]) }}"
                            class="px-3 py-2 text-sm rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">
                            View resumes
                        </a>

                        {{-- <a href="{{ route('admin.templates.preview', $t['key']) }}"
                            class="px-3 py-2 text-sm rounded-lg bg-black text-white hover:bg-gray-800 inline-flex items-center gap-2">
                            <x-lucide-eye class="w-4 h-4" />
                            Preview
                        </a> --}}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full p-10 text-center text-slate-500 bg-white rounded-xl border border-slate-200">
                No templates found.
            </div>
        @endforelse
    </div>

</div>
