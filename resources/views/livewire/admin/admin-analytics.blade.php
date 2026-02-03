<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Analytics</h1>
            <p class="text-sm text-slate-500">
                Engagement metrics and platform usage trends.
            </p>
        </div>

        {{-- <div class="flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm rounded-md border border-slate-200
                       bg-white text-slate-700 hover:bg-slate-50 transition">
                <x-lucide-arrow-left class="w-4 h-4" />
                <span>Back to Dashboard</span>
            </a>
        </div> --}}
    </div>

    <!-- Top Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">

        <!-- DAU Today -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="h-10 w-10 rounded-lg bg-teal-50 flex items-center justify-center">
                    <x-lucide-activity class="w-5 h-5 text-teal-600" />
                </div>
            </div>
            <div class="mt-4 text-sm text-slate-500 font-medium">DAU (Today)</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($dauToday ?? 0) }}</div>
        </div>

        <!-- DAU Yesterday -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="h-10 w-10 rounded-lg bg-teal-50 flex items-center justify-center">
                    <x-lucide-calendar class="w-5 h-5 text-teal-600" />
                </div>
            </div>
            <div class="mt-4 text-sm text-slate-500 font-medium">DAU (Yesterday)</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($dauYesterday ?? 0) }}</div>
        </div>

        <!-- Avg DAU 7D -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="h-10 w-10 rounded-lg bg-teal-50 flex items-center justify-center">
                    <x-lucide-trending-up class="w-5 h-5 text-teal-600" />
                </div>
            </div>
            <div class="mt-4 text-sm text-slate-500 font-medium">Avg DAU (7d)</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($dauAvg7 ?? 0) }}</div>
        </div>

        <!-- WAU -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="h-10 w-10 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <x-lucide-bar-chart-3 class="w-5 h-5 text-indigo-600" />
                </div>
            </div>
            <div class="mt-4 text-sm text-slate-500 font-medium">WAU (7d)</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($wau ?? 0) }}</div>
        </div>

        <!-- MAU -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="h-10 w-10 rounded-lg bg-purple-50 flex items-center justify-center">
                    <x-lucide-line-chart class="w-5 h-5 text-purple-600" />
                </div>
            </div>
            <div class="mt-4 text-sm text-slate-500 font-medium">MAU (30d)</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($mau ?? 0) }}</div>
        </div>

        <!-- 7-day retention summary card -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="h-10 w-10 rounded-lg bg-amber-50 flex items-center justify-center">
                    <x-lucide-repeat-2 class="w-5 h-5 text-amber-600" />
                </div>
            </div>

            <div class="mt-4 text-sm text-slate-500 font-medium">7-Day Retention</div>

            <div class="mt-1 text-2xl font-bold text-slate-900">
                {{ number_format($retention7dPercent ?? 0, 1) }}%
            </div>

            <div class="mt-2 text-xs text-slate-500">
                {{ number_format($retention7dRetained ?? 0) }}
                /
                {{ number_format($retention7dCohortSize ?? 0) }}
                retained
            </div>
        </div>
    </div>


    <!-- Row 1 Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

        <!-- DAU Chart -->
        <div class="col-span-3 bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="text-xl font-extrabold text-slate-900">Daily Active Users</div>
                    <div class="text-sm text-slate-500">Last 7 days</div>
                </div>
            </div>

            <div class="h-72">
                <canvas id="dauChart" data-labels='@json($dauLabels)'
                    data-data='@json($dauData)'>
                </canvas>
            </div>
        </div>

        <!-- Template Usage -->
        <div class="col-span-2 bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xl font-extrabold text-slate-900 mb-2">Template Usage</div>
            <div class="text-sm text-slate-500 mb-5">All time distribution</div>

            <div class="h-72">
                <canvas id="templateUsageChart" data-labels='@json($templateLabels)'
                    data-data='@json($templateData)'>
                </canvas>
            </div>
        </div>
    </div>


    <!-- Row 2 Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

        <!-- Resumes Created -->
        <div class="col-span-3 bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="text-xl font-extrabold text-slate-900">Resumes Created</div>
                    <div class="text-sm text-slate-500">Last 7 days</div>
                </div>
            </div>

            <div class="h-72">
                <canvas id="resumesCreatedChart" data-labels='@json($resumeCreateLabels)'
                    data-data='@json($resumeCreateData)'>
                </canvas>
            </div>
        </div>

        <!-- Retention Trend -->
        <div class="col-span-2 bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xl font-extrabold text-slate-900 mb-2">7-Day Retention Trend</div>
            <div class="text-sm text-slate-500 mb-5">Last 4 weeks</div>

            <div class="h-72">
                <canvas id="retentionTrendChart" data-labels='@json($retentionTrendLabels)'
                    data-data='@json($retentionTrendData)'>
                </canvas>
            </div>
        </div>
    </div>


    <!-- Cohorts Table -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <div class="text-xl font-extrabold text-slate-900">Retention Cohorts</div>
                <div class="text-sm text-slate-500">Signup cohorts and 7-day return rate</div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Cohort</th>
                        <th class="px-4 py-3 text-left">Users Joined</th>
                        <th class="px-4 py-3 text-left">Retained (D+7)</th>
                        <th class="px-4 py-3 text-left">Retention %</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($retentionCohorts ?? [] as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">
                                {{ $row['cohort_label'] }}
                            </td>

                            <td class="px-4 py-3 text-slate-700">
                                {{ number_format($row['joined'] ?? 0) }}
                            </td>

                            <td class="px-4 py-3 text-slate-700">
                                {{ number_format($row['retained'] ?? 0) }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700 font-semibold">
                                    {{ number_format($row['retention'] ?? 0, 1) }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-slate-500">
                                No cohort data available.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <!-- ChartJS Rendering -->
    <script>
        document.addEventListener('DOMContentLoaded', renderAllAnalyticsCharts);
        document.addEventListener('livewire:navigated', renderAllAnalyticsCharts);

        function renderAllAnalyticsCharts() {
            renderDauChart();
            renderTemplateUsageChart();
            renderResumesCreatedChart();
            renderRetentionTrendChart();
        }

        function renderDauChart() {
            const canvas = document.getElementById('dauChart');
            if (!canvas) return;

            if (canvas.__chart) canvas.__chart.destroy();
            const ctx = canvas.getContext('2d');

            const labels = JSON.parse(canvas.dataset.labels || "[]");
            const data = JSON.parse(canvas.dataset.data || "[]");

            const glowPlugin = {
                id: 'glowPluginDau',
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
            gradient.addColorStop(0, "rgba(13, 148, 136, 0.22)");
            gradient.addColorStop(0.55, "rgba(13, 148, 136, 0.06)");
            gradient.addColorStop(1, "rgba(13, 148, 136, 0)");

            canvas.__chart = new Chart(ctx, {
                type: 'line',
                plugins: [glowPlugin],
                data: {
                    labels,
                    datasets: [{
                        data,
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
                                display: false
                            },
                            border: {
                                display: false
                            }
                        },
                        y: {
                            grid: {
                                display: false
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

        function renderResumesCreatedChart() {
            const canvas = document.getElementById('resumesCreatedChart');
            if (!canvas) return;

            if (canvas.__chart) canvas.__chart.destroy();
            const ctx = canvas.getContext('2d');

            const labels = JSON.parse(canvas.dataset.labels || "[]");
            const data = JSON.parse(canvas.dataset.data || "[]");

            const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
            gradient.addColorStop(0, "rgba(99, 102, 241, 0.20)");
            gradient.addColorStop(0.60, "rgba(99, 102, 241, 0.06)");
            gradient.addColorStop(1, "rgba(99, 102, 241, 0)");

            canvas.__chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        data,
                        tension: 0.4,
                        borderColor: '#6366F1',
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
                                display: false
                            },
                            border: {
                                display: false
                            }
                        },
                        y: {
                            grid: {
                                display: false
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

        function renderTemplateUsageChart() {
            const canvas = document.getElementById('templateUsageChart');
            if (!canvas) return;

            if (canvas.__chart) canvas.__chart.destroy();
            const ctx = canvas.getContext('2d');

            const labels = JSON.parse(canvas.dataset.labels || "[]");
            const data = JSON.parse(canvas.dataset.data || "[]");

            canvas.__chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data,
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        }
                    },
                    cutout: '72%',
                }
            });
        }

        function renderRetentionTrendChart() {
            const canvas = document.getElementById('retentionTrendChart');
            if (!canvas) return;

            if (canvas.__chart) canvas.__chart.destroy();
            const ctx = canvas.getContext('2d');

            const labels = JSON.parse(canvas.dataset.labels || "[]");
            const data = JSON.parse(canvas.dataset.data || "[]");

            canvas.__chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        data,
                        borderRadius: 10
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
                                display: false
                            },
                            border: {
                                display: false
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            border: {
                                display: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>

</div>
