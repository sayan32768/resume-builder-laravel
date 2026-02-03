<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function exportCsv(): StreamedResponse
    {
        $tz = 'Asia/Kolkata';

        $fileName = 'admin-report-' . now()->timezone($tz)->format('Y-m-d_H-i') . '.csv';

        return response()->streamDownload(function () use ($tz) {

            // ✅ stabilize long streaming
            ignore_user_abort(true);
            set_time_limit(0);

            // ✅ clear output buffers to prevent invalid response
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $out = fopen('php://output', 'w');

            // ✅ Summary
            fputcsv($out, ['ADMIN REPORT']);
            fputcsv($out, ['Generated at', now()->timezone($tz)->toDateTimeString()]);
            fputcsv($out, []);

            $totalUsers = User::count();
            $blockedUsers = User::where('is_blocked', true)->count();
            $totalResumes = Resume::count();

            fputcsv($out, ['Summary']);
            fputcsv($out, ['Total Users', $totalUsers]);
            fputcsv($out, ['Blocked Users', $blockedUsers]);
            fputcsv($out, ['Total Resumes', $totalResumes]);
            fputcsv($out, []);

            /**
             * ✅ Conversion Funnel
             *
             * Funnel Steps:
             * 1) Signed up
             * 2) Created at least 1 resume
             * 3) Completed resume (based on completion score threshold)
             */
            $completionThreshold = 60;

            $usersWithResume = Resume::query()
                ->whereNotNull('userId')
                ->distinct('userId')
                ->count('userId');

            // ✅ Determine users with at least one "completed" resume
            $usersWithCompletedResume = [];

            Resume::query()
                ->with([
                    'personalDetails',
                    'educationDetails',
                    'skills',
                    'professionalExperience',
                    'projects',
                    'certifications',
                ])
                ->select(['id', 'userId'])
                ->whereNotNull('userId')
                ->orderBy('id')
                ->chunkById(200, function ($resumes) use (&$usersWithCompletedResume, $completionThreshold) {
                    foreach ($resumes as $resume) {
                        $score = $this->calculateCompletion($resume);

                        if ($score >= $completionThreshold) {
                            $usersWithCompletedResume[(string) $resume->userId] = true;
                        }
                    }
                });

            $completedUsersCount = count($usersWithCompletedResume);

            // Funnel export block
            fputcsv($out, ['Conversion Funnel']);
            fputcsv($out, ['Completion threshold (%)', $completionThreshold]);
            fputcsv($out, []);

            fputcsv($out, ['Step', 'Users', 'Conversion %']);

            // Step 1
            fputcsv($out, [
                'Signed up',
                $totalUsers,
                '100%',
            ]);

            // Step 2
            $conv2 = $totalUsers > 0 ? round(($usersWithResume / $totalUsers) * 100, 2) : 0;
            fputcsv($out, [
                'Created >= 1 resume',
                $usersWithResume,
                $conv2 . '%',
            ]);

            // Step 3
            $conv3 = $totalUsers > 0 ? round(($completedUsersCount / $totalUsers) * 100, 2) : 0;
            fputcsv($out, [
                "Completed resume >= {$completionThreshold}%",
                $completedUsersCount,
                $conv3 . '%',
            ]);

            fputcsv($out, []);

            // ✅ Users
            fputcsv($out, ['Users']);
            fputcsv($out, ['User ID', 'Full Name', 'Email', 'Role', 'Blocked', 'Joined']);

            User::query()
                ->select(['id', 'fullName', 'email', 'role', 'is_blocked', 'created_at'])
                ->orderByDesc('created_at')
                ->chunkById(500, function ($users) use ($out, $tz) {
                    foreach ($users as $user) {
                        fputcsv($out, [
                            $user->id,
                            $user->fullName ?? '',
                            $user->email ?? '',
                            $user->role ?? '',
                            $user->is_blocked ? 'YES' : 'NO',
                            optional($user->created_at)->timezone($tz)->toDateTimeString(),
                        ]);
                    }
                });

            fputcsv($out, []);

            // ✅ Resumes
            fputcsv($out, ['Resumes']);
            fputcsv($out, ['Resume ID', 'User ID', 'User Email', 'Title', 'Template', 'Draft', 'Completion', 'Created']);

            Resume::query()
                ->with([
                    'user:id,email',
                    'personalDetails',
                    'educationDetails',
                    'skills',
                    'professionalExperience',
                    'projects',
                    'certifications',
                ])
                ->select(['id', 'userId', 'resumeTitle', 'resumeType', 'isDraft', 'created_at'])
                ->orderByDesc('created_at')
                ->chunkById(200, function ($resumes) use ($out, $tz) {
                    foreach ($resumes as $resume) {
                        $completion = $this->calculateCompletion($resume);

                        fputcsv($out, [
                            $resume->id,
                            $resume->userId,
                            $resume->user?->email ?? '',
                            $resume->resumeTitle ?? '',
                            $resume->resumeType ?? '',
                            $resume->isDraft ? 'YES' : 'NO',
                            $completion . '%',
                            optional($resume->created_at)->timezone($tz)->toDateTimeString(),
                        ]);
                    }
                });

            fclose($out);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * ✅ Completion score (0-100)
     */
    private function calculateCompletion($resume): int
    {
        $score = 0;

        if (
            $resume->personalDetails &&
            collect([
                $resume->personalDetails->fullName,
                $resume->personalDetails->email,
                $resume->personalDetails->phone,
                $resume->personalDetails->about,
            ])->filter()->isNotEmpty()
        ) {
            $score += 20;
        }

        if ($resume->educationDetails && $resume->educationDetails->isNotEmpty()) {
            $score += 15;
        }

        if (
            $resume->skills &&
            collect($resume->skills->toArray())
            ->except(['id', 'resume_id', 'created_at', 'updated_at'])
            ->filter()
            ->isNotEmpty()
        ) {
            $score += 15;
        }

        if ($resume->professionalExperience && $resume->professionalExperience->isNotEmpty()) {
            $score += 25;
        }

        if ($resume->projects && $resume->projects->isNotEmpty()) {
            $score += 15;
        }

        if ($resume->certifications && $resume->certifications->isNotEmpty()) {
            $score += 10;
        }

        return min($score, 100);
    }
}
