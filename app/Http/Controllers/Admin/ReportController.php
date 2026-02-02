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
        $fileName = 'admin-report-' . now()->timezone('Asia/Kolkata')->format('Y-m-d_H-i') . '.csv';


        return response()->streamDownload(function () {

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
            fputcsv($out, ['Generated at', now()->toDateTimeString()]);
            fputcsv($out, []);

            $totalUsers = User::count();
            $blockedUsers = User::where('is_blocked', true)->count();
            $totalResumes = Resume::count();

            fputcsv($out, ['Summary']);
            fputcsv($out, ['Total Users', $totalUsers]);
            fputcsv($out, ['Blocked Users', $blockedUsers]);
            fputcsv($out, ['Total Resumes', $totalResumes]);
            fputcsv($out, []);

            // ✅ Users
            fputcsv($out, ['Users']);
            fputcsv($out, ['User ID', 'Full Name', 'Email', 'Role', 'Blocked', 'Joined']);

            User::query()
                ->select(['id', 'fullName', 'email', 'role', 'is_blocked', 'created_at'])
                ->orderByDesc('created_at')
                ->chunkById(500, function ($users) use ($out) {
                    foreach ($users as $user) {
                        fputcsv($out, [
                            $user->id,
                            $user->fullName ?? '',
                            $user->email ?? '',
                            $user->role ?? '',
                            $user->is_blocked ? 'YES' : 'NO',
                            optional($user->created_at)->toDateTimeString(),
                        ]);
                    }
                });

            fputcsv($out, []);

            // ✅ Resumes
            fputcsv($out, ['Resumes']);
            fputcsv($out, ['Resume ID', 'User ID', 'User Email', 'Title', 'Template', 'Draft', 'Created']);

            Resume::query()
                ->with('user:id,email')
                ->select(['id', 'userId', 'resumeTitle', 'resumeType', 'isDraft', 'created_at'])
                ->orderByDesc('created_at')
                ->chunkById(500, function ($resumes) use ($out) {
                    foreach ($resumes as $resume) {
                        fputcsv($out, [
                            $resume->id,
                            $resume->user_id,
                            $resume->user?->email ?? '',
                            $resume->resumeTitle ?? '',
                            $resume->resumeType ?? '',
                            $resume->isDraft ? 'YES' : 'NO',
                            optional($resume->created_at)->toDateTimeString(),
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
}
