<div class="space-y-6">

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total Users</div>
            <div class="text-2xl font-bold">{{ $totalUsers }}</div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total Resumes</div>
            <div class="text-2xl font-bold">{{ $totalResumes }}</div>
        </div>

        {{-- <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Blocked Users</div>
            <div class="text-2xl font-bold">{{ $blockedUsers }}</div>
        </div> --}}

        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Admins</div>
            <div class="text-2xl font-bold">{{ $adminUsers }}</div>
        </div>
    </div>

    <!-- Recent activity -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="font-semibold mb-3">Recent Users</div>

            <div class="space-y-2">
                @foreach ($recentUsers as $user)
                    <div class="flex justify-between border-b pb-2">
                        <div>
                            <div class="font-medium">{{ $user->name ?? 'Unnamed' }}</div>
                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $user->created_at->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Resumes -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="font-semibold mb-3">Recent Resumes</div>

            <div class="space-y-2">
                @foreach ($recentResumes as $resume)
                    <div class="flex justify-between border-b pb-2">
                        <div>
                            <div class="font-medium">
                                {{ $resume->title ?? 'Untitled Resume' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                by {{ $resume->user->email ?? 'Unknown' }}
                            </div>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $resume->created_at->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
