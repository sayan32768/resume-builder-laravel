<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Resume;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Component
{
    public function render()
    {
        $totalUsers = User::count();
        $totalResumes = Resume::count();

        // $blockedUsers = User::where('is_blocked', true)->count();
        $adminUsers = User::where('role', 'ADMIN')->count();

        $recentUsers = User::latest()->limit(5)->get();
        $recentResumes = Resume::latest()->with('user')->limit(5)->get();


        $startDate = now()->subDays(6)->startOfDay();
        $usersPerDay = User::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Fill missing dates with 0
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('d M');
            $data[] = $usersPerDay[$date] ?? 0;
        }

        return view('livewire.admin.admin-dashboard', [
            'totalUsers' => $totalUsers,
            'totalResumes' => $totalResumes,
            // 'blockedUsers' => $blockedUsers,
            'adminUsers' => $adminUsers,
            'recentUsers' => $recentUsers,
            'recentResumes' => $recentResumes,

            'userJoinLabels' => $labels,
            'userJoinData' => $data,
        ]);
    }
}
