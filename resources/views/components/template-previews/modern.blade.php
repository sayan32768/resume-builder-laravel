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

    <!-- Tinted background -->
    <div class="absolute inset-0" style="background-color: {{ $color }}; opacity: 0.08;"></div>

    <!-- Content -->
    <div class="absolute inset-4 rounded-md bg-white shadow-lg overflow-hidden">

        <!-- Header -->
        <div class="px-3 pt-3">
            <div class="h-2.5 w-32 rounded" style="background-color: {{ $color }};"></div>
            <div class="mt-2 h-1.5 w-40 rounded bg-slate-300"></div>
        </div>

        <!-- Divider -->
        <div class="mx-3 my-3 h-px" style="background-color: {{ $color }}; opacity: 0.35;"></div>

        <!-- Body -->
        <div class="grid grid-cols-[1fr_2fr] gap-3 px-3 pb-3">
            <div class="space-y-2">
                <div class="h-1.5 w-full rounded bg-slate-200"></div>
                <div class="h-1.5 w-4/5 rounded bg-slate-200"></div>
                <div class="h-1.5 w-3/5 rounded bg-slate-200"></div>
            </div>

            <div class="space-y-2">
                <div class="h-1.5 w-full rounded bg-slate-300"></div>
                <div class="h-1.5 w-5/6 rounded bg-slate-300"></div>
                <div class="h-1.5 w-4/6 rounded bg-slate-300"></div>
            </div>
        </div>

    </div>
</div>
