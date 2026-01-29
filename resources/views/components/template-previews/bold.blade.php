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

    $color = $color ?? '#f14d34';
@endphp


<div class="relative h-full w-full overflow-hidden rounded-none">

    <!-- Soft tinted background -->
    <div class="absolute inset-0" style="background-color: {{ $color }}; opacity: 0.12;"></div>

    <!-- Floating resume frame -->
    <div class="absolute inset-4 overflow-hidden rounded-md bg-white shadow-lg">
        <div class="flex h-full">

            <!-- LEFT ACCENT STRIP -->
            <div class="w-[14px]" style="background-color: {{ $color }};"></div>

            <!-- MAIN CONTENT -->
            <div class="flex-1 p-3">

                <!-- Name -->
                <div class="mb-2 h-2.5 w-2/3 rounded bg-slate-900"></div>

                <!-- Contact row -->
                <div class="mb-3 flex gap-2">
                    <div class="h-1.5 w-1/4 rounded bg-slate-300"></div>
                    <div class="h-1.5 w-1/5 rounded bg-slate-300"></div>
                    <div class="h-1.5 w-1/3 rounded bg-slate-300"></div>
                </div>

                <!-- Summary -->
                <div class="mb-3 space-y-2">
                    <div class="h-1.5 w-full rounded bg-slate-200"></div>
                    <div class="h-1.5 w-5/6 rounded bg-slate-200"></div>
                </div>

                <!-- EXPERIENCE -->
                <div class="mb-3">
                    <div class="mb-2 h-1.5 w-24 rounded" style="background-color: {{ $color }}; opacity: .85;">
                    </div>
                    <div class="space-y-2">
                        <div class="h-1.5 w-1/2 rounded bg-slate-200"></div>
                        <div class="h-1.5 w-full rounded bg-slate-200"></div>
                        <div class="h-1.5 w-5/6 rounded bg-slate-200"></div>
                    </div>
                </div>

                <!-- PROJECTS -->
                <div>
                    <div class="mb-2 h-1.5 w-24 rounded" style="background-color: {{ $color }}; opacity: .85;">
                    </div>
                    <div class="space-y-2">
                        <div class="h-1.5 w-3/5 rounded bg-slate-200"></div>
                        <div class="h-1.5 w-4/6 rounded bg-slate-200"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
