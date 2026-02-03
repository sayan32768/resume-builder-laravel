<?php

namespace App\Livewire\Admin;

use App\Models\Resume;
use App\Models\User;
use App\Models\UserSession;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AdminAnalytics extends Component
{
    /**
     * 📌 Helper: timezone used across the analytics page
     */
    private function tz(): string
    {
        return config('app.timezone', 'Asia/Kolkata');
    }

    /**
     * ✅ DAU chart (last 7 days)
     */
    private function getDauLast7DaysChartData(): array
    {
        $tz = $this->tz();

        $start = Carbon::now($tz)->subDays(6)->startOfDay();
        $end   = Carbon::now($tz)->endOfDay();

        $rows = UserSession::query()
            ->selectRaw("
                to_char((last_seen_at AT TIME ZONE 'UTC') AT TIME ZONE ?, 'YYYY-MM-DD') as day,
                count(distinct user_id)::int as total
            ", [$tz])
            ->whereNotNull('user_id')
            ->whereNotNull('last_seen_at')
            ->whereBetween('last_seen_at', [
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

            $labels[] = $day->format('D');
            $data[]   = (int) ($map[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * ✅ DAU stats (today/yesterday + avg)
     */
    private function getDauStats(): array
    {
        $tz = $this->tz();

        $todayStart = Carbon::now($tz)->startOfDay()->timezone('UTC');
        $todayEnd   = Carbon::now($tz)->endOfDay()->timezone('UTC');

        $yStart = Carbon::now($tz)->subDay()->startOfDay()->timezone('UTC');
        $yEnd   = Carbon::now($tz)->subDay()->endOfDay()->timezone('UTC');

        $dauToday = UserSession::query()
            ->whereNotNull('user_id')
            ->whereBetween('last_seen_at', [$todayStart, $todayEnd])
            ->distinct('user_id')
            ->count('user_id');

        $dauYesterday = UserSession::query()
            ->whereNotNull('user_id')
            ->whereBetween('last_seen_at', [$yStart, $yEnd])
            ->distinct('user_id')
            ->count('user_id');

        $chart = $this->getDauLast7DaysChartData();
        $avg7 = count($chart['data']) ? (int) round(array_sum($chart['data']) / count($chart['data'])) : 0;

        return [
            'today' => (int) $dauToday,
            'yesterday' => (int) $dauYesterday,
            'avg7' => (int) $avg7,
        ];
    }

    /**
     * ✅ WAU (last 7 days distinct active users)
     */
    private function getWau(): int
    {
        $tz = $this->tz();
        $start = Carbon::now($tz)->subDays(6)->startOfDay()->timezone('UTC');

        return (int) UserSession::query()
            ->whereNotNull('user_id')
            ->where('last_seen_at', '>=', $start)
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * ✅ MAU (last 30 days distinct active users)
     */
    private function getMau(): int
    {
        $tz = $this->tz();
        $start = Carbon::now($tz)->subDays(29)->startOfDay()->timezone('UTC');

        return (int) UserSession::query()
            ->whereNotNull('user_id')
            ->where('last_seen_at', '>=', $start)
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * ✅ Resumes created (last 7 days)
     */
    private function getResumesCreatedLast7DaysChartData(): array
    {
        $tz = $this->tz();

        $start = Carbon::now($tz)->subDays(6)->startOfDay();
        $end   = Carbon::now($tz)->endOfDay();

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

            $labels[] = $day->format('D');
            $data[]   = (int) ($map[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * ✅ Template usage distribution (all time)
     */
    private function getTemplateUsageDistributionData(): array
    {
        $rows = Resume::query()
            ->select('resumeType')
            ->selectRaw('count(*)::int as total')
            ->whereNotNull('resumeType')
            ->groupBy('resumeType')
            ->orderByDesc('total')
            ->get();

        $labels = $rows->pluck('resumeType')
            ->map(fn($t) => ucfirst(strtolower((string) $t)))
            ->toArray();

        $data = $rows->pluck('total')
            ->map(fn($v) => (int) $v)
            ->toArray();

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    // ---------------------------------------------------------------------
    // ✅ RETENTION SECTION
    // ---------------------------------------------------------------------

    /**
     * ✅ 7-day retention summary:
     * Users who signed up 7 days ago and came back today (or in their D+7 window).
     */
    private function getRetention7dSummary(): array
    {
        $tz = $this->tz();

        // Cohort = users who joined exactly 7 days ago (local day)
        $cohortDay = Carbon::now($tz)->subDays(7);
        $cohortStartUtc = $cohortDay->copy()->startOfDay()->timezone('UTC');
        $cohortEndUtc   = $cohortDay->copy()->endOfDay()->timezone('UTC');

        // Retention day = today (local day)
        $retentionDay = Carbon::now($tz);
        $retStartUtc = $retentionDay->copy()->startOfDay()->timezone('UTC');
        $retEndUtc   = $retentionDay->copy()->endOfDay()->timezone('UTC');

        // cohort size
        $cohortUserIds = User::query()
            ->whereBetween('created_at', [$cohortStartUtc, $cohortEndUtc])
            ->pluck('id');

        $cohortSize = $cohortUserIds->count();

        if ($cohortSize === 0) {
            return [
                'percent' => 0.0,
                'retained' => 0,
                'cohortSize' => 0,
            ];
        }

        // retained users = had session activity today
        $retained = UserSession::query()
            ->whereIn('user_id', $cohortUserIds)
            ->whereBetween('last_seen_at', [$retStartUtc, $retEndUtc])
            ->distinct('user_id')
            ->count('user_id');

        $percent = ($cohortSize > 0)
            ? round(($retained / $cohortSize) * 100, 1)
            : 0.0;

        return [
            'percent' => $percent,
            'retained' => (int) $retained,
            'cohortSize' => (int) $cohortSize,
        ];
    }

    /**
     * ✅ Cohorts table for last N weeks
     * Each cohort = users joined within that week
     * Retained = those cohort users active in week+1 (7 days after week start)
     */
    private function getRetentionCohorts(int $weeks = 4): array
    {
        $tz = $this->tz();

        $now = Carbon::now($tz);
        $startWeek = $now->copy()->startOfWeek(Carbon::MONDAY)->subWeeks($weeks - 1);

        $cohorts = [];

        for ($i = 0; $i < $weeks; $i++) {
            $weekStart = $startWeek->copy()->addWeeks($i);
            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

            $cohortStartUtc = $weekStart->copy()->startOfDay()->timezone('UTC');
            $cohortEndUtc   = $weekEnd->copy()->endOfDay()->timezone('UTC');

            // retention window: exactly +7 days week range
            $retWeekStart = $weekStart->copy()->addDays(7);
            $retWeekEnd   = $weekEnd->copy()->addDays(7);

            $retStartUtc = $retWeekStart->copy()->startOfDay()->timezone('UTC');
            $retEndUtc   = $retWeekEnd->copy()->endOfDay()->timezone('UTC');

            $userIds = User::query()
                ->whereBetween('created_at', [$cohortStartUtc, $cohortEndUtc])
                ->pluck('id');

            $joined = $userIds->count();

            if ($joined === 0) {
                $cohorts[] = [
                    'cohort_label' => $weekStart->format('M d') . ' - ' . $weekEnd->format('M d'),
                    'joined' => 0,
                    'retained' => 0,
                    'retention' => 0.0,
                ];
                continue;
            }

            $retained = UserSession::query()
                ->whereIn('user_id', $userIds)
                ->whereBetween('last_seen_at', [$retStartUtc, $retEndUtc])
                ->distinct('user_id')
                ->count('user_id');

            $retention = round(($retained / $joined) * 100, 1);

            $cohorts[] = [
                'cohort_label' => $weekStart->format('M d') . ' - ' . $weekEnd->format('M d'),
                'joined' => (int) $joined,
                'retained' => (int) $retained,
                'retention' => (float) $retention,
            ];
        }

        // show latest cohort first
        return array_reverse($cohorts);
    }

    /**
     * ✅ Retention trend (last N weeks):
     * For each week cohort, calculate 7d retention %
     */
    private function getRetentionTrendLastWeeks(int $weeks = 4): array
    {
        $cohorts = $this->getRetentionCohorts($weeks);

        $labels = [];
        $data = [];

        foreach ($cohorts as $row) {
            $labels[] = $row['cohort_label'];
            $data[] = (float) $row['retention'];
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function render()
    {
        // DAU
        $dauChart = $this->getDauLast7DaysChartData();
        $dauStats = $this->getDauStats();

        // WAU / MAU
        $wau = $this->getWau();
        $mau = $this->getMau();

        // Resumes created
        $resumesCreatedChart = $this->getResumesCreatedLast7DaysChartData();

        // Template usage
        $templateUsageChart = $this->getTemplateUsageDistributionData();

        // ✅ retention
        $retSummary = $this->getRetention7dSummary();
        $retCohorts = $this->getRetentionCohorts(4);
        $retTrend = $this->getRetentionTrendLastWeeks(4);

        return view('livewire.admin.admin-analytics', [
            // ✅ DAU
            'dauLabels' => $dauChart['labels'],
            'dauData'   => $dauChart['data'],

            'dauToday'     => $dauStats['today'],
            'dauYesterday' => $dauStats['yesterday'],
            'dauAvg7'      => $dauStats['avg7'],

            // ✅ WAU/MAU
            'wau' => $wau,
            'mau' => $mau,

            // ✅ resumes created (moved from dashboard)
            'resumeCreateLabels' => $resumesCreatedChart['labels'],
            'resumeCreateData'   => $resumesCreatedChart['data'],

            // ✅ templates
            'templateLabels' => $templateUsageChart['labels'],
            'templateData'   => $templateUsageChart['data'],

            // ✅ retention summary
            'retention7dPercent' => $retSummary['percent'],
            'retention7dRetained' => $retSummary['retained'],
            'retention7dCohortSize' => $retSummary['cohortSize'],

            // ✅ retention trend last 4 weeks
            'retentionTrendLabels' => $retTrend['labels'],
            'retentionTrendData' => $retTrend['data'],

            // ✅ retention cohorts table
            'retentionCohorts' => $retCohorts,
        ]);
    }
}
