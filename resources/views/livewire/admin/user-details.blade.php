<div class="space-y-6">

    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $user->fullName ?? 'Unnamed User' }}</h1>
            <p class="text-sm text-slate-500">{{ $user->email }}</p>
        </div>

        <a href="{{ route('admin.users.index') }}"
            class="px-4 py-2 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm">
            Back to Users
        </a>
    </div>

    <div class="mt-6 border-t border-slate-200 pt-5 space-y-4 mx-2">

        <div class="flex items-center justify-between">
            <div class="text-sm font-semibold text-slate-800">Password</div>

            <button wire:click="$toggle('showPasswordForm')" class="text-sm font-medium text-slate-700 hover:underline">
                {{ $showPasswordForm ? 'Cancel' : 'Change Password' }}
            </button>
        </div>

        @if ($showPasswordForm)
            <div class="space-y-3">

                <input type="password" wire:model="newPassword" placeholder="New password"
                    class="w-full rounded-md border border-slate-300 bg-white px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-[#183D3D]/30" />
                @error('newPassword')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror

                <input type="password" wire:model="confirmNewPassword" placeholder="Confirm new password"
                    class="w-full rounded-md border border-slate-300 bg-white px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-[#183D3D]/30" />
                @error('confirmNewPassword')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="flex justify-end">
                    <button wire:click="changePassword" wire:loading.attr="disabled" wire:target="changePassword"
                        class="rounded-md bg-[#183D3D] px-4 py-2 text-sm font-medium text-white
                           transition hover:bg-[#145252] disabled:opacity-60">

                        <span wire:loading.remove wire:target="changePassword">
                            Update Password
                        </span>

                        <span wire:loading wire:target="changePassword">
                            Updating...
                        </span>
                    </button>
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="text-sm text-slate-500">Total Resumes</div>
            <div class="text-2xl font-bold text-slate-900">{{ $totalResumes }}</div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="text-sm text-slate-500">Role</div>
            <div class="text-2xl font-bold text-slate-900">{{ $user->role ?? 'USER' }}</div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="text-sm text-slate-500">Joined</div>
            <div class="text-lg font-semibold text-slate-900">{{ $user->created_at->format('d M Y') }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        <div class="col-span-3 bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="text-lg font-bold text-slate-900">Resume Analytics</div>
                    <div class="text-sm text-slate-500">
                        Completion threshold: {{ $resumeStats['threshold'] ?? 60 }}%
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs text-slate-500 font-medium">Draft Resumes</div>
                    <div class="mt-1 text-xl font-bold text-slate-900">{{ $resumeStats['drafts'] ?? 0 }}</div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs text-slate-500 font-medium">Completed Resumes</div>
                    <div class="mt-1 text-xl font-bold text-slate-900">{{ $resumeStats['completed'] ?? 0 }}</div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs text-slate-500 font-medium">Top Template</div>
                    <div class="mt-1 text-xl font-bold text-slate-900">
                        {{ $resumeStats['topTemplate'] ?? '-' }}
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs text-slate-500 font-medium">Avg Completion</div>
                    <div class="mt-1 text-xl font-bold text-slate-900">{{ $resumeStats['avgCompletion'] ?? 0 }}%</div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs text-slate-500 font-medium">Best Completion</div>
                    <div class="mt-1 text-xl font-bold text-slate-900">{{ $resumeStats['bestCompletion'] ?? 0 }}%</div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs text-slate-500 font-medium">Total Resumes (analytics)</div>
                    <div class="mt-1 text-xl font-bold text-slate-900">{{ $resumeStats['total'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-span-2 bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-lg font-bold text-slate-900 mb-2">Completion Distribution</div>
            <div class="text-sm text-slate-500 mb-5">Resume completion score buckets</div>

            <div class="h-72">
                <canvas id="completionDistChart" data-labels='@json($completionLabels ?? [])'
                    data-data='@json($completionData ?? [])'>
                </canvas>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', renderUserAnalyticsCharts);
        document.addEventListener('livewire:navigated', renderUserAnalyticsCharts);

        function renderUserAnalyticsCharts() {
            renderCompletionDistChart();
        }

        function renderCompletionDistChart() {
            const canvas = document.getElementById('completionDistChart');
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
                                display: false
                            }
                        }
                    }
                }
            });
        }
    </script>

    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="text-lg font-bold text-slate-900">User Info</div>

            <span
                class="px-2 py-1 rounded-full text-xs font-semibold
                {{ $user->is_blocked ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                {{ $user->is_blocked ? 'Blocked' : 'Active' }}
            </span>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-slate-500">User ID</div>
                <div class="font-medium text-slate-900 break-all">{{ $user->id }}</div>
            </div>

            <div>
                <div class="text-slate-500">Email Verified</div>
                <div class="font-medium text-slate-900">
                    {{ $user->isVerified ? 'Yes' : 'No' }}
                </div>
            </div>
        </div>
    </div>

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

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200">
            <div class="text-lg font-bold text-slate-900">Login Sessions</div>
            <div class="text-sm text-slate-500">Devices</div>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">User Agent</th>
                    <th class="px-4 py-3 text-left">Last Seen</th>
                </tr>
            </thead>

            <tbody class="border-t border-slate-200">
                @forelse ($sessions as $session)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-600">
                            {{ $session->browser ?? '-' }} on {{ $session->platform ?? '-' }}
                            {{ $session->device ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $session->last_seen_at ? $session->last_seen_at->diffForHumans() : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-10 text-center text-slate-500">
                            No active sessions found for this user.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200">
            <div class="text-lg font-bold text-slate-900">Resumes</div>
            <div class="text-sm text-slate-500">Recent resumes created by this user</div>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Title</th>
                    <th class="px-4 py-3 text-left">Template</th>
                    <th class="px-4 py-3 text-left">Draft</th>
                    <th class="px-4 py-3 text-left">Completion</th>
                    <th class="px-4 py-3 text-left">Created</th>
                    <th class="px-4 py-3 text-right pr-6">Actions</th>
                </tr>
            </thead>

            <tbody class="border-t border-slate-200">
                @forelse($resumes as $resume)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900">
                            {{ blank($resume->resumeTitle) ? 'Untitled Resume' : $resume->resumeTitle }}
                        </td>

                        <td class="px-4 py-3 text-slate-600">
                            {{ $resume->resumeType ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-slate-600">
                            {{ $resume->isDraft ? 'Yes' : 'No' }}
                        </td>

                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-1 text-xs rounded-full font-semibold
                                {{ ($resume->completion ?? 0) >= ($resumeStats['threshold'] ?? 60)
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-slate-100 text-slate-700' }}">
                                {{ $resume->completion ?? 0 }}%
                            </span>
                        </td>

                        <td class="px-4 py-3 text-slate-600">
                            {{ $resume->created_at->diffForHumans() }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-2">
                                {{-- <a href="{{ route('admin.resumes.show', $resume->id) }}"
                                    class="p-2 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                                    title="Show Resume Data">
                                    <x-lucide-eye class="w-4 h-4" />
                                </a> --}}

                                <button wire:click="viewResume('{{ $resume->id }}')" wire:loading.attr="disabled"
                                    class="p-2 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                                    title="View resume">
                                    <!-- spinner -->
                                    <svg wire:loading wire:target="viewResume('{{ $resume->id }}')"
                                        class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                    <x-lucide-eye wire:loading.remove wire:target="viewResume('{{ $resume->id }}')"
                                        class="w-4 h-4" />
                                </button>

                                {{-- <a href="{{ route('admin.resumes.preview', $resume->id) }}"
                                    class="p-2 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                                    title="Preview Resume">
                                    <x-lucide-file-text class="w-4 h-4" />
                                </a> --}}

                                <button wire:click="deleteResume('{{ $resume->id }}')"
                                    wire:confirm="Delete this resume?"
                                    class="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold rounded-lg
                                           text-slate-400 hover:text-red-700 hover:bg-red-50 transition"
                                    title="Delete Resume">
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                            No resumes found for this user.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $resumes->links() }}
        </div>
    </div>

</div>
