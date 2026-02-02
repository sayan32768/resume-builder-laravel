<div class="space-y-6">

    <!-- Header -->
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

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @if ($user?->role !== 'ADMIN')
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
                <div class="text-sm text-slate-500">Total Resumes</div>
                <div class="text-2xl font-bold text-slate-900">{{ $totalResumes }}</div>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="text-sm text-slate-500">Role</div>
            <div class="text-2xl font-bold text-slate-900">{{ $user->role ?? 'USER' }}</div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="text-sm text-slate-500">Joined</div>
            <div class="text-lg font-semibold text-slate-900">{{ $user->created_at->format('d M Y') }}</div>
        </div>
    </div>

    <!-- User info + status -->
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

    <!-- Sessions -->
    @if ($user?->role !== 'ADMIN')
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200">
                <div class="text-lg font-bold text-slate-900">Login Sessions</div>
                {{-- <div class="text-sm text-slate-500">Devices / IPs recently used by this user</div> --}}
                <div class="text-sm text-slate-500">Devices</div>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        {{-- <th class="px-4 py-3 text-left">IP Address</th> --}}
                        <th class="px-4 py-3 text-left">User Agent</th>
                        <th class="px-4 py-3 text-left">Last Seen</th>
                    </tr>
                </thead>

                <tbody class="border-t border-slate-200">
                    @forelse ($sessions as $session)
                        <tr class="hover:bg-slate-50">
                            {{-- <td class="px-4 py-3 text-slate-600">
                            {{ $session->ip_address ?? '-' }}
                        </td> --}}
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
                            <td colspan="3" class="px-4 py-10 text-center text-slate-500">
                                No active sessions found for this user.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        <!-- Resume list -->
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
                            <td class="px-4 py-3 text-slate-600">
                                {{ $resume->created_at->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.resumes.show', $resume->id) }}"
                                        class="p-2 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                                        title="Show Resume Data">
                                        <x-lucide-eye class="w-4 h-4" />
                                    </a>

                                    <a href="{{ route('admin.resumes.preview', $resume->id) }}"
                                        class="p-2 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                                        title="Preview Resume">
                                        <x-lucide-file-text class="w-4 h-4" />
                                    </a>
                                    <button wire:click="deleteResume('{{ $resume->id }}')"
                                        wire:confirm="Delete this resume?"
                                        class="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold rounded-lg
           text-slate-400 hover:text-red-700 hover:bg-red-50 transition"
                                        title="Delete Resume">
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                        {{-- Delete --}}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                No resumes found for this user.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Pagination -->
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $resumes->links() }}
            </div>
        </div>
    @endif

</div>
