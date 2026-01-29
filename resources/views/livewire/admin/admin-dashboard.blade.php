<div class="space-y-4 flex flex-col">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">

        <!-- Total Users -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="h-10 w-10 rounded-lg bg-teal-50 flex items-center justify-center">
                    <x-lucide-users class="w-5 h-5 text-teal-600" />
                </div>

                {{-- <span
                class="inline-flex items-center gap-1 rounded-full bg-green-100 text-green-700 px-2 py-1 text-xs font-semibold">
                ↗ +12%
            </span> --}}
            </div>

            <div class="mt-4 text-sm text-slate-500 font-medium">Total Users</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">
                {{ number_format($totalUsers) }}
            </div>
        </div>

        <!-- Resumes Created -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="h-10 w-10 rounded-lg bg-teal-50 flex items-center justify-center">
                    <x-lucide-file-text class="w-5 h-5 text-teal-600" />
                </div>

                {{-- <span
                class="inline-flex items-center gap-1 rounded-full bg-green-100 text-green-700 px-2 py-1 text-xs font-semibold">
                ↗ +8%
            </span> --}}
            </div>

            <div class="mt-4 text-sm text-slate-500 font-medium">Resumes Created</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">
                {{ number_format($totalResumes) }}
            </div>
        </div>

        <!-- PDF Downloads (dummy stat for now) -->
        {{-- <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="h-10 w-10 rounded-lg bg-teal-50 flex items-center justify-center">
                    <x-lucide-file-down class="w-5 h-5 text-teal-600" />
                </div>

            </div>

            <div class="mt-4 text-sm text-slate-500 font-medium">PDF Downloads</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">
                {{ number_format($pdfDownloads ?? 0) }}
            </div>
        </div> --}}

        <!-- Active Users -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="h-10 w-10 rounded-lg bg-teal-50 flex items-center justify-center">
                    <x-lucide-bar-chart-3 class="w-5 h-5 text-teal-600" />
                </div>

                {{-- <span
                class="inline-flex items-center gap-1 rounded-full bg-green-100 text-green-700 px-2 py-1 text-xs font-semibold">
                ↗ +5%
            </span> --}}
            </div>

            <div class="mt-4 text-sm text-slate-500 font-medium">Logged In Users</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">
                {{ number_format($loggedInUsers ?? 0) }}
            </div>
        </div>

        <!-- Blocked Users -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="h-10 w-10 rounded-lg bg-teal-50 flex items-center justify-center">
                    <x-lucide-user-x class="w-5 h-5 text-teal-600" />
                </div>

                {{-- <span
                class="inline-flex items-center gap-1 rounded-full bg-green-100 text-green-700 px-2 py-1 text-xs font-semibold">
                ↗ +5%
            </span> --}}
            </div>

            <div class="mt-4 text-sm text-slate-500 font-medium">Blocked Users</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">
                {{ number_format($blockedUsers ?? 0) }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        <div class="col-span-3 bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="text-xl font-extrabold text-slate-900">User Signups</div>
                    <div class="text-sm text-slate-500">Last 7 days</div>
                </div>
            </div>

            <div class="h-70">
                <canvas id="usersJoinedChart" data-labels='@json($userJoinLabels)'
                    data-data='@json($userJoinData)'></canvas>
                <script>
                    document.addEventListener('livewire:navigated', renderUsersJoinedChart);
                    document.addEventListener('DOMContentLoaded', renderUsersJoinedChart);

                    function renderUsersJoinedChart() {
                        const canvas = document.getElementById('usersJoinedChart');
                        if (!canvas) return;

                        if (canvas.__chart) {
                            canvas.__chart.destroy();
                        }

                        const ctx = canvas.getContext('2d');

                        const labels = JSON.parse(canvas.dataset.labels || "[]");
                        const data = JSON.parse(canvas.dataset.data || "[]");

                        const glowPlugin = {
                            id: 'glowPlugin',
                            beforeDatasetsDraw(chart) {
                                const {
                                    ctx
                                } = chart;
                                ctx.save();
                                ctx.shadowColor = "rgba(13, 148, 136, 0.5)";
                                ctx.shadowBlur = 14;

                            },
                            afterDatasetsDraw(chart) {
                                chart.ctx.restore();
                            }
                        };

                        const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);

                        // near the line (visible but not harsh)
                        gradient.addColorStop(0, "rgba(13, 148, 136, 0.22)");

                        // soft fade (this is the key change)
                        gradient.addColorStop(0.55, "rgba(13, 148, 136, 0.06)");

                        // fully transparent before x-axis
                        gradient.addColorStop(1, "rgba(13, 148, 136, 0)");

                        canvas.__chart = new Chart(ctx, {
                            type: 'line',
                            plugins: [glowPlugin],
                            data: {
                                labels: labels,
                                datasets: [{
                                    data: data,
                                    tension: 0.4,
                                    borderColor: '#0D9488',
                                    borderWidth: 4,
                                    backgroundColor: gradient,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 4,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: {
                                            display: false,
                                            drawBorder: false
                                        },
                                        ticks: {
                                            display: true,
                                            maxRotation: 0,
                                            autoSkip: true,
                                        },
                                        border: {
                                            display: false
                                        }
                                    },
                                    y: {
                                        grid: {
                                            display: false,
                                            drawBorder: false
                                        },
                                        ticks: {
                                            display: false
                                        },
                                        border: {
                                            display: false
                                        }
                                    }
                                }
                            }
                        });
                    }
                </script>

            </div>
        </div>

        <div class="col-span-2">

            <!-- Recent Users -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <div class="text-xl font-extrabold text-slate-900 mb-6">Recently Registered Users</div>

                <div class="space-y-4">
                    @foreach ($recentUsers as $user)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center font-semibold">
                                    {{ strtoupper(substr($user->fullName ?? $user->email, 0, 1)) }}
                                </div>

                                <div>
                                    <div class="font-medium text-slate-900">{{ $user->fullName ?? 'Unnamed' }}</div>
                                    <div class="text-sm text-slate-500">{{ $user->email }}</div>
                                </div>
                            </div>

                            <!-- Joined Time -->
                            <div class="text-sm text-slate-500 whitespace-nowrap">
                                {{ $user->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 text-center">
                    <a href="{{ route('admin.users.index') }}"
                        class="inline-block px-4 pt-2 text-teal-600 text-sm font-semibold rounded-md">
                        View All Users
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
