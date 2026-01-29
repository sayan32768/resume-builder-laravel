@props([
    'color' => null,
    'seed' => null,
])

@php
    $palette = config('resume_colors', []);
    $count = count($palette);

    if (!$color && $count > 0) {
        $index = $seed ? crc32($seed) % $count : array_rand($palette);
        $color = $palette[$index]['value'];
    }

    $color = $color ?? '#183D3D';
@endphp

<div class="relative h-full w-full overflow-hidden">

    <!-- Soft tinted background -->
    <div class="absolute inset-0" style="background-color: {{ $color }}; opacity: 0.12;"></div>

    <!-- Floating resume -->
    <div class="absolute inset-4 flex overflow-hidden rounded-md bg-white shadow-lg">

        <!-- Sidebar -->
        <div class="w-1/3 p-3" style="background-color: {{ $color }};">
            <div class="mb-3 h-2.5 w-16 rounded bg-white/70"></div>

            <div class="space-y-2">
                <div class="h-1.5 w-full rounded bg-white/30"></div>
                <div class="h-1.5 w-4/5 rounded bg-white/30"></div>
                <div class="h-1.5 w-3/5 rounded bg-white/30"></div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1 p-3">
            <div class="mb-2 h-2.5 w-40 rounded" style="background-color: {{ $color }}; opacity: 0.85;"></div>

            <div class="mb-3 h-px w-full" style="background-color: {{ $color }}; opacity: 0.35;"></div>

            <div class="space-y-2">
                <div class="h-1.5 w-full rounded bg-slate-200"></div>
                <div class="h-1.5 w-5/6 rounded bg-slate-200"></div>
                <div class="h-1.5 w-4/6 rounded bg-slate-200"></div>
            </div>
        </div>

    </div>
</div>
