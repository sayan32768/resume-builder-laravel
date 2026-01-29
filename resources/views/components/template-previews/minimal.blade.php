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

    <!-- Tinted background (same as Classic) -->
    <div class="absolute inset-0" style="background-color: {{ $color }}; opacity: 0.08;"></div>

    <!-- Floating resume frame -->
    <div class="absolute inset-4 overflow-hidden rounded-md bg-white shadow-lg">

        <!-- Resume content -->
        <div class="flex h-full flex-col px-4 pt-4">

            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto mb-2 h-2.5 w-32 rounded bg-slate-300"></div>
                <div class="mx-auto h-1.5 w-40 rounded bg-slate-200"></div>

                <div class="mt-3 flex justify-center gap-2">
                    <div class="h-1.5 w-16 rounded bg-slate-200"></div>
                    <div class="h-1.5 w-16 rounded bg-slate-200"></div>
                    <div class="h-1.5 w-20 rounded bg-slate-200"></div>
                </div>
            </div>

            <!-- Divider -->
            <div class="my-3 h-px bg-slate-200"></div>

            <!-- Body -->
            <div class="flex-1 space-y-3">

                <!-- Experience -->
                <div>
                    <div class="mb-2 h-1.5 w-24 rounded" style="background-color: {{ $color }}; opacity: 0.7;">
                    </div>
                    <div class="space-y-2">
                        <div class="h-1.5 w-28 rounded bg-slate-200"></div>
                        <div class="h-1.5 w-full rounded bg-slate-200"></div>
                        <div class="h-1.5 w-5/6 rounded bg-slate-200"></div>
                    </div>
                </div>

                <!-- Projects -->
                <div>
                    <div class="mb-2 h-1.5 w-24 rounded" style="background-color: {{ $color }}; opacity: 0.7;">
                    </div>
                    <div class="space-y-2">
                        <div class="h-1.5 w-32 rounded bg-slate-200"></div>
                        <div class="h-1.5 w-full rounded bg-slate-200"></div>
                    </div>
                </div>

                <!-- Education -->
                <div>
                    <div class="mb-2 h-1.5 w-24 rounded" style="background-color: {{ $color }}; opacity: 0.7;">
                    </div>
                    <div class="space-y-2">
                        <div class="h-1.5 w-40 rounded bg-slate-200"></div>
                        <div class="h-1.5 w-4/6 rounded bg-slate-200"></div>
                    </div>
                </div>

                <!-- Skills -->
                <div>
                    <div class="mb-2 h-1.5 w-20 rounded" style="background-color: {{ $color }}; opacity: 0.7;">
                    </div>

                    <div class="flex flex-wrap gap-x-2 gap-y-1">
                        @for ($i = 0; $i < 6; $i++)
                            <div class="h-1.5 w-10 rounded bg-slate-200"></div>
                        @endfor
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
