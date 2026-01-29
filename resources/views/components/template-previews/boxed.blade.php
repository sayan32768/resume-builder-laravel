@props([
    'color' => null,
    'seed' => null, // template key recommended
])

@php
    $palette = config('resume_colors', []);
    $count = count($palette);

    if (!$color && $count > 0) {
        $index = $seed ? crc32($seed) % $count : array_rand($palette);
        $color = $palette[$index]['value'];
    }

    $color = $color ?? '#111827';
@endphp

<div class="relative h-full w-full overflow-hidden">

    <!-- Soft tinted background -->
    <div class="absolute inset-0" style="background-color: {{ $color }}; opacity: 0.08;"></div>

    <!-- Floating resume frame -->
    <div class="absolute inset-4 overflow-hidden rounded-md bg-white shadow-lg">
        <div class="flex h-full">

            <!-- LEFT SIDEBAR -->
            <div class="w-1/3 bg-slate-100 p-3">
                <div class="mb-3 h-1.5 w-20 rounded bg-slate-400"></div>

                <div class="mb-3 space-y-2">
                    <div class="h-1.5 w-full rounded bg-slate-200"></div>
                    <div class="h-1.5 w-4/5 rounded bg-slate-200"></div>
                    <div class="h-1.5 w-3/5 rounded bg-slate-200"></div>
                </div>

                <div class="space-y-2">
                    <div class="h-1.5 w-5/6 rounded bg-slate-200"></div>
                    <div class="h-1.5 w-4/6 rounded bg-slate-200"></div>
                    <div class="h-1.5 w-3/6 rounded bg-slate-200"></div>
                </div>
            </div>

            <!-- RIGHT CONTENT -->
            <div class="flex-1 p-3">

                <!-- BOXED HEADER -->
                <div class="mx-auto mb-3 h-4 w-36 rounded border-2" style="border-color: {{ $color }};"></div>

                <!-- Profile -->
                <div class="mb-3 space-y-2">
                    <div class="h-1.5 w-full rounded bg-slate-200"></div>
                    <div class="h-1.5 w-5/6 rounded bg-slate-200"></div>
                </div>

                <!-- SECTION -->
                <div class="mb-3 space-y-2">
                    <div class="h-1.5 w-28 rounded bg-slate-200"></div>
                    <div class="h-1.5 w-full rounded bg-slate-200"></div>
                    <div class="h-1.5 w-5/6 rounded bg-slate-200"></div>
                </div>

                <div class="space-y-2">
                    <div class="h-1.5 w-32 rounded bg-slate-200"></div>
                    <div class="h-1.5 w-4/6 rounded bg-slate-200"></div>
                </div>

            </div>
        </div>
    </div>
</div>
