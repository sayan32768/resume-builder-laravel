<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Resume;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Component
{
    private function getTemplateUsageDistributionData(): array
    {
        $rows = Resume::query()
            ->select('resumeType')
            ->selectRaw('count(*)::int as total')
            ->whereNotNull('resumeType')
            ->groupBy('resumeType')
            ->orderByDesc('total')
            ->get();

        $labels = $rows->pluck('resumeType')->map(fn($t) => ucfirst(strtolower($t)))->toArray();
        $data = $rows->pluck('total')->map(fn($v) => (int) $v)->toArray();

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }


    private function getResumesCreatedLast7DaysChartData(): array
    {
        $tz = config('app.timezone', 'Asia/Kolkata');

        $start = Carbon::now($tz)->subDays(6)->startOfDay();
        $end   = Carbon::now($tz)->endOfDay();

        /**
         * ✅ Postgres explanation:
         * - created_at is stored in UTC
         * - we convert it into local timezone for grouping by day
         * - group by local date string
         */
        $rows = Resume::query()
            ->selectRaw("
            to_char((created_at AT TIME ZONE 'UTC') AT TIME ZONE ?, 'YYYY-MM-DD') as day,
            count(*)::int as total
        ", [$tz])
            ->whereBetween('created_at', [
                $start->copy()->timezone('UTC'),
                $end->copy()->timezone('UTC'),
            ])
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $map = $rows->pluck('total', 'day')->toArray();

        $labels = [];
        $data = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i);
            $key = $day->format('Y-m-d');

            $labels[] = Carbon::parse($key)->format('d M');
            $data[] = (int) ($map[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }


    private function getUsersSignupLast7DaysChartData(): array
    {
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

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }


    public function render()
    {
        $totalUsers = User::count();
        $totalResumes = Resume::count();
        $blockedUsers = User::where('is_blocked', true)->count();
        $loggedInUsers = User::where('isLoggedIn', true)->count();
        $recentUsers = User::latest()->limit(5)->get();


        $userSignupChart = $this->getUsersSignupLast7DaysChartData();
        $resumesCreatedChart = $this->getResumesCreatedLast7DaysChartData();
        $templateUsageChart  = $this->getTemplateUsageDistributionData();

        return view('livewire.admin.admin-dashboard', [
            'totalUsers' => $totalUsers,
            'totalResumes' => $totalResumes,
            'blockedUsers' => $blockedUsers,
            'loggedInUsers' => $loggedInUsers,
            'recentUsers' => $recentUsers,

            'userJoinLabels' => $userSignupChart['labels'],
            'userJoinData' => $userSignupChart['data'],

            'resumeCreateLabels' => $resumesCreatedChart['labels'],
            'resumeCreateData'   => $resumesCreatedChart['data'],

            'templateLabels'     => $templateUsageChart['labels'],
            'templateData'       => $templateUsageChart['data'],
        ]);
    }
}
