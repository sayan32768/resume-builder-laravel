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

    $color = $color ?? '#8F9B8F';
@endphp

<div class="relative h-full w-full overflow-hidden">

    <!-- Soft tinted background -->
    <div class="absolute inset-0" style="background-color: {{ $color }}; opacity: 0.12;"></div>

    <!-- Floating resume frame -->
    <div class="absolute inset-4 overflow-hidden rounded-md bg-white shadow-lg">
        <div class="flex h-full">

            <!-- LEFT SIDEBAR (Charm color panel) -->
            <div class="flex w-[38%] flex-col gap-3 p-3" style="background-color: {{ $color }};">
                <div class="h-2.5 w-20 rounded bg-white/70"></div>

                <div class="space-y-2">
                    <div class="h-1.5 w-full rounded bg-white/30"></div>
                    <div class="h-1.5 w-5/6 rounded bg-white/30"></div>
                    <div class="h-1.5 w-4/6 rounded bg-white/30"></div>
                </div>

                <div class="mt-2 space-y-2">
                    <div class="h-1.5 w-3/4 rounded bg-white/30"></div>
                    <div class="h-1.5 w-2/3 rounded bg-white/30"></div>
                </div>
            </div>

            <!-- RIGHT CONTENT -->
            <div class="flex flex-1 flex-col">

                <!-- Header band -->
                <div class="bg-[#ECECE5] px-3 py-2.5">
                    <div class="h-2.5 w-40 rounded bg-slate-400"></div>
                    <div class="mt-2 h-1.5 w-28 rounded bg-slate-300"></div>
                </div>

                <!-- Body -->
                <div class="flex-1 space-y-3 p-3">

                    <!-- Experience block -->
                    <div>
                        <div class="mb-2 h-1.5 w-24 rounded"
                            style="background-color: {{ $color }}; opacity: 0.7;"></div>
                        <div class="space-y-2">
                            <div class="h-1.5 w-32 rounded bg-slate-200"></div>
                            <div class="h-1.5 w-full rounded bg-slate-200"></div>
                            <div class="h-1.5 w-5/6 rounded bg-slate-200"></div>
                        </div>
                    </div>

                    <!-- Projects block -->
                    <div>
                        <div class="mb-2 h-1.5 w-24 rounded"
                            style="background-color: {{ $color }}; opacity: 0.7;"></div>
                        <div class="space-y-2">
                            <div class="h-1.5 w-40 rounded bg-slate-200"></div>
                            <div class="h-1.5 w-4/6 rounded bg-slate-200"></div>
                        </div>
                    </div>

                    <!-- Education block -->
                    <div>
                        <div class="mb-2 h-1.5 w-24 rounded"
                            style="background-color: {{ $color }}; opacity: 0.7;"></div>
                        <div class="space-y-2">
                            <div class="h-1.5 w-28 rounded bg-slate-200"></div>
                            <div class="h-1.5 w-3/4 rounded bg-slate-200"></div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
